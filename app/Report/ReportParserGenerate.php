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

use DomainException;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Factories\MarkdownFactory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use LogicException;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use XMLParser;

use function addcslashes;
use function addslashes;
use function array_pop;
use function array_shift;
use function count;
use function end;
use function explode;
use function file;
use function file_exists;
use function getimagesize;
use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function in_array;
use function ltrim;
use function method_exists;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function reset;
use function round;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strip_tags;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;
use function trim;
use function uasort;
use function xml_error_string;
use function xml_get_current_line_number;
use function xml_get_error_code;
use function xml_parse;
use function xml_parser_create;
use function xml_parser_set_option;
use function xml_set_character_data_handler;
use function xml_set_element_handler;

use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;
use const XML_OPTION_CASE_FOLDING;

class ReportParserGenerate extends ReportParserBase
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

    private int $repeat_bytes = 0;

    /** @var array<string> Repeated data when iterating over loops */
    private array $repeats = [];

    /** @var array<int,array{0:array<int,string>,1:int}> Nested repeating data */
    private array $repeats_stack = [];

    /** @var array<AbstractRenderer> Nested repeating data */
    private array $wt_report_stack = [];

    // Nested repeating data
    private XMLParser $parser;

    /** @var XMLParser[] Nested repeating data */
    private array $parser_stack = [];

    private string $gedrec = '';

    /** @var array<int,array{0:string,1:string,2:string}> Nested GEDCOM records */
    private array $gedrec_stack = [];

    private ReportBaseElement $current_element;

    private ReportBaseElement $footnote_element;

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

    /** @var string The filename of the XML report */
    protected string $report;

    private AbstractRenderer $renderer;

    // It's not clear what this variable does.
    private ReportBaseElement|AbstractRenderer $wt_report;

    /** @var array<string,string> Variables defined in the report at run-time */
    private array $vars;

    private Tree $tree;

    public function __construct(string $report, AbstractRenderer $renderer, array $vars, Tree $tree)
    {
        $this->report          = $report;
        $this->renderer        = $renderer;
        $this->wt_report       = $renderer;
        $this->current_element = new ReportBaseElement();
        $this->vars            = $vars;
        $this->tree            = $tree;

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
     * @param array<string,string> $attrs
     */
    protected function startElement(XMLParser $parser, string $name, array $attrs): void
    {
        $newattrs = [];

        foreach ($attrs as $key => $value) {
            if (preg_match("/^\\$(\w+)$/", $value, $match)) {
                if (isset($this->vars[$match[1]])) {
                    $value = $this->vars[$match[1]];
                }
            }
            $newattrs[$key] = $value;
        }
        $attrs = $newattrs;
        if ($this->process_footnote && ($this->process_ifs === 0 || $name === 'if') && ($this->process_gedcoms === 0 || $name === 'Gedcom') && ($this->process_repeats === 0 || $name === 'Facts' || $name === 'RepeatTag')) {
            $method = $name . 'StartHandler';

            if (method_exists($this, $method)) {
                $this->{$method}($attrs);
            }
        }
    }

    protected function endElement(XMLParser $parser, string $name): void
    {
        if (($this->process_footnote || $name === 'Footnote') && ($this->process_ifs === 0 || $name === 'if') && ($this->process_gedcoms === 0 || $name === 'Gedcom') && ($this->process_repeats === 0 || $name === 'Facts' || $name === 'RepeatTag' || $name === 'List' || $name === 'Relatives')) {
            $method = $name . 'EndHandler';

            if (method_exists($this, $method)) {
                $this->{$method}();
            }
        }
    }

    protected function characterData(XMLParser $parser, string $data): void
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

        $style = [
            'name'  => $attrs['name'],
            'font'  => $attrs['font'] ?? $this->wt_report->default_font,
            'size'  => (float) ($attrs['size'] ?? $this->wt_report->default_font_size),
            'style' => $attrs['style'] ?? '',
        ];

        $this->wt_report->addStyle($style);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function docStartHandler(array $attrs): void
    {
        $this->parser = $this->xml_parser;

        $this->wt_report->page_width        = (float) ($attrs['customwidth'] ?? $this->wt_report->page_width);
        $this->wt_report->page_height       = (float) ($attrs['customheight'] ?? $this->wt_report->page_height);
        $this->wt_report->left_margin       = (float) ($attrs['leftmargin'] ?? $this->wt_report->left_margin);
        $this->wt_report->right_margin      = (float) ($attrs['rightmargin'] ?? $this->wt_report->right_margin);
        $this->wt_report->top_margin        = (float) ($attrs['topmargin'] ?? $this->wt_report->top_margin);
        $this->wt_report->bottom_margin     = (float) ($attrs['bottommargin'] ?? $this->wt_report->bottom_margin);
        $this->wt_report->header_margin     = (float) ($attrs['headermargin'] ?? $this->wt_report->header_margin);
        $this->wt_report->footer_margin     = (float) ($attrs['footermargin'] ?? $this->wt_report->footer_margin);
        $this->wt_report->orientation       = $attrs['orientation'] ?? $this->wt_report->orientation;
        $this->wt_report->page_format       = $attrs['pageSize'] ?? $this->wt_report->page_format;
        $this->wt_report->show_generated_by = (bool) ($attrs['showGeneratedBy'] ?? $this->wt_report->show_generated_by);
        $this->wt_report->setup();
    }

    protected function docEndHandler(): void
    {
        $this->wt_report->run();
    }

    protected function headerStartHandler(): void
    {
        $this->wt_report->setProcessing('H');
    }

    protected function bodyStartHandler(): void
    {
        $this->wt_report->setProcessing('B');
    }

    protected function footerStartHandler(): void
    {
        $this->wt_report->setProcessing('F');
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function cellStartHandler(array $attrs): void
    {
        $align   = strtoupper($attrs['align'] ?? '');
        $bgcolor = $attrs['bgcolor'] ?? '';
        $bocolor = $attrs['bocolor'] ?? '';
        $border  = $attrs['border'] ?? '';
        $fill    = (bool) ($attrs['fill'] ?? false);
        $height  = (float) ($attrs['height'] ?? 0.0);
        $left    = (float) ($attrs['left'] ?? ReportBaseElement::CURRENT_POSITION);
        $ln      = (int) ($attrs['newline'] ?? 0);
        $reseth  = (bool) ($attrs['reseth'] ?? true);
        $stretch = (int) ($attrs['stretch'] ?? 0);
        $style   = $attrs['style'] ?? '';
        $tcolor  = $attrs['tcolor'] ?? '';
        $top     = (float) ($attrs['top'] ?? ReportBaseElement::CURRENT_POSITION);
        $width   = (float) ($attrs['width'] ?? 0.0);

        $align = strtr($align, [
            'leftrtl'  => $this->wt_report->rtl ? 'R' : 'L',
            'rightrtl' => $this->wt_report->rtl ? 'L' : 'R',
            'left'     => 'L',
            'right'    => 'R',
            'center'   => 'C',
            'justify'  => 'J',
        ]);

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

    protected function cellEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);
        $this->wt_report->addElement($this->current_element);
    }

    protected function nowStartHandler(): void
    {
        $this->current_element->addText(Registry::timestampFactory()->now()->isoFormat('LLLL'));
    }

    protected function pageNumStartHandler(): void
    {
        $this->current_element->addText('#PAGENUM#');
    }

    protected function totalPagesStartHandler(): void
    {
        $this->current_element->addText('{{:ptp:}}');
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
            $this->gedrec_stack[] = [$this->gedrec, $this->fact, $this->desc];
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
            [$this->gedrec, $this->fact, $this->desc] = array_pop($this->gedrec_stack);
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
        $left      = (float) ($attrs['left'] ?? ReportBaseElement::CURRENT_POSITION);
        $newline   = (bool) ($attrs['newline'] ?? false);
        $padding   = (bool) ($attrs['pagecheck'] ?? true);
        $pagecheck = (bool) ($attrs['pagecheck'] ?? false);
        $reseth    = (bool) ($attrs['reseth'] ?? false);
        $top       = (float) ($attrs['top'] ?? ReportBaseElement::CURRENT_POSITION);
        $width     = (float) ($attrs['width'] ?? 0.0);
        $style     = '';

        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = false;

        $this->wt_report_stack[] = $this->wt_report;
        $this->wt_report         = $this->renderer->createTextBox(
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
        $this->print_data      = array_pop($this->print_data_stack);
        $this->current_element = $this->wt_report;

        $this->wt_report = array_pop($this->wt_report_stack);
        $this->wt_report->addElement($this->current_element);
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function textStartHandler(array $attrs): void
    {
        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = true;

        $style = $attrs['style'] ?? '';
        $color = $attrs['color'] ?? '';

        $this->current_element = $this->renderer->createText($style, $color);
    }

    protected function textEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);
        $this->wt_report->addElement($this->current_element);
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
            if (isset($this->vars[$match[1]])) {
                $id = $this->vars[$match[1]];
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

        $this->repeats_stack[] = [$this->repeats, $this->repeat_bytes];
        $this->repeats         = [];
        $this->repeat_bytes    = xml_get_current_line_number($this->parser);

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

        // Check if there is anything to repeat
        if (count($this->repeats) > 0) {
            // No need to load them if not used...

            $lineoffset = 0;
            foreach ($this->repeats_stack as $rep) {
                $lineoffset += $rep[1];
            }
            //-- read the xml from the file
            $lines = file($this->report);
            while (!str_contains($lines[$lineoffset + $this->repeat_bytes], '<RepeatTag')) {
                $lineoffset--;
            }
            $lineoffset++;
            $reportxml = "<tempdoc>\n";
            $line_nr   = $lineoffset + $this->repeat_bytes;
            // RepeatTag Level counter
            $count = 1;
            while (0 < $count) {
                if (str_contains($lines[$line_nr], '<RepeatTag')) {
                    $count++;
                } elseif (str_contains($lines[$line_nr], '</RepeatTag')) {
                    $count--;
                }
                if (0 < $count) {
                    $reportxml .= $lines[$line_nr];
                }
                $line_nr++;
            }
            // No need to drag this
            unset($lines);
            $reportxml .= "</tempdoc>\n";
            // Save original values
            $this->parser_stack[] = $this->parser;
            $oldgedrec            = $this->gedrec;
            foreach ($this->repeats as $gedrec) {
                $this->gedrec  = $gedrec;
                $repeat_parser = xml_parser_create();
                $this->parser  = $repeat_parser;
                xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, 0);

                xml_set_element_handler(
                    $repeat_parser,
                    function ($parser, string $name, array $attrs): void {
                        $this->startElement($parser, $name, $attrs);
                    },
                    function ($parser, string $name): void {
                        $this->endElement($parser, $name);
                    }
                );

                xml_set_character_data_handler(
                    $repeat_parser,
                    function ($parser, string $data): void {
                        $this->characterData($parser, $data);
                    }
                );

                if (!xml_parse($repeat_parser, $reportxml, true)) {
                    throw new DomainException(sprintf(
                        'RepeatTagEHandler XML error: %s at line %d',
                        xml_error_string(xml_get_error_code($repeat_parser)),
                        xml_get_current_line_number($repeat_parser)
                    ));
                }
            }
            // Restore original values
            $this->gedrec = $oldgedrec;
            $this->parser = array_pop($this->parser_stack);
        }
        [$this->repeats, $this->repeat_bytes] = array_pop($this->repeats_stack);
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
            throw new DomainException('REPORT ERROR var: The attribute "var=" is missing or not set in the XML file on line: ' . xml_get_current_line_number($this->parser));
        }

        $var = $attrs['var'];
        // SetVar element preset variables
        if (!empty($this->vars[$var])) {
            $var = $this->vars[$var];
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
        $this->text = $var; // Used for title/descriptio
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

        $this->repeats_stack[] = [$this->repeats, $this->repeat_bytes];
        $this->repeats         = [];
        $this->repeat_bytes    = xml_get_current_line_number($this->parser);

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
            $tag = $this->vars[$match[1]];
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

        // Check if there is anything to repeat
        if (count($this->repeats) > 0) {
            $line       = xml_get_current_line_number($this->parser) - 1;
            $lineoffset = 0;
            foreach ($this->repeats_stack as $rep) {
                $lineoffset += $rep[1];
            }

            // Read the XML from the file
            $lines = file($this->report);
            while ($lineoffset + $this->repeat_bytes > 0 && !str_contains($lines[$lineoffset + $this->repeat_bytes], '<Facts ')) {
                $lineoffset--;
            }
            $lineoffset++;
            $reportxml = "<tempdoc>\n";
            $i         = $line + $lineoffset;
            $line_nr   = $this->repeat_bytes + $lineoffset;
            while ($line_nr < $i) {
                $reportxml .= $lines[$line_nr];
                $line_nr++;
            }
            // No need to drag this
            unset($lines);
            $reportxml .= "</tempdoc>\n";
            // Save original values
            $this->parser_stack[] = $this->parser;
            $oldgedrec            = $this->gedrec;
            $count                = count($this->repeats);
            $i                    = 0;
            while ($i < $count) {
                $this->gedrec = $this->repeats[$i];
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
                $repeat_parser = xml_parser_create();
                $this->parser  = $repeat_parser;
                xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, 0);

                xml_set_element_handler(
                    $repeat_parser,
                    function ($parser, string $name, array $attrs): void {
                        $this->startElement($parser, $name, $attrs);
                    },
                    function ($parser, string $name): void {
                        $this->endElement($parser, $name);
                    }
                );

                xml_set_character_data_handler(
                    $repeat_parser,
                    function ($parser, string $data): void {
                        $this->characterData($parser, $data);
                    }
                );

                if (!xml_parse($repeat_parser, $reportxml, true)) {
                    throw new DomainException(sprintf(
                        'FactsEHandler XML error: %s at line %d',
                        xml_error_string(xml_get_error_code($repeat_parser)),
                        xml_get_current_line_number($repeat_parser)
                    ));
                }

                $i++;
            }
            // Restore original values
            $this->parser = array_pop($this->parser_stack);
            $this->gedrec = $oldgedrec;
        }
        [$this->repeats, $this->repeat_bytes] = array_pop($this->repeats_stack);
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
        if (preg_match("/\\$(\w+)/", $name, $match)) {
            $name = $this->vars["'" . $match[1] . "'"];
        }
        $count = preg_match_all("/\\$(\w+)/", $value, $match, PREG_SET_ORDER);
        $i     = 0;
        while ($i < $count) {
            $t     = $this->vars[$match[$i][1]];
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
            $expression_provider  = new ReportExpressionLanguageProvider();
            $expression_cache     = new NullAdapter();
            $expression_language  = new ExpressionLanguage($expression_cache, [$expression_provider]);

            $value = (string) $expression_language->evaluate($value);
        }

        if (str_contains($value, '@')) {
            $value = '';
        }
        $this->vars[$name] = $value;
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
        $expression_provider  = new ReportExpressionLanguageProvider();
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
            $style                    = '';
            if (!empty($attrs['style'])) {
                $style = $attrs['style'];
            }
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
                $this->wt_report->addElement($this->current_element);
            }
            $this->current_element = $this->footnote_element;
        } else {
            $this->process_footnote = true;
        }
    }

    protected function footnoteTextsStartHandler(): void
    {
        $this->wt_report->addElement('footnotetexts');
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

        $align  = $attrs['align'] ?? '';
        $height = (float) ($attrs['height'] ?? 0.0);
        $left   = (float) ($attrs['left'] ?? ReportBaseElement::CURRENT_POSITION);
        $ln     = $attrs['ln'] ?? 'T';
        $top    = (float) ($attrs['top'] ?? ReportBaseElement::CURRENT_POSITION);
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
            $this->wt_report->addElement($image);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function imageStartHandler(array $attrs): void
    {
        $align  = $attrs['align'] ?? '';
        $file   = $attrs['file'] ?? '';
        $height = (float) ($attrs['height'] ?? 0.0);
        $left   = (float) ($attrs['left'] ?? ReportBaseElement::CURRENT_POSITION);
        $ln     = $attrs['ln'] ?? 'T';
        $top    = (float) ($attrs['top'] ?? ReportBaseElement::CURRENT_POSITION);
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
                    $this->wt_report->addElement($image);
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
            $this->wt_report->addElement($image);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function lineStartHandler(array $attrs): void
    {
        $x1 = (float) ($attrs['x1'] ?? ReportBaseElement::CURRENT_POSITION);
        $x2 = (float) ($attrs['x2'] ?? ReportBaseElement::CURRENT_POSITION);
        $y1 = (float) ($attrs['y1'] ?? ReportBaseElement::CURRENT_POSITION);
        $y2 = (float) ($attrs['y2'] ?? ReportBaseElement::CURRENT_POSITION);

        $line = $this->renderer->createLine($x1, $y1, $x2, $y2);
        $this->wt_report->addElement($line);
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
                $sortby = $this->vars[$match[1]];
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
                            $val = $this->vars[$match[1]];
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

        $this->repeats_stack[] = [$this->repeats, $this->repeat_bytes];
        $this->repeat_bytes    = xml_get_current_line_number($this->parser) + 1;
    }

    protected function listEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        // Check if there is any list
        if (count($this->list) > 0) {
            $lineoffset = 0;
            foreach ($this->repeats_stack as $rep) {
                $lineoffset += $rep[1];
            }
            //-- read the xml from the file
            $lines = file($this->report);
            while ((!str_contains($lines[$lineoffset + $this->repeat_bytes], '<List')) && (($lineoffset + $this->repeat_bytes) > 0)) {
                $lineoffset--;
            }
            $lineoffset++;
            $reportxml = "<tempdoc>\n";
            $line_nr   = $lineoffset + $this->repeat_bytes;
            // List Level counter
            $count = 1;
            while (0 < $count) {
                if (str_contains($lines[$line_nr], '<List')) {
                    $count++;
                } elseif (str_contains($lines[$line_nr], '</List')) {
                    $count--;
                }
                if (0 < $count) {
                    $reportxml .= $lines[$line_nr];
                }
                $line_nr++;
            }
            // No need to drag this
            unset($lines);
            $reportxml .= '</tempdoc>';
            // Save original values
            $this->parser_stack[] = $this->parser;
            $oldgedrec            = $this->gedrec;

            $this->list_total   = count($this->list);
            $this->list_private = 0;
            foreach ($this->list as $record) {
                if ($record->canShow()) {
                    $this->gedrec = $record->privatizeGedcom(Auth::accessLevel($record->tree()));
                    //-- start the sax parser
                    $repeat_parser = xml_parser_create();
                    $this->parser  = $repeat_parser;
                    xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, 0);

                    xml_set_element_handler(
                        $repeat_parser,
                        function ($parser, string $name, array $attrs): void {
                            $this->startElement($parser, $name, $attrs);
                        },
                        function ($parser, string $name): void {
                            $this->endElement($parser, $name);
                        }
                    );

                    xml_set_character_data_handler(
                        $repeat_parser,
                        function ($parser, string $data): void {
                            $this->characterData($parser, $data);
                        }
                    );

                    if (!xml_parse($repeat_parser, $reportxml, true)) {
                        throw new DomainException(sprintf(
                            'ListEHandler XML error: %s at line %d',
                            xml_error_string(xml_get_error_code($repeat_parser)),
                            xml_get_current_line_number($repeat_parser)
                        ));
                    }
                } else {
                    $this->list_private++;
                }
            }
            $this->list   = [];
            $this->parser = array_pop($this->parser_stack);
            $this->gedrec = $oldgedrec;
        }
        [$this->repeats, $this->repeat_bytes] = array_pop($this->repeats_stack);
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
            $sortby = $this->vars[$match[1]];
            $sortby = trim($sortby);
        }

        $maxgen = (int) ($attrs['maxgen'] ?? -1);
        $group  = $attrs['group'] ?? 'child-family';

        if (preg_match("/\\$(\w+)/", $group, $match)) {
            $group = $this->vars[$match[1]];
            $group = trim($group);
        }

        $id = $attrs['id'] ?? '';

        if (preg_match("/\\$(\w+)/", $id, $match)) {
            $id = $this->vars[$match[1]];
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
        $this->repeats_stack[] = [$this->repeats, $this->repeat_bytes];
        $this->repeat_bytes    = xml_get_current_line_number($this->parser) + 1;
    }

    protected function relativesEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        // Check if there is any relatives
        if (count($this->list) > 0) {
            $lineoffset = 0;
            foreach ($this->repeats_stack as $rep) {
                $lineoffset += $rep[1];
            }
            //-- read the xml from the file
            $lines = file($this->report);
            while (!str_contains($lines[$lineoffset + $this->repeat_bytes], '<Relatives') && $lineoffset + $this->repeat_bytes > 0) {
                $lineoffset--;
            }
            $lineoffset++;
            $reportxml = "<tempdoc>\n";
            $line_nr   = $lineoffset + $this->repeat_bytes;
            // Relatives Level counter
            $count = 1;
            while (0 < $count) {
                if (str_contains($lines[$line_nr], '<Relatives')) {
                    $count++;
                } elseif (str_contains($lines[$line_nr], '</Relatives')) {
                    $count--;
                }
                if (0 < $count) {
                    $reportxml .= $lines[$line_nr];
                }
                $line_nr++;
            }
            // No need to drag this
            unset($lines);
            $reportxml .= "</tempdoc>\n";
            // Save original values
            $this->parser_stack[] = $this->parser;
            $oldgedrec            = $this->gedrec;

            $this->list_total   = count($this->list);
            $this->list_private = 0;
            foreach ($this->list as $xref => $value) {
                if (isset($value->generation)) {
                    $this->generation = $value->generation;
                }
                $tmp          = Registry::gedcomRecordFactory()->make((string) $xref, $this->tree);
                $this->gedrec = $tmp->privatizeGedcom(Auth::accessLevel($this->tree));

                $repeat_parser = xml_parser_create();
                $this->parser  = $repeat_parser;
                xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, 0);

                xml_set_element_handler(
                    $repeat_parser,
                    function ($parser, string $name, array $attrs): void {
                        $this->startElement($parser, $name, $attrs);
                    },
                    function ($parser, string $name): void {
                        $this->endElement($parser, $name);
                    }
                );

                xml_set_character_data_handler(
                    $repeat_parser,
                    function ($parser, string $data): void {
                        $this->characterData($parser, $data);
                    }
                );

                if (!xml_parse($repeat_parser, $reportxml, true)) {
                    throw new DomainException(sprintf('RelativesEHandler XML error: %s at line %d', xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser)));
                }
            }
            // Clean up the list array
            $this->list   = [];
            $this->parser = array_pop($this->parser_stack);
            $this->gedrec = $oldgedrec;
        }
        [$this->repeats, $this->repeat_bytes] = array_pop($this->repeats_stack);
    }

    protected function generationStartHandler(): void
    {
        $this->current_element->addText(I18N::number($this->generation));
    }

    protected function newPageStartHandler(): void
    {
        $this->wt_report->addElement('addpage');
    }

    protected function titleEndHandler(): void
    {
        $this->renderer->addTitle($this->text);
    }

    protected function descriptionEndHandler(): void
    {
        $this->renderer->addDescription($this->text);
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
            function (array $matches) use ($quote): string {
                if (isset($this->vars[$matches[1]])) {
                    if ($quote) {
                        return "'" . addcslashes($this->vars[$matches[1]], "'") . "'";
                    }

                    return $this->vars[$matches[1]];
                }

                throw new DomainException(sprintf('Undefined variable $%s in report', $matches[1]));

                return '$' . $matches[1];
            },
            $expression
        );
    }
}
