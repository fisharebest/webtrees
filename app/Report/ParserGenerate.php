<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Report;

use Closure;
use DomainException;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use function addcslashes;
use function addslashes;
use function array_pop;
use function array_shift;
use function count;
use function end;
use function explode;
use function file_exists;
use function getimagesize;
use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function in_array;
use function ltrim;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function round;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strip_tags;
use function strlen;
use function strpos;
use function substr;
use function trim;
use function uasort;

use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

class ParserGenerate extends AbstractParser
{
    private bool $process_footnote = true;

    // We only print character data for certain element types.
    // e.g. exclude whitespace in loops, conditionals, etc.
    private bool $print_data = false;

    /** @var array<int,bool> Push-down stack of $print_data */
    private array $print_data_stack = [];

    private int $process_gedcoms = 0;

    private int $process_ifs = 0;

    private int $process_repeats = 0;

    /** Approximate line number in the source XML where the active repeat block begins. */
    private int $repeat_line = 0;

    /** @var array<string> Repeated data when iterating over loops */
    private array $repeats = [];

    /**
     * Captured inner XML of the currently-open repeat block.  Set by the
     * start handler (via XMLReader::readInnerXml()) and consumed by the
     * matching end handler, which re-parses it once per iteration through
     * {@see AbstractParser::parseFragment()}.
     */
    private string $repeat_xml = '';

    /** @var array<RepeatFrame> Snapshots of the loop state captured when nesting <RepeatTag>, <Facts>, <List> or <Relatives>. */
    private array $repeats_stack = [];

    /** @var array<ElementContainerInterface> Stack of containers when nesting text boxes */
    private array $container_stack = [];


    private string $gedrec = '';

    /** @var array<GedcomFrame> Snapshots of the GEDCOM-record state captured when nesting <Gedcom>. */
    private array $gedrec_stack = [];

    private AbstractElement $current_element;

    private AbstractElement $footnote_element;

    private string $fact = '';

    private string $desc = '';

    private string $type = '';

    private int $generation = 1;

    /** @var array<GedcomRecord&object{generation:int}> Source data for processing lists */
    private array $list = [];

    /** Number of items in lists */
    private int $list_total = 0;

    /** Number of items filtered from lists */
    private int $list_private = 0;

    /** Report title, captured from the <Title> element */
    private string $report_title = '';

    /** Report description, captured from the <Description> element */
    private string $report_description = '';

    private AbstractRenderer $renderer;

    /** The current target for addElement() — either the renderer or a nested text box */
    private ElementContainerInterface $current_container;

    /** Variables defined in the report at run-time, seeded from the setup form. */
    private VariableTable $variables;

    private Tree $tree;

    /**
     * @param array<string,string> $vars Initial variable bindings from the report setup form.
     */
    public function __construct(string $report, AbstractRenderer $renderer, array $vars, Tree $tree)
    {
        $this->renderer          = $renderer;
        $this->current_container = $renderer;
        $this->current_element   = new NullElement();
        $this->variables         = new VariableTable($vars);
        $this->tree              = $tree;

        parent::__construct($report);
    }

    /**
     * get a gedcom subrecord
     *
     * searches a gedcom record and returns a subrecord of it. A subrecord is defined starting at a
     * line with level N and all subsequent lines greater than N until the next N level is reached.
     * For example, the following is a BIRT subrecord:
     * <code>1 BIRT
     * 2 DATE 1 JAN 1900
     * 2 PLAC Phoenix, Maricopa, Arizona</code>
     * The following example is the DATE subrecord of the above BIRT subrecord:
     * <code>2 DATE 1 JAN 1900</code>
     *
     * @param int    $level   the N level of the subrecord to get
     * @param string $tag     a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
     * @param string $gedrec  the parent gedcom record to search in
     * @param int    $num     this allows you to specify which matching <var>$tag</var> to get. Oftentimes a
     *                        gedcom record will have more that 1 of the same type of subrecord. An individual may have
     *                        multiple events for example. Passing $num=1 would get the first 1. Passing $num=2 would get the
     *                        second one, etc.
     */
    public static function getSubRecord(int $level, string $tag, string $gedrec, int $num = 1): string
    {
        if ($gedrec === '') {
            return '';
        }
        // -- adding \n before and after gedrec
        $gedrec       = "\n" . $gedrec . "\n";
        $tag          = trim($tag);
        $searchTarget = "~[\n]" . $tag . "[\s]~";
        $ct           = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        if ($ct === 0) {
            return '';
        }
        if ($ct < $num) {
            return '';
        }
        $pos1 = (int) $match[$num - 1][0][1];
        $pos2 = strpos($gedrec, "\n$level", $pos1 + 1);
        if (!$pos2) {
            $pos2 = strpos($gedrec, "\n1", $pos1 + 1);
        }
        if (!$pos2) {
            $pos2 = strpos($gedrec, "\nWT_", $pos1 + 1); // WT_SPOUSE, WT_FAMILY_ID ...
        }
        if (!$pos2) {
            return ltrim(substr($gedrec, $pos1));
        }
        $subrec = substr($gedrec, $pos1, $pos2 - $pos1);

        return ltrim($subrec);
    }

    /**
     * get CONT lines
     *
     * get the N+1 CONT or CONC lines of a gedcom subrecord
     *
     * @param int    $nlevel the level of the CONT lines to get
     * @param string $nrec   the gedcom subrecord to search in
     *
     * @return string a string with all CONT lines merged
     */
    public static function getCont(int $nlevel, string $nrec): string
    {
        $text = '';

        $subrecords = explode("\n", $nrec);
        foreach ($subrecords as $thisSubrecord) {
            if (substr($thisSubrecord, 0, 2) !== $nlevel . ' ') {
                continue;
            }
            $subrecordType = substr($thisSubrecord, 2, 4);
            if ($subrecordType === 'CONT') {
                $text .= "\n" . substr($thisSubrecord, 7);
            }
        }

        return $text;
    }

    /**
     * Dispatch table for opening XML tags.
     *
     * Every tag that may legally appear in a report must be present here,
     * even if it requires no action on open (use a no-op).
     *
     * @return array<string,Closure(array<string,string>):void>
     */
    protected function startHandlers(): array
    {
        return [
            'Body'             => $this->bodyStartHandler(...),
            'Cell'             => $this->cellStartHandler(...),
            'Description'      => $this->descriptionStartHandler(...),
            'Doc'              => $this->docStartHandler(...),
            'Facts'            => $this->factsStartHandler(...),
            'Footer'           => $this->footerStartHandler(...),
            'Footnote'         => $this->footnoteStartHandler(...),
            'FootnoteTexts'    => $this->footnoteTextsStartHandler(...),
            'Gedcom'           => $this->gedcomStartHandler(...),
            'GedcomValue'      => $this->gedcomValueStartHandler(...),
            'Generation'       => $this->generationStartHandler(...),
            'GetPersonName'    => $this->getPersonNameStartHandler(...),
            'Header'           => $this->headerStartHandler(...),
            'HighlightedImage' => $this->highlightedImageStartHandler(...),
            'Image'            => $this->imageStartHandler(...),
            'Input'            => $this->noop(...), // metadata consumed by ParserSetup
            'Line'             => $this->lineStartHandler(...),
            'List'             => $this->listStartHandler(...),
            'ListTotal'        => $this->listTotalStartHandler(...),
            'NewPage'          => $this->newPageStartHandler(...),
            'Now'              => $this->nowStartHandler(...),
            'PageNum'          => $this->pageNumStartHandler(...),
            'Relatives'        => $this->relativesStartHandler(...),
            'RepeatTag'        => $this->repeatTagStartHandler(...),
            'Report'           => $this->noop(...), // the root tag carries no logic
            'SetVar'           => $this->setVarStartHandler(...),
            'Style'            => $this->styleStartHandler(...),
            'Text'             => $this->textStartHandler(...),
            'TextBox'          => $this->textBoxStartHandler(...),
            'Title'            => $this->titleStartHandler(...),
            'TotalPages'       => $this->totalPagesStartHandler(...),
            'br'               => $this->brStartHandler(...),
            'if'               => $this->ifStartHandler(...),
            'tempdoc'          => $this->noop(...), // synthetic wrapper injected during iteration
            'var'              => $this->varStartHandler(...),
        ];
    }

    /**
     * @return array<string,Closure():void>
     */
    protected function endHandlers(): array
    {
        return [
            'Body'             => $this->noop(...),
            'Cell'             => $this->cellEndHandler(...),
            'Description'      => $this->descriptionEndHandler(...),
            'Doc'              => $this->docEndHandler(...),
            'Facts'            => $this->factsEndHandler(...),
            'Footer'           => $this->noop(...),
            'Footnote'         => $this->footnoteEndHandler(...),
            'FootnoteTexts'    => $this->noop(...),
            'Gedcom'           => $this->gedcomEndHandler(...),
            'GedcomValue'      => $this->noop(...),
            'Generation'       => $this->noop(...),
            'GetPersonName'    => $this->noop(...),
            'Header'           => $this->noop(...),
            'HighlightedImage' => $this->noop(...),
            'Image'            => $this->noop(...),
            'Input'            => $this->noop(...),
            'Line'             => $this->noop(...),
            'List'             => $this->listEndHandler(...),
            'ListTotal'        => $this->noop(...),
            'NewPage'          => $this->noop(...),
            'Now'              => $this->noop(...),
            'PageNum'          => $this->noop(...),
            'Relatives'        => $this->relativesEndHandler(...),
            'RepeatTag'        => $this->repeatTagEndHandler(...),
            'Report'           => $this->noop(...),
            'SetVar'           => $this->noop(...),
            'Style'            => $this->noop(...),
            'Text'             => $this->textEndHandler(...),
            'TextBox'          => $this->textBoxEndHandler(...),
            'Title'            => $this->titleEndHandler(...),
            'TotalPages'       => $this->noop(...),
            'br'               => $this->noop(...),
            'if'               => $this->ifEndHandler(...),
            'tempdoc'          => $this->noop(...),
            'var'              => $this->noop(...),
        ];
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function startElement(string $name, array $attrs): void
    {
        // Expand any $variable references in attribute values up front so
        // that individual handlers see fully resolved attributes.
        $newattrs = [];
        foreach ($attrs as $key => $value) {
            if (preg_match("/^\\$(\w+)$/", $value, $match) && $this->variables->has($match[1])) {
                $value = $this->variables->get($match[1]);
            }
            $newattrs[$key] = $value;
        }
        $attrs = $newattrs;

        // Gating: while we are skipping content inside <Footnote>, <if>,
        // <Gedcom>, <Facts> or <RepeatTag>, only the tag(s) that can end
        // (or re-enter) those scopes are dispatched.  Tags suppressed by a
        // gate are not validated against the handler table either.
        if (!$this->process_footnote) {
            return;
        }
        if ($this->process_ifs !== 0 && $name !== 'if') {
            return;
        }
        if ($this->process_gedcoms !== 0 && $name !== 'Gedcom') {
            return;
        }
        if ($this->process_repeats !== 0 && $name !== 'Facts' && $name !== 'RepeatTag') {
            return;
        }

        parent::startElement($name, $attrs);
    }

    protected function endElement(string $name): void
    {
        // Mirror image of the gating in startElement().  <Footnote>, <if>,
        // <Gedcom>, <Facts>, <RepeatTag>, <List> and <Relatives> can each
        // close the scope they opened, so they must be dispatched even when
        // the corresponding gate is active.
        if (!$this->process_footnote && $name !== 'Footnote') {
            return;
        }
        if ($this->process_ifs !== 0 && $name !== 'if') {
            return;
        }
        if ($this->process_gedcoms !== 0 && $name !== 'Gedcom') {
            return;
        }
        if ($this->process_repeats !== 0 && $name !== 'Facts' && $name !== 'RepeatTag' && $name !== 'List' && $name !== 'Relatives') {
            return;
        }

        parent::endElement($name);
    }

    protected function characterData(string $data): void
    {
        if ($this->print_data && $this->process_gedcoms === 0 && $this->process_ifs === 0 && $this->process_repeats === 0) {
            $this->current_element->addText($data);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function styleStartHandler(array $attrs): void
    {
        if (empty($attrs['name'])) {
            throw new DomainException('REPORT ERROR Style: The "name" of the style is missing or not set in the XML file.');
        }

        $style = new Style(
            name:  $attrs['name'],
            font:  $attrs['font'] ?? $this->renderer->default_font,
            size:  (float) ($attrs['size'] ?? $this->renderer->default_font_size),
            style: $attrs['style'] ?? '',
        );

        $this->renderer->addStyle($style);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function docStartHandler(array $attrs): void
    {
        // Default margins are in millimeters, converted to points (1 point = 1/72 inch).
        $mm_to_points = 72.0 / 25.4;

        $page_size = PageSize::tryFrom($attrs['pageSize'] ?? 'A4') ?? PageSize::A4;

        // Resolve page dimensions: use custom dimensions if specified, otherwise look up the paper size.
        $page_width  = (float) ($attrs['customwidth'] ?? 0.0);
        $page_height = (float) ($attrs['customheight'] ?? 0.0);

        if ($page_width === 0.0 || $page_height === 0.0) {
            $page_width  = $page_size->width();
            $page_height = $page_size->height();
        }

        $config = new ReportConfig(
            page_width:        $page_width,
            page_height:       $page_height,
            left_margin:       (float) ($attrs['leftmargin'] ?? 18.0 * $mm_to_points),
            right_margin:      (float) ($attrs['rightmargin'] ?? 9.9 * $mm_to_points),
            top_margin:        (float) ($attrs['topmargin'] ?? 26.8 * $mm_to_points),
            bottom_margin:     (float) ($attrs['bottommargin'] ?? 21.6 * $mm_to_points),
            header_margin:     (float) ($attrs['headermargin'] ?? 4.9 * $mm_to_points),
            footer_margin:     (float) ($attrs['footermargin'] ?? 9.9 * $mm_to_points),
            orientation:       PageOrientation::from($attrs['orientation'] ?? 'portrait'),
            page_size:         $page_size,
            show_generated_by: (bool) ($attrs['showGeneratedBy'] ?? true),
            rtl:               I18N::direction() === 'rtl',
            // I18N: This is a report footer. %s is the name of the application.
            generated_by:      I18N::translate('Generated by %s', Webtrees::NAME . ' ' . Webtrees::VERSION),
            author:            Webtrees::NAME . ' ' . Webtrees::VERSION,
            title:             $this->report_title,
            description:       $this->report_description,
            align_rtl:         I18N::direction() === 'rtl' ? 'right' : 'left',
            entity_rtl:        I18N::direction() === 'rtl' ? '&rlm;' : '&lrm;',
        );

        $this->renderer->setup($config);
    }

    protected function docEndHandler(): void
    {
        $this->renderer->run();
    }

    protected function headerStartHandler(): void
    {
        $this->renderer->setProcessing(ReportSection::Header);
    }

    protected function bodyStartHandler(): void
    {
        $this->renderer->setProcessing(ReportSection::Body);
    }

    protected function footerStartHandler(): void
    {
        $this->renderer->setProcessing(ReportSection::Footer);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function cellStartHandler(array $attrs): void
    {
        $bgcolor = $attrs['bgcolor'] ?? '';
        $bocolor = $attrs['bocolor'] ?? '';
        $border  = $attrs['border'] ?? '';
        $fill    = (bool) ($attrs['fill'] ?? false);
        $height  = (float) ($attrs['height'] ?? 0.0);
        $left    = (float) ($attrs['left'] ?? AbstractElement::CURRENT_POSITION);
        $ln      = CellNewline::tryFrom((int) ($attrs['newline'] ?? 0)) ?? CellNewline::Right;
        $reseth  = (bool) ($attrs['reseth'] ?? true);
        $stretch = (int) ($attrs['stretch'] ?? 0);
        $style   = $this->renderer->getStyle($attrs['style'] ?? '');
        $tcolor  = $attrs['tcolor'] ?? '';
        $top     = (float) ($attrs['top'] ?? AbstractElement::CURRENT_POSITION);
        $width   = (float) ($attrs['width'] ?? 0.0);
        $align   = $this->parseCellAlign($attrs['align'] ?? '');

        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = true;

        $this->current_element = $this->renderer->createCell(
            $width,
            $height,
            $border,
            $align,
            $bgcolor,
            $style,
            $ln,
            $top,
            $left,
            $fill,
            $stretch,
            $bocolor,
            $tcolor,
            $reseth
        );
    }

    private function parseCellAlign(string $value): CellAlign
    {
        return match ($value) {
            'center'   => CellAlign::Center,
            'justify'  => CellAlign::Justify,
            'left'     => CellAlign::Left,
            'leftrtl'  => $this->renderer->config->rtl ? CellAlign::Right : CellAlign::Left,
            'right'    => CellAlign::Right,
            'rightrtl' => $this->renderer->config->rtl ? CellAlign::Left : CellAlign::Right,
            default    => CellAlign::None,
        };
    }

    protected function cellEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);
        $this->current_container->addElement($this->current_element);
    }

    protected function nowStartHandler(): void
    {
        $this->current_element->addText(Registry::timestampFactory()->now()->isoFormat('LLLL'));
    }

    protected function pageNumStartHandler(): void
    {
        $this->current_element->addPageNumber();
    }

    protected function totalPagesStartHandler(): void
    {
        $this->current_element->addTotalPages();
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function gedcomStartHandler(array $attrs): void
    {
        if ($this->process_gedcoms > 0) {
            $this->process_gedcoms++;

            return;
        }

        $tag       = $attrs['id'];
        $tag       = str_replace('@fact', $this->fact, $tag);
        $tags      = explode(':', $tag);
        $newgedrec = '';
        if (count($tags) < 2) {
            $tmp       = Registry::gedcomRecordFactory()->make($attrs['id'], $this->tree);
            $newgedrec = $tmp ? $tmp->privatizeGedcom(Auth::accessLevel($this->tree)) : '';
        }
        if (empty($newgedrec)) {
            $tgedrec   = $this->gedrec;
            $newgedrec = '';
            foreach ($tags as $tag) {
                if (preg_match('/\$(.+)/', $tag, $match)) {
                    $tmp       = Registry::gedcomRecordFactory()->make($match[1], $this->tree);
                    $newgedrec = $tmp ? $tmp->privatizeGedcom(Auth::accessLevel($this->tree)) : '';
                } elseif (preg_match('/@(.+)/', $tag, $match)) {
                    $gmatch = [];
                    if (preg_match("/\d $match[1] @([^@]+)@/", $tgedrec, $gmatch)) {
                        $tmp       = Registry::gedcomRecordFactory()->make($gmatch[1], $this->tree);
                        $newgedrec = $tmp ? $tmp->privatizeGedcom(Auth::accessLevel($this->tree)) : '';
                        $tgedrec   = $newgedrec;
                    } else {
                        $newgedrec = '';
                        break;
                    }
                } else {
                    $level     = 1 + (int) explode(' ', trim($tgedrec))[0];
                    $newgedrec = self::getSubRecord($level, "$level $tag", $tgedrec);
                    $tgedrec   = $newgedrec;
                }
            }
        }
        if (!empty($newgedrec)) {
            $this->gedrec_stack[] = new GedcomFrame(
                gedrec: $this->gedrec,
                fact:   $this->fact,
                desc:   $this->desc,
            );
            $this->gedrec         = $newgedrec;
            if (preg_match("/(\d+) (_?[A-Z0-9]+) (.*)/", $this->gedrec, $match)) {
                $this->fact = $match[2];
                $this->desc = trim($match[3]);
            }
        } else {
            $this->process_gedcoms++;
        }
    }

    protected function gedcomEndHandler(): void
    {
        if ($this->process_gedcoms > 0) {
            $this->process_gedcoms--;
        } else {
            $frame        = array_pop($this->gedrec_stack);
            $this->gedrec = $frame->gedrec;
            $this->fact   = $frame->fact;
            $this->desc   = $frame->desc;
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function textBoxStartHandler(array $attrs): void
    {
        $bgcolor   = $attrs['bgcolor'] ?? '';
        $border    = (bool) ($attrs['border'] ?? false);
        $fill      = (bool) ($attrs['fill'] ?? false);
        $height    = (float) ($attrs['height'] ?? 0.0);
        $left      = (float) ($attrs['left'] ?? AbstractElement::CURRENT_POSITION);
        $newline   = (bool) ($attrs['newline'] ?? false);
        $padding   = (bool) ($attrs['pagecheck'] ?? true);
        $pagecheck = (bool) ($attrs['pagecheck'] ?? false);
        $reseth    = (bool) ($attrs['reseth'] ?? false);
        $top       = (float) ($attrs['top'] ?? AbstractElement::CURRENT_POSITION);
        $width     = (float) ($attrs['width'] ?? 0.0);
        $style     = '';

        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = false;

        $this->container_stack[] = $this->current_container;
        $this->current_container         = $this->renderer->createTextBox(
            $width,
            $height,
            $border,
            $bgcolor,
            $newline,
            $left,
            $top,
            $pagecheck,
            $style,
            $fill,
            $padding,
            $reseth
        );
    }

    protected function textBoxEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);

        // The text box we were building becomes an element to add to the parent container.
        assert($this->current_container instanceof AbstractTextBox);
        $this->current_element = $this->current_container;

        $this->current_container = array_pop($this->container_stack);
        $this->current_container->addElement($this->current_element);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function textStartHandler(array $attrs): void
    {
        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = true;

        $style = $this->renderer->getStyle($attrs['style'] ?? '');
        $color = $attrs['color'] ?? '';

        $this->current_element = $this->renderer->createText($style, $color);
    }

    protected function textEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);
        $this->current_container->addElement($this->current_element);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function getPersonNameStartHandler(array $attrs): void
    {
        $id    = '';
        $match = [];
        if (empty($attrs['id'])) {
            if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
                $id = $match[1];
            }
        } elseif (preg_match('/\$(.+)/', $attrs['id'], $match)) {
            if ($this->variables->has($match[1])) {
                $id = $this->variables->get($match[1]);
            }
        } elseif (preg_match('/@(.+)/', $attrs['id'], $match)) {
            $gmatch = [];
            if (preg_match("/\d $match[1] @([^@]+)@/", $this->gedrec, $gmatch)) {
                $id = $gmatch[1];
            }
        } else {
            $id = $attrs['id'];
        }
        if (!empty($id)) {
            $record = Registry::gedcomRecordFactory()->make($id, $this->tree);
            if ($record === null) {
                return;
            }
            if (!$record->canShowName()) {
                $this->current_element->addText(I18N::translate('Private'));
            } else {
                $name = $record->fullName();
                $name = strip_tags($name);
                if (!empty($attrs['truncate'])) {
                    $name = Str::limit($name, (int) $attrs['truncate'], I18N::translate('…'));
                } else {
                    $addname = $record->alternateName() ?? '';
                    $addname = strip_tags($addname);
                    if (!empty($addname)) {
                        $name .= ' ' . $addname;
                    }
                }
                $this->current_element->addText(trim($name));
            }
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function gedcomValueStartHandler(array $attrs): void
    {
        $id    = '';
        $match = [];
        if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
            $id = $match[1];
        }

        $tag       = $attrs['tag'];
        $use_break = (bool) ($attrs['use_break'] ?? false);

        if (!empty($tag)) {
            if ($tag === '@desc') {
                $value = $this->desc;
                $value = trim($value);
                $this->current_element->addText($value);
            }
            if ($tag === '@id') {
                $this->current_element->addText($id);
            } else {
                $tag = str_replace('@fact', $this->fact, $tag);
                if (empty($attrs['level'])) {
                    $level = (int) explode(' ', trim($this->gedrec))[0];
                    if ($level === 0) {
                        $level++;
                    }
                } else {
                    $level = (int) $attrs['level'];
                }
                $tags  = preg_split('/[: ]/', $tag);
                $value = $this->getGedcomValue($tag, $level, $this->gedrec);
                switch (end($tags)) {
                    case 'DATE':
                        $tmp   = new Date($value);
                        $value = strip_tags($tmp->display());
                        break;
                    case 'PLAC':
                        $tmp   = new Place($value, $this->tree);
                        $value = $tmp->shortName();
                        break;
                }
                if ($use_break) {
                    // Insert <br> when multiple dates exist.
                    // This works around a TCPDF bug that incorrectly wraps RTL dates on LTR pages
                    $value = str_replace('(', '<br>(', $value);
                    $value = str_replace('<span dir="ltr"><br>', '<br><span dir="ltr">', $value);
                    $value = str_replace('<span dir="rtl"><br>', '<br><span dir="rtl">', $value);
                    if (str_starts_with($value, '<br>')) {
                        $value = substr($value, 4);
                    }
                }

                if (!empty($attrs['truncate'])) {
                    $value = Str::limit($value, (int) $attrs['truncate'], I18N::translate('…'));
                }
                $this->current_element->addText($value);
            }
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function repeatTagStartHandler(array $attrs): void
    {
        $this->process_repeats++;
        if ($this->process_repeats > 1) {
            return;
        }

        $this->pushRepeatFrame();
        $this->repeats         = [];
        $this->repeat_xml      = (string) $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();

        $tag = $attrs['tag'] ?? '';

        if (!empty($tag)) {
            if ($tag === '@desc') {
                $value = $this->desc;
                $value = trim($value);
                $this->current_element->addText($value);
            } else {
                $tag   = str_replace('@fact', $this->fact, $tag);
                $tags  = explode(':', $tag);
                $level = (int) explode(' ', trim($this->gedrec))[0];
                if ($level === 0) {
                    $level++;
                }
                $subrec = $this->gedrec;
                $t      = $tag;
                $count  = count($tags);
                $i      = 0;
                while ($i < $count) {
                    $t = $tags[$i];
                    if (!empty($t)) {
                        if ($i < ($count - 1)) {
                            $subrec = self::getSubRecord($level, "$level $t", $subrec);
                            if (empty($subrec)) {
                                $level--;
                                $subrec = self::getSubRecord($level, "@ $t", $this->gedrec);
                                if (empty($subrec)) {
                                    return;
                                }
                            }
                        }
                        $level++;
                    }
                    $i++;
                }
                $level--;
                $count = preg_match_all("/$level $t(.*)/", $subrec, $match, PREG_SET_ORDER);
                $i     = 0;
                while ($i < $count) {
                    $i++;
                    // Privacy check - is this a link, and are we allowed to view the linked object?
                    $subrecord = self::getSubRecord($level, "$level $t", $subrec, $i);
                    if (preg_match('/^\d ' . Gedcom::REGEX_TAG . ' @(' . Gedcom::REGEX_XREF . ')@/', $subrecord, $xref_match)) {
                        $linked_object = Registry::gedcomRecordFactory()->make($xref_match[1], $this->tree);
                        if ($linked_object && !$linked_object->canShow()) {
                            continue;
                        }
                    }
                    $this->repeats[] = $subrecord;
                }
            }
        }
    }

    protected function repeatTagEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        // Re-parse the captured inner XML once per matched GEDCOM subrecord.
        if ($this->repeats !== []) {
            $fragment  = '<tempdoc>' . $this->repeat_xml . '</tempdoc>';
            $oldgedrec = $this->gedrec;

            foreach ($this->repeats as $gedrec) {
                $this->gedrec = $gedrec;
                $this->parseFragment($fragment);
            }

            $this->gedrec = $oldgedrec;
        }

        $this->popRepeatFrame();
    }

    /**
     * Variable lookup
     * Retrieve predefined variables :
     * @ desc GEDCOM fact description, example:
     *        1 EVEN This is a description
     * @ fact GEDCOM fact tag, such as BIRT, DEAT etc.
     * $ I18N::translate('....')
     * $ language_settings[]
     *
     * @param array<string,string> $attrs
     */
    protected function varStartHandler(array $attrs): void
    {
        if (empty($attrs['var'])) {
            throw new DomainException('REPORT ERROR var: The attribute "var=" is missing or not set in the XML file on line: ' . $this->currentLineNumber());
        }

        $var = $attrs['var'];

        if ($this->variables->has($var)) {
            $var = $this->variables->get($var);
        } else {
            $tfact = $this->fact;
            if (($this->fact === 'EVEN' || $this->fact === 'FACT') && $this->type !== '') {
                // Use :
                // n TYPE This text if string
                $tfact = $this->type;
            } else {
                foreach ([Individual::RECORD_TYPE, Family::RECORD_TYPE] as $record_type) {
                    $element = Registry::elementFactory()->make($record_type . ':' . $this->fact);

                    if (!$element instanceof UnknownElement) {
                        $tfact = $element->label();
                        break;
                    }
                }
            }

            $var = strtr($var, ['@desc' => $this->desc, '@fact' => $tfact]);

            if (preg_match('/^I18N::number\((.+)\)$/', $var, $match)) {
                $var = I18N::number((int) $match[1]);
            } elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $var, $match)) {
                $var = I18N::translate($match[1]);
            } elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $var, $match)) {
                $var = I18N::translateContext($match[1], $match[2]);
            }
        }
        // Check if variable is set as a date and reformat the date
        if (isset($attrs['date'])) {
            if ($attrs['date'] === '1') {
                $g   = new Date($var);
                $var = $g->display();
            }
        }
        $this->current_element->addText($var);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function factsStartHandler(array $attrs): void
    {
        $this->process_repeats++;
        if ($this->process_repeats > 1) {
            return;
        }

        $this->pushRepeatFrame();
        $this->repeats         = [];
        $this->repeat_xml      = (string) $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();

        $id    = '';
        $match = [];
        if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
            $id = $match[1];
        }
        $tag = '';
        if (isset($attrs['ignore'])) {
            $tag .= $attrs['ignore'];
        }
        if (preg_match('/\$(.+)/', $tag, $match)) {
            $tag = $this->variables->get($match[1]);
        }

        $record = Registry::gedcomRecordFactory()->make($id, $this->tree);
        if (empty($attrs['diff']) && !empty($id)) {
            $facts = $record->facts([], true);
            $this->repeats = [];
            $nonfacts      = explode(',', $tag);
            foreach ($facts as $fact) {
                $tag = explode(':', $fact->tag())[1];

                if (!in_array($tag, $nonfacts, true)) {
                    $this->repeats[] = $fact->gedcom();
                }
            }
        } else {
            foreach ($record->facts() as $fact) {
                if (($fact->isPendingAddition() || $fact->isPendingDeletion()) && !str_ends_with($fact->tag(), ':CHAN')) {
                    $this->repeats[] = $fact->gedcom();
                }
            }
        }
    }

    protected function factsEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        if ($this->repeats !== []) {
            $fragment  = '<tempdoc>' . $this->repeat_xml . '</tempdoc>';
            $oldgedrec = $this->gedrec;

            foreach ($this->repeats as $gedrec) {
                $this->gedrec = $gedrec;
                $this->fact   = '';
                $this->desc   = '';
                if (preg_match('/1 (\w+)(.*)/', $this->gedrec, $match)) {
                    $this->fact = $match[1];
                    if ($this->fact === 'EVEN' || $this->fact === 'FACT') {
                        $tmatch = [];
                        if (preg_match('/2 TYPE (.+)/', $this->gedrec, $tmatch)) {
                            $this->type = trim($tmatch[1]);
                        } else {
                            $this->type = ' ';
                        }
                    }
                    $this->desc = trim($match[2]);
                    $this->desc .= self::getCont(2, $this->gedrec);
                }

                $this->parseFragment($fragment);
            }

            $this->gedrec = $oldgedrec;
        }

        $this->popRepeatFrame();
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function setVarStartHandler(array $attrs): void
    {
        if (empty($attrs['name'])) {
            throw new DomainException('REPORT ERROR var: The attribute "name" is missing or not set in the XML file');
        }

        $name  = $attrs['name'];
        $value = $attrs['value'];
        $match = [];

        // Current GEDCOM record strings
        if ($value === '@ID') {
            if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
                $value = $match[1];
            }
        } elseif ($value === '@fact') {
            $value = $this->fact;
        } elseif ($value === '@desc') {
            $value = $this->desc;
        } elseif ($value === '@generation') {
            $value = (string) $this->generation;
        } elseif (preg_match("/@(\w+)/", $value, $match)) {
            $gmatch = [];
            if (preg_match("/\d $match[1] (.+)/", $this->gedrec, $gmatch)) {
                $value = str_replace('@', '', trim($gmatch[1]));
            }
        }
        $count = preg_match_all("/\\$(\w+)/", $value, $match, PREG_SET_ORDER);
        $i     = 0;
        while ($i < $count) {
            $t     = $this->variables->get($match[$i][1]);
            $value = preg_replace('/\$' . $match[$i][1] . '/', $t, $value, 1);
            $i++;
        }
        if (preg_match('/^I18N::number\((.+)\)$/', $value, $match)) {
            $value = I18N::number((int) $match[1]);
        } elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $value, $match)) {
            $value = I18N::translate($match[1]);
        } elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $value, $match)) {
            $value = I18N::translateContext($match[1], $match[2]);
        }

        // Arithmetic functions
        if (preg_match("/(\d+)\s*([-+*\/])\s*(\d+)/", $value, $match)) {
            // Create an expression language with the functions used by our reports.
            $expression_provider  = new ExpressionLanguageProvider();
            $expression_cache     = new NullAdapter();
            $expression_language  = new ExpressionLanguage($expression_cache, [$expression_provider]);

            $value = (string) $expression_language->evaluate($value);
        }

        if (str_contains($value, '@')) {
            $value = '';
        }
        $this->variables->set($name, $value);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function ifStartHandler(array $attrs): void
    {
        if ($this->process_ifs > 0) {
            $this->process_ifs++;

            return;
        }

        $condition = $attrs['condition'];
        $condition = $this->substituteVars($condition, true);
        $condition = str_replace([
            ' LT ',
            ' GT ',
        ], [
            '<',
            '>',
        ], $condition);
        // Replace the first occurrence only once of @fact:DATE or in any other combinations to the current fact, such as BIRT
        $condition = str_replace('@fact:', $this->fact . ':', $condition);
        $match     = [];
        $count     = preg_match_all("/@([\w:.]+)/", $condition, $match, PREG_SET_ORDER);
        $i         = 0;
        while ($i < $count) {
            $id    = $match[$i][1];
            $value = '""';
            if ($id === 'ID') {
                if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
                    $value = "'" . $match[1] . "'";
                }
            } elseif ($id === 'fact') {
                $value = '"' . $this->fact . '"';
            } elseif ($id === 'desc') {
                $value = '"' . addslashes($this->desc) . '"';
            } elseif ($id === 'generation') {
                $value = '"' . $this->generation . '"';
            } else {
                $level = (int) explode(' ', trim($this->gedrec))[0];
                if ($level === 0) {
                    $level++;
                }
                $value = $this->getGedcomValue($id, $level, $this->gedrec);
                if (empty($value)) {
                    $level++;
                    $value = $this->getGedcomValue($id, $level, $this->gedrec);
                }
                $value = preg_replace('/^@(' . Gedcom::REGEX_XREF . ')@$/', '$1', $value);
                $value = '"' . addslashes($value) . '"';
            }
            $condition = str_replace("@$id", $value, $condition);
            $i++;
        }

        // Create an expression language with the functions used by our reports.
        $expression_provider  = new ExpressionLanguageProvider();
        $expression_cache     = new NullAdapter();
        $expression_language  = new ExpressionLanguage($expression_cache, [$expression_provider]);

        $ret = $expression_language->evaluate($condition);

        if (!$ret) {
            $this->process_ifs++;
        }
    }

    protected function ifEndHandler(): void
    {
        if ($this->process_ifs > 0) {
            $this->process_ifs--;
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function footnoteStartHandler(array $attrs): void
    {
        $id = '';
        if (preg_match('/[0-9] (.+) @(.+)@/', $this->gedrec, $match)) {
            $id = $match[2];
        }
        $record = Registry::gedcomRecordFactory()->make($id, $this->tree);
        if ($record && $record->canShow()) {
            $this->print_data_stack[] = $this->print_data;
            $this->print_data         = true;
            $style                    = $this->renderer->getStyle($attrs['style'] ?? 'footnote');
            $this->footnote_element = $this->current_element;
            $this->current_element  = $this->renderer->createFootnote($style);
        } else {
            $this->print_data       = false;
            $this->process_footnote = false;
        }
    }

    protected function footnoteEndHandler(): void
    {
        if ($this->process_footnote) {
            $this->print_data = array_pop($this->print_data_stack);
            $temp             = trim($this->current_element->getValue());
            if (strlen($temp) > 3) {
                $this->current_container->addElement($this->current_element);
            }
            $this->current_element = $this->footnote_element;
        } else {
            $this->process_footnote = true;
        }
    }

    protected function footnoteTextsStartHandler(): void
    {
        $this->current_container->addElement(new FootnoteTextsElement());
    }

    protected function brStartHandler(): void
    {
        if ($this->print_data && $this->process_gedcoms === 0) {
            $this->current_element->addText('<br>');
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function highlightedImageStartHandler(array $attrs): void
    {
        $id = '';
        if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
            $id = $match[1];
        }

        $align  = $this->parseCellAlign($attrs['align'] ?? '');
        $height = (float) ($attrs['height'] ?? 0.0);
        $left   = (float) ($attrs['left'] ?? AbstractElement::CURRENT_POSITION);
        $ln     = ImageContinuation::tryFrom($attrs['ln'] ?? 'T') ?? ImageContinuation::SameLine;
        $top    = (float) ($attrs['top'] ?? AbstractElement::CURRENT_POSITION);
        $width  = (float) ($attrs['width'] ?? 0.0);

        $person     = Registry::individualFactory()->make($id, $this->tree);
        $media_file = $person->findHighlightedMediaFile();

        if ($media_file instanceof MediaFile && $media_file->fileExists()) {
            $image      = imagecreatefromstring($media_file->fileContents());
            $attributes = [imagesx($image), imagesy($image)];

            if ($width > 0 && $height == 0) {
                $perc   = $width / $attributes[0];
                $height = round($attributes[1] * $perc);
            } elseif ($height > 0 && $width == 0) {
                $perc  = $height / $attributes[1];
                $width = round($attributes[0] * $perc);
            } else {
                $width  = (float) $attributes[0];
                $height = (float) $attributes[1];
            }
            $image = $this->renderer->createImageFromObject($media_file, $left, $top, $width, $height, $align, $ln);
            $this->current_container->addElement($image);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function imageStartHandler(array $attrs): void
    {
        $align  = $this->parseCellAlign($attrs['align'] ?? '');
        $file   = $attrs['file'] ?? '';
        $height = (float) ($attrs['height'] ?? 0.0);
        $left   = (float) ($attrs['left'] ?? AbstractElement::CURRENT_POSITION);
        $ln     = ImageContinuation::tryFrom($attrs['ln'] ?? 'T') ?? ImageContinuation::SameLine;
        $top    = (float) ($attrs['top'] ?? AbstractElement::CURRENT_POSITION);
        $width  = (float) ($attrs['width'] ?? 0.0);

        if ($file === '@FILE') {
            $match = [];
            if (preg_match("/\d OBJE @(.+)@/", $this->gedrec, $match)) {
                $mediaobject = Registry::mediaFactory()->make($match[1], $this->tree);
                $media_file  = $mediaobject->firstImageFile();

                if ($media_file instanceof MediaFile && $media_file->fileExists()) {
                    $image      = imagecreatefromstring($media_file->fileContents());
                    $attributes = [imagesx($image), imagesy($image)];

                    if ($width > 0 && $height == 0) {
                        $perc   = $width / $attributes[0];
                        $height = round($attributes[1] * $perc);
                    } elseif ($height > 0 && $width == 0) {
                        $perc  = $height / $attributes[1];
                        $width = round($attributes[0] * $perc);
                    } else {
                        $width  = (float) $attributes[0];
                        $height = (float) $attributes[1];
                    }
                    $image = $this->renderer->createImageFromObject($media_file, $left, $top, $width, $height, $align, $ln);
                    $this->current_container->addElement($image);
                }
            }
        } elseif (file_exists($file) && preg_match('/(jpg|jpeg|png|gif)$/i', $file)) {
            $size = getimagesize($file);
            if ($width > 0 && $height == 0) {
                $perc   = $width / $size[0];
                $height = round($size[1] * $perc);
            } elseif ($height > 0 && $width == 0) {
                $perc  = $height / $size[1];
                $width = round($size[0] * $perc);
            } else {
                $width  = $size[0];
                $height = $size[1];
            }
            $image = $this->renderer->createImage($file, $left, $top, $width, $height, $align, $ln);
            $this->current_container->addElement($image);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function lineStartHandler(array $attrs): void
    {
        $x1 = (float) ($attrs['x1'] ?? AbstractElement::CURRENT_POSITION);
        $x2 = (float) ($attrs['x2'] ?? AbstractElement::CURRENT_POSITION);
        $y1 = (float) ($attrs['y1'] ?? AbstractElement::CURRENT_POSITION);
        $y2 = (float) ($attrs['y2'] ?? AbstractElement::CURRENT_POSITION);

        $line = $this->renderer->createLine($x1, $y1, $x2, $y2);
        $this->current_container->addElement($line);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function listStartHandler(array $attrs): void
    {
        $this->process_repeats++;
        if ($this->process_repeats > 1) {
            return;
        }

        $match = [];
        if (isset($attrs['sortby'])) {
            $sortby = $attrs['sortby'];
            if (preg_match("/\\$(\w+)/", $sortby, $match)) {
                $sortby = $this->variables->get($match[1]);
                $sortby = trim($sortby);
            }
        } else {
            $sortby = 'NAME';
        }

        $listname = $attrs['list'] ?? 'individual';

        // Some filters/sorts can be applied using SQL, while others require PHP
        switch ($listname) {
            case 'pending':
                $this->list = DB::table('change')
                    ->whereIn('change_id', function (Builder $query): void {
                        $query->select([new Expression('MAX(change_id)')])
                            ->from('change')
                            ->where('gedcom_id', '=', $this->tree->id())
                            ->where('status', '=', 'pending')
                            ->groupBy(['xref']);
                    })
                    ->get()
                    ->map(fn (object $row): GedcomRecord|null => Registry::gedcomRecordFactory()->make($row->xref, $this->tree, $row->new_gedcom ?: $row->old_gedcom))
                    ->filter()
                    ->all();
                break;

            case 'individual':
                $query = DB::table('individuals')
                    ->where('i_file', '=', $this->tree->id())
                    ->select(['i_id AS xref', 'i_gedcom AS gedcom'])
                    ->distinct();

                foreach ($attrs as $attr => $value) {
                    if (str_starts_with($attr, 'filter') && $value !== '') {
                        $value = $this->substituteVars($value, false);
                        // Convert the various filters into SQL
                        if (preg_match('/^(\w+):DATE (LTE|GTE) (.+)$/', $value, $match)) {
                            $query->join('dates AS ' . $attr, static function (JoinClause $join) use ($attr): void {
                                $join
                                    ->on($attr . '.d_gid', '=', 'i_id')
                                    ->on($attr . '.d_file', '=', 'i_file');
                            });

                            $query->where($attr . '.d_fact', '=', $match[1]);

                            $date = new Date($match[3]);

                            if ($match[2] === 'LTE') {
                                $query->where($attr . '.d_julianday2', '<=', $date->maximumJulianDay());
                            } else {
                                $query->where($attr . '.d_julianday1', '>=', $date->minimumJulianDay());
                            }

                            // This filter has been fully processed
                            unset($attrs[$attr]);
                        } elseif (preg_match('/^NAME CONTAINS (.+)$/', $value, $match)) {
                            $query->join('name AS ' . $attr, static function (JoinClause $join) use ($attr): void {
                                $join
                                    ->on($attr . '.n_id', '=', 'i_id')
                                    ->on($attr . '.n_file', '=', 'i_file');
                            });
                            // Search the DB only if there is any name supplied
                            $names = explode(' ', $match[1]);
                            foreach ($names as $name) {
                                $query->where($attr . '.n_full', 'LIKE', '%' . addcslashes($name, '\\%_') . '%');
                            }

                            // This filter has been fully processed
                            unset($attrs[$attr]);
                        } elseif (preg_match('/^LIKE \/(.+)\/$/', $value, $match)) {
                            // Convert newline escape sequences to actual new lines
                            $match[1] = str_replace('\n', "\n", $match[1]);

                            $query->where('i_gedcom', 'LIKE', $match[1]);

                            // This filter has been fully processed
                            unset($attrs[$attr]);
                        } elseif (preg_match('/^(?:\w*):PLAC CONTAINS (.+)$/', $value, $match)) {
                            // Don't unset this filter. This is just initial filtering for performance
                            $query
                                ->join('placelinks AS ' . $attr . 'a', static function (JoinClause $join) use ($attr): void {
                                    $join
                                        ->on($attr . 'a.pl_file', '=', 'i_file')
                                        ->on($attr . 'a.pl_gid', '=', 'i_id');
                                })
                                ->join('places AS ' . $attr . 'b', static function (JoinClause $join) use ($attr): void {
                                    $join
                                        ->on($attr . 'b.p_file', '=', $attr . 'a.pl_file')
                                        ->on($attr . 'b.p_id', '=', $attr . 'a.pl_p_id');
                                })
                                ->where($attr . 'b.p_place', 'LIKE', '%' . addcslashes($match[1], '\\%_') . '%');
                        } elseif (preg_match('/^(\w*):(\w+) CONTAINS (.+)$/', $value, $match)) {
                            // Don't unset this filter. This is just initial filtering for performance
                            $match[3] = strtr($match[3], ['\\' => '\\\\', '%'  => '\\%', '_'  => '\\_', ' ' => '%']);
                            $like = "%\n1 " . $match[1] . "%\n2 " . $match[2] . '%' . $match[3] . '%';
                            $query->where('i_gedcom', 'LIKE', $like);
                        } elseif (preg_match('/^(\w+) CONTAINS (.*)$/', $value, $match)) {
                            // Don't unset this filter. This is just initial filtering for performance
                            $match[2] = strtr($match[2], ['\\' => '\\\\', '%'  => '\\%', '_'  => '\\_', ' ' => '%']);
                            $like = "%\n1 " . $match[1] . '%' . $match[2] . '%';
                            $query->where('i_gedcom', 'LIKE', $like);
                        }
                    }
                }

                $this->list = [];

                foreach ($query->get() as $row) {
                    $this->list[$row->xref] = Registry::individualFactory()->make($row->xref, $this->tree, $row->gedcom);
                }
                break;

            case 'family':
                $query = DB::table('families')
                    ->where('f_file', '=', $this->tree->id())
                    ->select(['f_id AS xref', 'f_gedcom AS gedcom'])
                    ->distinct();

                foreach ($attrs as $attr => $value) {
                    if (str_starts_with($attr, 'filter') && $value !== '') {
                        $value = $this->substituteVars($value, false);
                        // Convert the various filters into SQL
                        if (preg_match('/^(\w+):DATE (LTE|GTE) (.+)$/', $value, $match)) {
                            $query->join('dates AS ' . $attr, static function (JoinClause $join) use ($attr): void {
                                $join
                                    ->on($attr . '.d_gid', '=', 'f_id')
                                    ->on($attr . '.d_file', '=', 'f_file');
                            });

                            $query->where($attr . '.d_fact', '=', $match[1]);

                            $date = new Date($match[3]);

                            if ($match[2] === 'LTE') {
                                $query->where($attr . '.d_julianday2', '<=', $date->maximumJulianDay());
                            } else {
                                $query->where($attr . '.d_julianday1', '>=', $date->minimumJulianDay());
                            }

                            // This filter has been fully processed
                            unset($attrs[$attr]);
                        } elseif (preg_match('/^LIKE \/(.+)\/$/', $value, $match)) {
                            // Convert newline escape sequences to actual new lines
                            $match[1] = str_replace('\n', "\n", $match[1]);

                            $query->where('f_gedcom', 'LIKE', $match[1]);

                            // This filter has been fully processed
                            unset($attrs[$attr]);
                        } elseif (preg_match('/^NAME CONTAINS (.*)$/', $value, $match)) {
                            if ($sortby === 'NAME' || $match[1] !== '') {
                                $query->join('name AS ' . $attr, static function (JoinClause $join) use ($attr): void {
                                    $join
                                        ->on($attr . '.n_file', '=', 'f_file')
                                        ->where(static function (Builder $query): void {
                                            $query
                                                ->whereColumn('n_id', '=', 'f_husb')
                                                ->orWhereColumn('n_id', '=', 'f_wife');
                                        });
                                });
                                // Search the DB only if there is any name supplied
                                if ($match[1] != '') {
                                    $names = explode(' ', $match[1]);
                                    foreach ($names as $name) {
                                        $query->where($attr . '.n_full', 'LIKE', '%' . addcslashes($name, '\\%_') . '%');
                                    }
                                }
                            }

                            // This filter has been fully processed
                            unset($attrs[$attr]);
                        } elseif (preg_match('/^(?:\w*):PLAC CONTAINS (.+)$/', $value, $match)) {
                            // Don't unset this filter. This is just initial filtering for performance
                            $query
                                ->join('placelinks AS ' . $attr . 'a', static function (JoinClause $join) use ($attr): void {
                                    $join
                                        ->on($attr . 'a.pl_file', '=', 'f_file')
                                        ->on($attr . 'a.pl_gid', '=', 'f_id');
                                })
                                ->join('places AS ' . $attr . 'b', static function (JoinClause $join) use ($attr): void {
                                    $join
                                        ->on($attr . 'b.p_file', '=', $attr . 'a.pl_file')
                                        ->on($attr . 'b.p_id', '=', $attr . 'a.pl_p_id');
                                })
                                ->where($attr . 'b.p_place', 'LIKE', '%' . addcslashes($match[1], '\\%_') . '%');
                        } elseif (preg_match('/^(\w*):(\w+) CONTAINS (.+)$/', $value, $match)) {
                            // Don't unset this filter. This is just initial filtering for performance
                            $match[3] = strtr($match[3], ['\\' => '\\\\', '%'  => '\\%', '_'  => '\\_', ' ' => '%']);
                            $like = "%\n1 " . $match[1] . "%\n2 " . $match[2] . '%' . $match[3] . '%';
                            $query->where('f_gedcom', 'LIKE', $like);
                        } elseif (preg_match('/^(\w+) CONTAINS (.+)$/', $value, $match)) {
                            // Don't unset this filter. This is just initial filtering for performance
                            $match[2] = strtr($match[2], ['\\' => '\\\\', '%'  => '\\%', '_'  => '\\_', ' ' => '%']);
                            $like = "%\n1 " . $match[1] . '%' . $match[2] . '%';
                            $query->where('f_gedcom', 'LIKE', $like);
                        }
                    }
                }

                $this->list = [];

                foreach ($query->get() as $row) {
                    $this->list[$row->xref] = Registry::familyFactory()->make($row->xref, $this->tree, $row->gedcom);
                }
                break;

            default:
                throw new DomainException('Invalid list name: ' . $listname);
        }

        $filters  = [];
        $filters2 = [];
        if (isset($attrs['filter1']) && count($this->list) > 0) {
            foreach ($attrs as $key => $value) {
                if (preg_match("/filter(\d)/", $key)) {
                    $condition = $value;
                    if (preg_match("/@(\w+)/", $condition, $match)) {
                        $id    = $match[1];
                        $value = "''";
                        if ($id === 'ID') {
                            if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
                                $value = "'" . $match[1] . "'";
                            }
                        } elseif ($id === 'fact') {
                            $value = "'" . $this->fact . "'";
                        } elseif ($id === 'desc') {
                            $value = "'" . $this->desc . "'";
                        } elseif (preg_match("/\d $id (.+)/", $this->gedrec, $match)) {
                            $value = "'" . str_replace('@', '', trim($match[1])) . "'";
                        }
                        $condition = preg_replace("/@$id/", $value, $condition);
                    }
                    //-- handle regular expressions
                    if (preg_match("/([A-Z:]+)\s*([^\s]+)\s*(.+)/", $condition, $match)) {
                        $tag  = trim($match[1]);
                        $expr = trim($match[2]);
                        $val  = trim($match[3]);
                        if (preg_match("/\\$(\w+)/", $val, $match)) {
                            $val = $this->variables->get($match[1]);
                            $val = trim($val);
                        }
                        if ($val !== '') {
                            $searchstr = '';
                            $tags      = explode(':', $tag);
                            //-- only limit to a level number if we are specifically looking at a level
                            if (count($tags) > 1) {
                                $level = 1;
                                $t = 'XXXX';
                                foreach ($tags as $t) {
                                    if (!empty($searchstr)) {
                                        $searchstr .= "[^\n]*(\n[2-9][^\n]*)*\n";
                                    }
                                    //-- search for both EMAIL and _EMAIL... silly double gedcom standard
                                    if ($t === 'EMAIL' || $t === '_EMAIL') {
                                        $t = '_?EMAIL';
                                    }
                                    $searchstr .= $level . ' ' . $t;
                                    $level++;
                                }
                            } else {
                                if ($tag === 'EMAIL' || $tag === '_EMAIL') {
                                    $tag = '_?EMAIL';
                                }
                                $t         = $tag;
                                $searchstr = '1 ' . $tag;
                            }
                            switch ($expr) {
                                case 'CONTAINS':
                                    if ($t === 'PLAC') {
                                        $searchstr .= "[^\n]*[, ]*" . $val;
                                    } else {
                                        $searchstr .= "[^\n]*" . $val;
                                    }
                                    $filters[] = $searchstr;
                                    break;
                                default:
                                    $filters2[] = [
                                        'tag'  => $tag,
                                        'expr' => $expr,
                                        'val'  => $val,
                                    ];
                                    break;
                            }
                        }
                    }
                }
            }
        }
        //-- apply other filters to the list that could not be added to the search string
        if ($filters !== []) {
            foreach ($this->list as $key => $record) {
                foreach ($filters as $filter) {
                    if (!preg_match('/' . $filter . '/i', $record->privatizeGedcom(Auth::accessLevel($this->tree)))) {
                        unset($this->list[$key]);
                        break;
                    }
                }
            }
        }
        if ($filters2 !== []) {
            $mylist = [];
            foreach ($this->list as $indi) {
                $key  = $indi->xref();
                $grec = $indi->privatizeGedcom(Auth::accessLevel($this->tree));
                $keep = true;
                foreach ($filters2 as $filter) {
                    if ($keep) {
                        $tag  = $filter['tag'];
                        $expr = $filter['expr'];
                        $val  = $filter['val'];
                        if ($val === "''") {
                            $val = '';
                        }
                        $tags = explode(':', $tag);
                        $t    = end($tags);
                        $v    = $this->getGedcomValue($tag, 1, $grec);
                        //-- check for EMAIL and _EMAIL (silly double gedcom standard :P)
                        if ($t === 'EMAIL' && empty($v)) {
                            $tag  = str_replace('EMAIL', '_EMAIL', $tag);
                            $tags = explode(':', $tag);
                            $t    = end($tags);
                            $v    = self::getSubRecord(1, $tag, $grec);
                        }

                        switch ($expr) {
                            case 'GTE':
                                if ($t === 'DATE') {
                                    $date1 = new Date($v);
                                    $date2 = new Date($val);
                                    $keep  = (Date::compare($date1, $date2) >= 0);
                                } elseif ($val >= $v) {
                                    $keep = true;
                                }
                                break;
                            case 'LTE':
                                if ($t === 'DATE') {
                                    $date1 = new Date($v);
                                    $date2 = new Date($val);
                                    $keep  = (Date::compare($date1, $date2) <= 0);
                                } elseif ($val >= $v) {
                                    $keep = true;
                                }
                                break;
                            default:
                                if ($v == $val) {
                                    $keep = true;
                                } else {
                                    $keep = false;
                                }
                                break;
                        }
                    }
                }
                if ($keep) {
                    $mylist[$key] = $indi;
                }
            }
            $this->list = $mylist;
        }

        switch ($sortby) {
            case 'NAME':
                uasort($this->list, GedcomRecord::nameComparator());
                break;
            case 'CHAN':
                uasort($this->list, GedcomRecord::lastChangeComparator());
                break;
            case 'BIRT:DATE':
                uasort($this->list, Individual::birthDateComparator());
                break;
            case 'DEAT:DATE':
                uasort($this->list, Individual::deathDateComparator());
                break;
            case 'MARR:DATE':
                uasort($this->list, Family::marriageDateComparator());
                break;
            default:
                // unsorted or already sorted by SQL
                break;
        }

        $this->pushRepeatFrame();
        $this->repeat_xml      = (string) $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();
    }

    protected function listEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        // Check if there is any list
        if (count($this->list) > 0) {
            $fragment  = '<tempdoc>' . $this->repeat_xml . '</tempdoc>';
            $oldgedrec = $this->gedrec;

            $this->list_total   = count($this->list);
            $this->list_private = 0;
            foreach ($this->list as $record) {
                if ($record->canShow()) {
                    $this->gedrec = $record->privatizeGedcom(Auth::accessLevel($record->tree()));
                    $this->parseFragment($fragment);
                } else {
                    $this->list_private++;
                }
            }
            $this->list   = [];
            $this->gedrec = $oldgedrec;
        }
        $this->popRepeatFrame();
    }

    /**
     * Handle <listTotal>
     * Prints the total number of records in a list
     * The total number is collected from <list> and <relatives>
     */
    protected function listTotalStartHandler(): void
    {
        if ($this->list_private == 0) {
            $this->current_element->addText((string) $this->list_total);
        } else {
            $this->current_element->addText(($this->list_total - $this->list_private) . ' / ' . $this->list_total);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function relativesStartHandler(array $attrs): void
    {
        $this->process_repeats++;
        if ($this->process_repeats > 1) {
            return;
        }

        $sortby = $attrs['sortby'] ?? 'NAME';

        $match = [];
        if (preg_match("/\\$(\w+)/", $sortby, $match)) {
            $sortby = $this->variables->get($match[1]);
            $sortby = trim($sortby);
        }

        $maxgen = (int) ($attrs['maxgen'] ?? -1);
        $group  = $attrs['group'] ?? 'child-family';

        if (preg_match("/\\$(\w+)/", $group, $match)) {
            $group = $this->variables->get($match[1]);
            $group = trim($group);
        }

        $id = $attrs['id'] ?? '';

        if (preg_match("/\\$(\w+)/", $id, $match)) {
            $id = $this->variables->get($match[1]);
            $id = trim($id);
        }

        $this->list = [];
        $person     = Registry::individualFactory()->make($id, $this->tree);
        if ($person instanceof Individual) {
            $this->list[$id] = $person;
            switch ($group) {
                case 'child-family':
                    foreach ($person->childFamilies() as $family) {
                        foreach ($family->spouses() as $spouse) {
                            $this->list[$spouse->xref()] = $spouse;
                        }

                        foreach ($family->children() as $child) {
                            $this->list[$child->xref()] = $child;
                        }
                    }
                    break;
                case 'spouse-family':
                    foreach ($person->spouseFamilies() as $family) {
                        foreach ($family->spouses() as $spouse) {
                            $this->list[$spouse->xref()] = $spouse;
                        }

                        foreach ($family->children() as $child) {
                            $this->list[$child->xref()] = $child;
                        }
                    }
                    break;
                case 'direct-ancestors':
                    $this->addAncestors($this->list, $id, false, $maxgen);
                    break;
                case 'ancestors':
                    $this->addAncestors($this->list, $id, true, $maxgen);
                    break;
                case 'descendants':
                    $this->list[$id]->generation = 1;
                    $this->addDescendancy($this->list, $id, false, $maxgen);
                    break;
                case 'all':
                    $this->addAncestors($this->list, $id, true, $maxgen);
                    $this->addDescendancy($this->list, $id, true, $maxgen);
                    break;
            }
        }

        switch ($sortby) {
            case 'NAME':
                uasort($this->list, GedcomRecord::nameComparator());
                break;
            case 'BIRT:DATE':
                uasort($this->list, Individual::birthDateComparator());
                break;
            case 'DEAT:DATE':
                uasort($this->list, Individual::deathDateComparator());
                break;
            case 'generation':
                uasort($this->list, static fn ($x, $y): int => $x->generation <=> $y->generation);
                break;
            default:
                // unsorted
                break;
        }
        $this->pushRepeatFrame();
        $this->repeat_xml      = (string) $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();
    }

    protected function relativesEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        // Check if there is any relatives
        if (count($this->list) > 0) {
            $fragment  = '<tempdoc>' . $this->repeat_xml . '</tempdoc>';
            $oldgedrec = $this->gedrec;

            $this->list_total   = count($this->list);
            $this->list_private = 0;
            foreach ($this->list as $xref => $value) {
                if (isset($value->generation)) {
                    $this->generation = $value->generation;
                }
                $tmp          = Registry::gedcomRecordFactory()->make((string) $xref, $this->tree);
                $this->gedrec = $tmp->privatizeGedcom(Auth::accessLevel($this->tree));

                $this->parseFragment($fragment);
            }
            // Clean up the list array
            $this->list   = [];
            $this->gedrec = $oldgedrec;
        }
        $this->popRepeatFrame();
    }

    protected function generationStartHandler(): void
    {
        $this->current_element->addText(I18N::number($this->generation));
    }

    protected function newPageStartHandler(): void
    {
        $this->current_container->addElement(new NewPageElement());
    }

    protected function titleStartHandler(): void
    {
        $this->current_element = new NullElement();
    }

    protected function titleEndHandler(): void
    {
        $this->report_title = $this->current_element->getValue();
    }

    protected function descriptionStartHandler(): void
    {
        $this->current_element = new NullElement();
    }

    protected function descriptionEndHandler(): void
    {
        $this->report_description = $this->current_element->getValue();
    }

    /**
     * @param array<Individual> $list
     */
    private function addDescendancy(array &$list, string $pid, bool $parents = false, int $generations = -1): void
    {
        $person = Registry::individualFactory()->make($pid, $this->tree);
        if ($person === null) {
            return;
        }
        if (!isset($list[$pid])) {
            $list[$pid] = $person;
        }
        if (!isset($list[$pid]->generation)) {
            $list[$pid]->generation = 0;
        }
        foreach ($person->spouseFamilies() as $family) {
            if ($parents) {
                $husband = $family->husband();
                $wife    = $family->wife();
                if ($husband) {
                    $list[$husband->xref()] = $husband;
                    if (isset($list[$pid]->generation)) {
                        $list[$husband->xref()]->generation = $list[$pid]->generation - 1;
                    } else {
                        $list[$husband->xref()]->generation = 1;
                    }
                }
                if ($wife) {
                    $list[$wife->xref()] = $wife;
                    if (isset($list[$pid]->generation)) {
                        $list[$wife->xref()]->generation = $list[$pid]->generation - 1;
                    } else {
                        $list[$wife->xref()]->generation = 1;
                    }
                }
            }

            $children = $family->children();

            foreach ($children as $child) {
                if ($child) {
                    $list[$child->xref()] = $child;

                    if (isset($list[$pid]->generation)) {
                        $list[$child->xref()]->generation = $list[$pid]->generation + 1;
                    } else {
                        $list[$child->xref()]->generation = 2;
                    }
                }
            }
            if ($generations === -1 || $list[$pid]->generation + 1 < $generations) {
                foreach ($children as $child) {
                    $this->addDescendancy($list, $child->xref(), $parents, $generations);
                }
            }
        }
    }

    /**
     * @param array<Individual> $list
     */
    private function addAncestors(array &$list, string $pid, bool $children = false, int $generations = -1): void
    {
        $genlist                = [$pid];
        $list[$pid]->generation = 1;
        while (count($genlist) > 0) {
            $id = array_shift($genlist);
            if (str_starts_with($id, 'empty')) {
                continue; // id can be something like “empty7”
            }
            $person = Registry::individualFactory()->make($id, $this->tree);
            foreach ($person->childFamilies() as $family) {
                $husband = $family->husband();
                $wife    = $family->wife();
                if ($husband) {
                    $list[$husband->xref()]             = $husband;
                    $list[$husband->xref()]->generation = $list[$id]->generation + 1;
                }
                if ($wife) {
                    $list[$wife->xref()]             = $wife;
                    $list[$wife->xref()]->generation = $list[$id]->generation + 1;
                }
                if ($generations === -1 || $list[$id]->generation + 1 < $generations) {
                    if ($husband) {
                        $genlist[] = $husband->xref();
                    }
                    if ($wife) {
                        $genlist[] = $wife->xref();
                    }
                }
                if ($children) {
                    foreach ($family->children() as $child) {
                        $list[$child->xref()] = $child;
                        $list[$child->xref()]->generation = $list[$id]->generation ?? 1;
                    }
                }
            }
        }
    }

    private function getGedcomValue(string $tag, int $level, string $gedrec): string
    {
        if ($gedrec === '') {
            return '';
        }
        $tags      = explode(':', $tag);
        $origlevel = $level;
        if ($level === 0) {
            $level = 1 + (int) $gedrec[0];
        }

        $subrec = $gedrec;
        $t = 'XXXX';
        foreach ($tags as $t) {
            $lastsubrec = $subrec;
            $subrec     = self::getSubRecord($level, "$level $t", $subrec);
            if (empty($subrec) && $origlevel == 0) {
                $level--;
                $subrec = self::getSubRecord($level, "$level $t", $lastsubrec);
            }
            if (empty($subrec)) {
                if ($t === 'TITL') {
                    $subrec = self::getSubRecord($level, "$level ABBR", $lastsubrec);
                    if (!empty($subrec)) {
                        $t = 'ABBR';
                    }
                }
                if ($subrec === '') {
                    if ($level > 0) {
                        $level--;
                    }
                    $subrec = self::getSubRecord($level, "@ $t", $gedrec);
                    if ($subrec === '') {
                        return '';
                    }
                }
            }
            $level++;
        }
        $level--;
        $ct = preg_match("/$level $t(.*)/", $subrec, $match);
        if ($ct === 0) {
            $ct = preg_match("/$level @.+@ (.+)/", $subrec, $match);
        }
        if ($ct === 0) {
            $ct = preg_match("/@ $t (.+)/", $subrec, $match);
        }
        if ($ct > 0) {
            $value = trim($match[1]);
            if ($t === 'NOTE' && preg_match('/^@(.+)@$/', $value, $match)) {
                $note = Registry::noteFactory()->make($match[1], $this->tree);
                if ($note instanceof Note) {
                    $value = $note->getNote();
                } else {
                    //-- set the value to the id without the @
                    $value = $match[1];
                }
            }
            if ($level !== 0 || $t !== 'NOTE') {
                $value .= self::getCont($level + 1, $subrec);
            }

            if ($tag === 'NAME' || $tag === '_MARNM' || $tag === '_AKA') {
                return strtr($value, ['/' => '']);
            }

            return $value;
        }

        return '';
    }

    /**
     * Replace variable identifiers with their values.
     *
     * @param string $expression An expression such as "$foo == 123"
     * @param bool   $quote      Whether to add quotation marks
     */
    private function substituteVars(string $expression, bool $quote): string
    {
        return preg_replace_callback(
            '/\$(\w+)/',
            function (array $matches) use ($quote, $expression): string {
                if ($this->variables->has($matches[1])) {
                    if ($quote) {
                        return "'" . addcslashes($this->variables->get($matches[1]), "'") . "'";
                    }

                    return $this->variables->get($matches[1]);
                }

                throw new DomainException(sprintf(
                    'Undefined variable $%s in report %s on line %d for record %s in expression: %s',
                    $matches[1],
                    $this->report,
                    $this->currentReportLine(),
                    $this->currentRecordXref(),
                    $expression,
                ));
            },
            $expression
        );
    }

    /**
     * Best-effort line number in the original report XML.
     *
     * When we are inside a sub-fragment parse, the active XMLReader is
     * looking at an in-memory copy of the captured inner XML, so its line
     * counter restarts at 1.  We add the original source line where each
     * enclosing repeat block began to give a number that points roughly at
     * the right place in the source file.
     */
    private function currentReportLine(): int
    {
        $line = $this->currentLineNumber();

        $offset = $this->repeat_line;
        foreach ($this->repeats_stack as $frame) {
            $offset += $frame->repeat_line;
        }

        return $line + $offset;
    }

    /**
     * Push the current repeat-loop state onto the stack so that nested
     * <RepeatTag>, <Facts>, <List> or <Relatives> blocks can restore it
     * when they finish iterating.
     */
    private function pushRepeatFrame(): void
    {
        $this->repeats_stack[] = new RepeatFrame(
            repeats:     $this->repeats,
            repeat_xml:  $this->repeat_xml,
            repeat_line: $this->repeat_line,
        );
    }

    /**
     * Restore the repeat-loop state saved by the matching
     * {@see pushRepeatFrame()} call.
     */
    private function popRepeatFrame(): void
    {
        $frame             = array_pop($this->repeats_stack);
        $this->repeats     = $frame->repeats;
        $this->repeat_xml  = $frame->repeat_xml;
        $this->repeat_line = $frame->repeat_line;
    }

    /**
     * Find the XREF of the current record being processed.
     *
     * Checks the current gedrec first, then walks the gedrec_stack
     * to find the nearest ancestor record with an XREF.
     */
    private function currentRecordXref(): string
    {
        if (preg_match('/^0 @(.+)@/', $this->gedrec, $match)) {
            return $match[1];
        }

        // Walk the stack from most recent to oldest
        for ($i = count($this->gedrec_stack) - 1; $i >= 0; $i--) {
            if (preg_match('/^0 @(.+)@/', $this->gedrec_stack[$i]->gedrec, $match)) {
                return $match[1];
            }
        }

        return '(unknown)';
    }
}
