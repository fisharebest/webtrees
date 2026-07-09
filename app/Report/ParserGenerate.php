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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Comparators\GedcomRecordComparator;
use Fisharebest\Webtrees\Comparators\IndividualComparator;
use Fisharebest\Webtrees\Contracts\TimestampInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\RomanNumeralsService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use LogicException;
use Throwable;

use function array_key_exists;
use function array_pop;
use function array_shift;
use function basename;
use function count;
use function explode;
use function file_exists;
use function file_get_contents;
use function getimagesize;
use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function in_array;
use function is_numeric;
use function pathinfo;
use function preg_match;
use function preg_match_all;
use function round;
use function sprintf;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strip_tags;
use function strtolower;
use function strlen;
use function trim;
use function uasort;

use const PATHINFO_EXTENSION;
use const PREG_SET_ORDER;

final class ParserGenerate extends AbstractParser
{
    private const string DEFAULT_FONT = 'dejavusans';

    // Millimeters to points (1 point = 1/72 inch).
    private const float MM_TO_POINTS = 72.0 / 25.4;

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
     * Captured inner XML of the currently open repeat block.  Set by the
     * start handler (via XMLReader::readInnerXml()) and consumed by the
     * matching end handler, which reparses it once per iteration through
     * {@see AbstractParser::parseFragment()}.
     */
    private string $repeat_xml = '';

    /** @var array<RepeatFrame> Snapshots of the loop state captured when nesting <RepeatTag>, <Facts>, <List> or <Relatives>. */
    private array $repeats_stack = [];

    /** @var array<DocumentBuilder|TextBox> Stack of containers when nesting text boxes */
    private array $container_stack = [];


    private string $gedrec = '';

    /** @var array<GedcomFrame> Snapshots of the GEDCOM-record state captured when nesting <Gedcom>. */
    private array $gedrec_stack = [];

    private Element $current_element;

    private Element $footnote_element;

    private string $fact = '';

    private string $desc = '';

    private string $type = '';

    private int $generation = 1;

    /** @var array<GedcomRecord> Source data for processing lists */
    private array $list = [];

    /** Number of items in lists */
    private int $list_total = 0;

    /** Number of items filtered from lists */
    private int $list_private = 0;

    /** Report title, captured from the <Title> element */
    private string $report_title = '';

    /** Report description, captured from the <Description> element */
    private string $report_description = '';

    private readonly OutputInterface&SetupInterface&ConfigProviderInterface $renderer;

    private readonly DocumentAcceptorInterface $document_acceptor;

    private readonly StyleConsumerInterface $style_consumer;

    private readonly ElementFactoryInterface $element_factory;

    private readonly DocumentBuilder $report_document_builder;

    /** The current target for addElement() — root builder or a nested text box. */
    private DocumentBuilder|TextBox $current_container;

    /** Variables defined in the report at run-time, seeded from the setup form. */
    private readonly VariableTable $variables;

    /** Resolves $variable, @token and I18N placeholders in attribute values and expressions. */
    private readonly PlaceholderExpander $expander;

    private readonly StyleRepository $style_repository;

    private Document|null $report_document = null;

    /**
     * @param array<string,string> $vars Initial variable bindings from the report setup form.
     */
    public function __construct(
        string $report,
        OutputInterface&SetupInterface&ConfigProviderInterface&ElementFactoryInterface&DocumentAcceptorInterface&StyleConsumerInterface $renderer,
        array $vars,
        private readonly Tree $tree,
        private readonly string $author,
        private readonly TimestampInterface $timestamp,
        private readonly bool $compression = true,
        private readonly bool $font_subsetting = true,
    ) {
        $this->renderer          = $renderer;
        $this->element_factory   = $renderer;
        $this->document_acceptor = $renderer;
        $this->style_consumer    = $renderer;
        $this->report_document_builder = new DocumentBuilder();
        $this->current_container = $this->report_document_builder;
        $this->current_element   = new NullElement();
        $this->variables         = new VariableTable($vars);
        $this->expander          = new PlaceholderExpander($this->variables);
        $this->style_repository  = new StyleRepository();

        parent::__construct($report);
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
            'GeneratedBy'      => $this->generatedByStartHandler(...),
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
            'WebtreesLogo'     => $this->webtreesLogoStartHandler(...),
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
            'GeneratedBy'      => $this->noop(...),
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
            'WebtreesLogo'     => $this->noop(...),
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
        foreach ($attrs as $key => $value) {
            if (preg_match('/^[$]([a-z_][a-z_0-9]*)$/i', $value, $match) && $this->variables->has($match[1])) {
                $value = $this->variables->get($match[1]);
            }

            $attrs[$key] = $value;
        }

        if ($this->gateAllowsStart($name)) {
            try {
                parent::startElement($name, $attrs);
            } catch (Throwable $exception) {
                throw $this->addContextToException($exception, $name);
            }
        }
    }

    protected function endElement(string $name): void
    {
        if ($this->gateAllowsEnd($name)) {
            try {
                parent::endElement($name);
            } catch (Throwable $exception) {
                throw $this->addContextToException($exception, $name);
            }
        }
    }

    protected function characterData(string $data): void
    {
        if ($this->print_data && $this->gateAllowsCharacterData()) {
            $this->current_element->addText($data);
        }
    }

    /**
     * While skipping content inside <Footnote>, <if>, <Gedcom>, or a
     * repeat block, only the tag(s) that can close or re-enter those
     * scopes are dispatched.  Gates are checked in priority order.
     */
    private function gateAllowsStart(string $name): bool
    {
        if (!$this->process_footnote) {
            return false;
        }
        if ($this->process_ifs !== 0 && $name !== 'if') {
            return false;
        }
        if ($this->process_gedcoms !== 0 && $name !== 'Gedcom') {
            return false;
        }
        if ($this->process_repeats !== 0 && $name !== 'Facts' && $name !== 'RepeatTag') {
            return false;
        }

        return true;
    }

    /**
     * Mirror of gateAllowsStart() for closing tags.  The end tag that
     * closes a gate scope must always pass through so the handler can
     * decrement the counter and re-enable normal dispatch.
     */
    private function gateAllowsEnd(string $name): bool
    {
        if (!$this->process_footnote && $name !== 'Footnote') {
            return false;
        }
        if ($this->process_ifs !== 0 && $name !== 'if') {
            return false;
        }
        if ($this->process_gedcoms !== 0 && $name !== 'Gedcom') {
            return false;
        }
        if (
            $this->process_repeats !== 0 &&
            $name !== 'Facts' &&
            $name !== 'RepeatTag' &&
            $name !== 'List' &&
            $name !== 'Relatives'
        ) {
            return false;
        }

        return true;
    }

    /** Character data is only processed when no gate is active. */
    private function gateAllowsCharacterData(): bool
    {
        return $this->process_gedcoms === 0
            && $this->process_ifs === 0
            && $this->process_repeats === 0;
    }

    /**
     * @param array<string,string> $attrs
     */
    private function styleStartHandler(array $attrs): void
    {
        $style = Style::fromXmlAttributes($attrs);

        $this->style_repository->add($style);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function docStartHandler(array $attrs): void
    {
        $page_size = PaperSize::tryFrom($attrs['pageSize'] ?? 'A4') ?? PaperSize::A4;

        // Resolve page dimensions: use custom dimensions if specified, otherwise look up the paper size.
        $page_width  = (float) ($attrs['customwidth'] ?? 0.0);
        $page_height = (float) ($attrs['customheight'] ?? 0.0);

        if ($page_width === 0.0 || $page_height === 0.0) {
            $page_width  = $page_size->width();
            $page_height = $page_size->height();
        }

        $config = new Config(
            paper_width: $page_width,
            paper_height: $page_height,
            left_margin: (float) ($attrs['leftmargin'] ?? 20.0 * self::MM_TO_POINTS),
            right_margin: (float) ($attrs['rightmargin'] ?? 25.0 * self::MM_TO_POINTS),
            top_margin: (float) ($attrs['topmargin'] ?? 20.0 * self::MM_TO_POINTS),
            bottom_margin: (float) ($attrs['bottommargin'] ?? 25.0 * self::MM_TO_POINTS),
            header_margin: (float) ($attrs['headermargin'] ?? 5.0 * self::MM_TO_POINTS),
            footer_margin: (float) ($attrs['footermargin'] ?? 10.0 * self::MM_TO_POINTS),
            orientation: PageOrientation::from($attrs['orientation'] ?? 'portrait'),
            paper_size: $page_size,
            rtl: I18N::direction() === 'rtl',
            // I18N: This is a report footer. %s is the name of the application.
            generated_by: I18N::translate('Generated by %s', $this->author),
            author: $this->author,
            title: $this->report_title,
            description: $this->report_description,
            align_rtl: I18N::direction() === 'rtl' ? 'right' : 'left',
            entity_rtl: I18N::direction() === 'rtl' ? '&rlm;' : '&lrm;',
            font: $this->variables->has('font') ? $this->variables->get('font') : self::DEFAULT_FONT,
            timestamp: $this->timestamp,
            font_subsetting: $this->font_subsetting,
            compression: $this->compression,
        );

        $this->renderer->setup($config);

        // Make these config variables available to the document
        $this->variables->set('PAPER_SIZE', $config->paper_size->value);
        $this->variables->set('PAPER_WIDTH', (string) $config->paper_width);
        $this->variables->set('PAPER_HEIGHT', (string) $config->paper_height);
        $this->variables->set('LEFT_MARGIN', (string) $config->left_margin);
        $this->variables->set('RIGHT_MARGIN', (string) $config->right_margin);
        $this->variables->set('TOP_MARGIN', (string) $config->top_margin);
        $this->variables->set('BOTTOM_MARGIN', (string) $config->bottom_margin);
        $this->variables->set('HEADER_MARGIN', (string) $config->header_margin);
        $this->variables->set('FOOTER_MARGIN', (string) $config->footer_margin);
        $this->variables->set('ORIENTATION', $config->orientation->value);
        $this->variables->set('AUTHOR', $config->author);
        $this->variables->set('TITLE', $config->title);
        $this->variables->set('DESCRIPTION', $config->description);
        if ($config->orientation === PageOrientation::Portrait) {
            $this->variables->set('PAGE_WIDTH', (string) ($config->paper_width - $config->left_margin - $config->right_margin));
            $this->variables->set('PAGE_HEIGHT', (string) ($config->paper_height - $config->top_margin - $config->bottom_margin));
        } else {
            $this->variables->set('PAGE_WIDTH', (string) ($config->paper_height - $config->top_margin - $config->bottom_margin));
            $this->variables->set('PAGE_HEIGHT', (string) ($config->paper_width - $config->left_margin - $config->right_margin));
        }
    }

    private function docEndHandler(): void
    {
        $report_document = $this->report_document_builder->reportDocument($this->report_title);
        $this->report_document = $report_document;

        foreach ($this->style_repository->all() as $style) {
            $this->style_consumer->addStyle($style);
        }

        $this->document_acceptor->applyDocument($report_document);
    }

    public function reportDocument(): Document|null
    {
        return $this->report_document;
    }

    private function headerStartHandler(): void
    {
        $this->report_document_builder->setProcessing(Section::Header);
    }

    private function bodyStartHandler(): void
    {
        $this->report_document_builder->setProcessing(Section::Body);
    }

    private function footerStartHandler(): void
    {
        $this->report_document_builder->setProcessing(Section::Footer);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function cellStartHandler(array $attrs): void
    {
        $background_color = $attrs['bgcolor'] ?? '';
        $border_color     = $attrs['bocolor'] ?? '';
        $border           = $attrs['border'] ?? '';
        $height           = (float) ($attrs['height'] ?? 0.0);
        $left         = (float) ($attrs['left'] ?? Element::CURRENT_POSITION);
        $newline      = CellNewline::tryFrom((int) ($attrs['newline'] ?? 0)) ?? CellNewline::Right;
        $style            = $this->style_repository->get($attrs['style'] ?? '');
        $text_color       = $attrs['tcolor'] ?? '';
        $top              = (float) ($attrs['top'] ?? Element::CURRENT_POSITION);
        $align            = $this->parseCellAlign($attrs['align'] ?? '');
        $width            = (float) ($attrs['width'] ?? 0.0);

        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = true;

        $this->current_element = $this->element_factory->createCell(
            $width,
            $height,
            $border,
            $align,
            $background_color,
            $style,
            $newline,
            $top,
            $left,
            $border_color,
            $text_color,
        );
    }

    private function parseCellAlign(string $value): CellAlign
    {
        return match ($value) {
            'center'   => CellAlign::Center,
            'left'     => CellAlign::Left,
            'leftrtl'  => $this->renderer->reportConfig()->rtl ? CellAlign::Right : CellAlign::Left,
            'right'    => CellAlign::Right,
            'rightrtl' => $this->renderer->reportConfig()->rtl ? CellAlign::Left : CellAlign::Right,
            default    => CellAlign::None,
        };
    }

    private function cellEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);
        $this->current_container->addElement($this->current_element);
    }

    private function nowStartHandler(): void
    {
        $this->current_element->addText($this->timestamp->isoFormat('LLLL'));
    }

    private function generatedByStartHandler(): void
    {
        $this->current_element->addText($this->renderer->reportConfig()->generated_by);
    }

    /**
     * Handle <WebtreesLogo /> — embed the webtrees logo as a clickable image
     * linking to https://webtrees.net.  Aspect ratio is 4:1.
     *
     * Uses SVG for HTML output and PNG for PDF output, since tc-lib-pdf
     * does not support SVG images.
     *
     * @param array<string,string> $attrs
     */
    private function webtreesLogoStartHandler(array $attrs): void
    {
        $default_width  = 80.0;
        $default_height = 20.0;

        $has_width  = isset($attrs['width']);
        $has_height = isset($attrs['height']);

        if ($has_width && $has_height) {
            $width  = (float) $attrs['width'];
            $height = (float) $attrs['height'];
        } elseif ($has_width) {
            $width  = (float) $attrs['width'];
            $height = $width * $default_height / $default_width;
        } elseif ($has_height) {
            $height = (float) $attrs['height'];
            $width  = $height * $default_width / $default_height;
        } else {
            $width  = $default_width;
            $height = $default_height;
        }

        // SVG for HTML (sharp at any size), PNG for PDF (tc-lib-pdf lacks SVG support).
        if ($this->renderer instanceof HtmlRenderer) {
            $logo_path = Webtrees::ROOT_DIR . 'resources/img/webtrees-logo.svg';
            $mime_type = 'image/svg+xml';
        } else {
            $logo_path = Webtrees::ROOT_DIR . 'resources/img/webtrees-logo.png';
            $mime_type = 'image/png';
        }

        $logo_data = file_get_contents($logo_path);

        if ($logo_data === false) {
            throw new LogicException('Cannot read webtrees logo: ' . $logo_path);
        }

        $image = $this->element_factory->createImage(
            $mime_type,
            $logo_data,
            Element::CURRENT_POSITION,
            Element::CURRENT_POSITION,
            $width,
            $height,
            CellAlign::Left,
            ImageContinuation::SameLine,
        );

        $url = route(TreePage::class, ['tree' => $this->tree->name()]);

        $this->current_container->addElement($image->withLink($url));
    }

    private function pageNumStartHandler(): void
    {
        $this->current_element->addPageNumber();
    }

    private function totalPagesStartHandler(): void
    {
        $this->current_element->addTotalPages();
    }

    /**
     * @param array<string,string> $attrs
     */
    private function gedcomStartHandler(array $attrs): void
    {
        if ($this->process_gedcoms > 0) {
            $this->process_gedcoms++;

            return;
        }

        $gedcom_path     = str_replace('@fact', $this->fact, $attrs['id']);
        $path_segments   = explode(':', $gedcom_path);
        $resolved_gedcom = '';
        if (count($path_segments) < 2) {
            $record          = Registry::gedcomRecordFactory()->make($attrs['id'], $this->tree);
            $resolved_gedcom = $record ? $record->privatizeGedcom(Auth::accessLevel($this->tree)) : '';
        }

        if ($resolved_gedcom === '') {
            $current_gedcom  = $this->gedrec;
            foreach ($path_segments as $path_segment) {
                if (preg_match('/\$(.+)/', $path_segment, $match)) {
                    $record          = Registry::gedcomRecordFactory()->make($match[1], $this->tree);
                    $resolved_gedcom = $record ? $record->privatizeGedcom(Auth::accessLevel($this->tree)) : '';
                } elseif (preg_match('/@(.+)/', $path_segment, $match)) {
                    $link_match = [];
                    if (preg_match("/\d $match[1] @([^@]+)@/", $current_gedcom, $link_match)) {
                        $record          = Registry::gedcomRecordFactory()->make($link_match[1], $this->tree);
                        $resolved_gedcom = $record ? $record->privatizeGedcom(Auth::accessLevel($this->tree)) : '';
                        $current_gedcom  = $resolved_gedcom;
                    } else {
                        $resolved_gedcom = '';
                        break;
                    }
                } else {
                    $level           = 1 + (int) explode(' ', trim($current_gedcom))[0];
                    $resolved_gedcom = GedcomTextReader::getSubRecord($level, "$level $path_segment", $current_gedcom);
                    $current_gedcom  = $resolved_gedcom;
                }
            }
        }

        if ($resolved_gedcom !== '') {
            $this->gedrec_stack[] = new GedcomFrame(
                gedrec: $this->gedrec,
                fact:   $this->fact,
                desc:   $this->desc,
            );
            $this->gedrec         = $resolved_gedcom;
            if (preg_match("/(\d+) (_?[A-Z0-9]+) (.*)/", $this->gedrec, $match)) {
                $this->fact = $match[2];
                $this->desc = trim($match[3]);
            }
        } else {
            $this->process_gedcoms++;
        }
    }

    private function gedcomEndHandler(): void
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
    private function textBoxStartHandler(array $attrs): void
    {
        $background_color = $attrs['bgcolor'] ?? '';
        $border           = (bool) ($attrs['border'] ?? false);
        $height           = (float) ($attrs['height'] ?? 0.0);
        $left             = (float) ($attrs['left'] ?? Element::CURRENT_POSITION);
        $newline          = (bool) ($attrs['newline'] ?? false);
        $padding          = (bool) ($attrs['padding'] ?? true);
        $check_page_break = (bool) ($attrs['pagecheck'] ?? false);
        $reset_height     = (bool) ($attrs['reseth'] ?? false);
        $top              = (float) ($attrs['top'] ?? Element::CURRENT_POSITION);
        $width            = (float) ($attrs['width'] ?? 0.0);

        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = false;

        $this->container_stack[] = $this->current_container;
        $this->current_container         = $this->element_factory->createTextBox(
            $width,
            $height,
            $border,
            $background_color,
            $newline,
            $left,
            $top,
            $check_page_break,
            $padding,
            $reset_height,
        );
    }

    private function textBoxEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);

        // The text box we were building becomes an element to add to the parent container.
        assert($this->current_container instanceof TextBox);
        $this->current_element = $this->current_container;

        $this->current_container = array_pop($this->container_stack);
        $this->current_container->addElement($this->current_element);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function textStartHandler(array $attrs): void
    {
        $this->print_data_stack[] = $this->print_data;
        $this->print_data         = true;

        $style    = $this->style_repository->get($attrs['style'] ?? '');
        $color    = $attrs['color'] ?? '';
        $truncate = (float) ($attrs['truncate'] ?? 0);

        $this->current_element = $this->element_factory->createText($style, $color, $truncate);
    }

    private function textEndHandler(): void
    {
        $this->print_data = array_pop($this->print_data_stack);
        $this->current_container->addElement($this->current_element);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function getPersonNameStartHandler(array $attrs): void
    {
        $id    = '';
        $match = [];
        if (!array_key_exists('id', $attrs) || $attrs['id'] === '') {
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
        if ($id !== '') {
            $record = Registry::gedcomRecordFactory()->make($id, $this->tree);
            if ($record === null) {
                return;
            }
            if ($record->canShowName()) {
                $name = $record->fullName();
                $name = strip_tags($name);
                $addname = $record->alternateName() ?? '';
                $addname = strip_tags($addname);
                if ($addname !== '') {
                    $name .= ' ' . $addname;
                }

                // Names are user data and may have a different direction to the page.
                $bidi_name = UTF8::FIRST_STRONG_ISOLATE . trim($name) . UTF8::POP_DIRECTIONAL_ISOLATE;

                // When inside a Text element, split it so the link only wraps
                // the name — not any preceding or following text in the same element.
                if ($this->current_element instanceof Text) {
                    // Flush any text accumulated before the name as a separate element.
                    if ($this->current_element->getValue() !== '') {
                        $this->current_container->addElement($this->current_element);
                        $this->current_element = $this->element_factory->createText(
                            $this->current_element->style,
                            $this->current_element->color,
                            0.0,
                        );
                    }

                    // Emit the name as its own linked Text element.
                    $name_element = $this->element_factory->createText(
                        $this->current_element->style,
                        $this->current_element->color,
                        $this->current_element->truncate,
                    );
                    $name_element->addText($bidi_name);
                    $name_element->url = $record->url();
                    $this->current_container->addElement($name_element);

                    // Continue with a fresh element for any text that follows.
                    $this->current_element = $this->element_factory->createText(
                        $this->current_element->style,
                        $this->current_element->color,
                        0.0,
                    );
                } else {
                    $this->current_element->addText($bidi_name);
                    // Set URL on Cell elements to make the cell a link.
                    if ($this->current_element instanceof Cell) {
                        $this->current_element->url = $record->url();
                    }
                }
            } else {
                $this->current_element->addText(I18N::translate('Private'));
            }
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    private function gedcomValueStartHandler(array $attrs): void
    {
        $id    = '';
        $match = [];
        if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
            $id = $match[1];
        }

        $tag = $attrs['tag'] ?? '';

        if ($tag === '') {
            throw new LogicException('The "tag" attribute is missing.');
        }

        if ($tag === '@desc') {
            // User data may have a different ltr/rtl direction to the page.
            $this->current_element->addText(UTF8::FIRST_STRONG_ISOLATE . $this->desc . UTF8::POP_DIRECTIONAL_ISOLATE);
        } elseif ($tag === '@id') {
            $this->current_element->addText($id);
        } else {
            $tag = str_replace('@fact', $this->fact, $tag);
            if (array_key_exists('level', $attrs)) {
                if (!is_numeric($attrs['level'])) {
                    throw new LogicException('The "level" attribute is not numeric.');
                }

                $level = (int) $attrs['level'];
            } else {
                $level = (int) explode(' ', trim($this->gedrec))[0];
                if ($level === 0) {
                    $level++;
                }
            }

            $value = GedcomTextReader::getGedcomValue($tag, $level, $this->gedrec, $this->tree);

            if ($tag === 'DATE' || str_ends_with($tag, ':DATE')) {
                $value = strip_tags((new Date($value))->display());
                // Some translations have NBSP between the epoch and the year.
                $value = strtr($value, ['&nbsp;' => "\u{a0}"]);
            }

            if ($tag === 'PLAC' || str_ends_with($tag, ':PLAC')) {
                $value = strip_tags((new Place($value, $this->tree))->shortName());
            }


            // User data may have a different ltr/rtl direction to the page.
            // FSI/PDI isolation prevents adjacent reversed-direction segments
            // from merging and keeps weak characters with their logical segment.
            $this->current_element->addText(UTF8::FIRST_STRONG_ISOLATE . $value . UTF8::POP_DIRECTIONAL_ISOLATE);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    private function repeatTagStartHandler(array $attrs): void
    {
        $this->process_repeats++;
        if ($this->process_repeats > 1) {
            return;
        }

        $this->pushRepeatFrame();
        $this->repeats         = [];
        $this->repeat_xml      = $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();

        $tag = $attrs['tag'] ?? '';

        if ($tag === '') {
            throw new LogicException('The "tag" attribute is missing.');
        }

        if ($tag === '@desc') {
            $this->current_element->addText($this->desc);
        } else {
            $tag_path = str_replace('@fact', $this->fact, $tag);
            $tag_segments = explode(':', $tag_path);
            $level = (int) explode(' ', trim($this->gedrec))[0];
            if ($level === 0) {
                $level++;
            }
            $subrecord = $this->gedrec;
            $current_tag = $tag_path;
            $tag_count = count($tag_segments);
            $tag_index = 0;
            while ($tag_index < $tag_count) {
                $current_tag = $tag_segments[$tag_index];
                if ($current_tag !== '') {
                    if ($tag_index < ($tag_count - 1)) {
                        $subrecord = GedcomTextReader::getSubRecord($level, "$level $current_tag", $subrecord);
                        if ($subrecord === '') {
                            $level--;
                            $subrecord = GedcomTextReader::getSubRecord($level, "@ $current_tag", $this->gedrec);
                            if ($subrecord === '') {
                                return;
                            }
                        }
                    }
                    $level++;
                }
                $tag_index++;
            }
            $level--;
            $subrecord_count = preg_match_all("/$level $current_tag(.*)/", $subrecord, $match, PREG_SET_ORDER);
            $subrecord_index = 0;
            while ($subrecord_index < $subrecord_count) {
                $subrecord_index++;
                // Privacy check - is this a link, and are we allowed to view the linked object?
                $repeat_subrecord = GedcomTextReader::getSubRecord(
                    $level,
                    "$level $current_tag",
                    $subrecord,
                    $subrecord_index,
                );
                if (
                    preg_match(
                        '/^\d ' . Gedcom::REGEX_TAG . ' @(' . Gedcom::REGEX_XREF . ')@/',
                        $repeat_subrecord,
                        $xref_match
                    )
                ) {
                    $linked_object = Registry::gedcomRecordFactory()->make($xref_match[1], $this->tree);
                    if ($linked_object && !$linked_object->canShow()) {
                        continue;
                    }
                }
                $this->repeats[] = $repeat_subrecord;
            }
        }
    }

    private function repeatTagEndHandler(): void
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
                $this->parseFragment($fragment);
            }

            $this->gedrec = $oldgedrec;
        }

        $this->popRepeatFrame();
    }

    /**
     * Variable lookup
     * Retrieve predefined variables:
     * @ desc GEDCOM fact description, example:
     *        1 EVEN This is a description
     * @ fact GEDCOM fact tag, such as BIRT, DEAT etc.
     * $ I18N::translate('....')
     * $ language_settings[]
     *
     * @param array<string,string> $attrs
     */
    private function varStartHandler(array $attrs): void
    {
        $variable_value = $attrs['var'] ?? null;

        if ($variable_value === null) {
            throw new LogicException('The "var" attribute is missing.');
        }

        if ($this->variables->has($variable_value)) {
            $variable_value = $this->variables->get($variable_value);
        } else {
            $fact_label = $this->fact;
            if (($this->fact === 'EVEN' || $this->fact === 'FACT') && $this->type !== '') {
                $fact_label = $this->type;
            } else {
                foreach ([Individual::RECORD_TYPE, Family::RECORD_TYPE] as $record_type) {
                    $element = Registry::elementFactory()->make($record_type . ':' . $this->fact);

                    if (!$element instanceof UnknownElement) {
                        $fact_label = $element->label();
                        break;
                    }
                }
            }

            $variable_value = strtr($variable_value, ['@desc' => $this->desc, '@fact' => $fact_label]);
            $variable_value = $this->expander->applyI18nFunctions($variable_value);
        }
        if (isset($attrs['format'])) {
            $roman_numerals_service = new RomanNumeralsService();

            $variable_value = match ($attrs['format']) {
                'date'   => strip_tags((new Date($variable_value))->display()),
                'number' => I18N::number((int) $variable_value),
                'roman'  => strtolower($roman_numerals_service->numberToRomanNumerals((int) $variable_value)),
                default  => throw new LogicException(sprintf('Unknown format "%s" for <var>.', $attrs['format'])),
            };
        }
        $this->current_element->addText($variable_value);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function factsStartHandler(array $attrs): void
    {
        $this->process_repeats++;
        if ($this->process_repeats > 1) {
            return;
        }

        $this->pushRepeatFrame();
        $this->repeats         = [];
        $this->repeat_xml      = $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();

        $record_id = '';
        $match = [];
        if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
            $record_id = $match[1];
        }
        $ignored_tags = '';
        if (isset($attrs['ignore'])) {
            $ignored_tags .= $attrs['ignore'];
        }
        if (preg_match('/\$(.+)/', $ignored_tags, $match)) {
            $ignored_tags = $this->variables->get($match[1]);
        }

        $record = Registry::gedcomRecordFactory()->make($record_id, $this->tree);

        if ($record instanceof GedcomRecord) {
            if (isset($attrs['diff']) && $attrs['diff'] === 'true') {
                foreach ($record->facts() as $fact) {
                    if (
                        ($fact->isPendingAddition() || $fact->isPendingDeletion()) &&
                        !str_ends_with($fact->tag(), ':CHAN')
                    ) {
                        $this->repeats[] = $fact->gedcom();
                    }
                }
            } else {
                $facts = $record->facts([], true);
                $this->repeats = [];
                $ignored_fact_tags = explode(',', $ignored_tags);
                foreach ($facts as $fact) {
                    $fact_tag = explode(':', $fact->tag())[1];

                    if (!in_array($fact_tag, $ignored_fact_tags, true)) {
                        $this->repeats[] = $fact->gedcom();
                    }
                }
            }
        }
    }

    private function factsEndHandler(): void
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
                    $this->desc .= GedcomTextReader::getCont(2, $this->gedrec);
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
    private function setVarStartHandler(array $attrs): void
    {
        if (!array_key_exists('name', $attrs)) {
            throw new LogicException('The "name" attribute is missing.');
        }

        if ($attrs['name'] === '') {
            throw new LogicException('The "name" attribute is empty.');
        }

        if (!array_key_exists('value', $attrs)) {
            throw new LogicException('The "value" attribute is missing.');
        }

        $name  = $attrs['name'];
        $value = $this->expander->resolveSetVarValue(
            $attrs['value'],
            $this->gedrec,
            $this->fact,
            $this->desc,
            $this->generation,
        );

        $this->variables->set($name, $value);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function ifStartHandler(array $attrs): void
    {
        if ($this->process_ifs > 0) {
            $this->process_ifs++;

            return;
        }

        $result = $this->expander->evaluateCondition(
            $attrs['condition'],
            $this->gedrec,
            $this->fact,
            $this->desc,
            $this->generation,
            $this->tree,
        );

        if (!$result) {
            $this->process_ifs++;
        }
    }

    private function ifEndHandler(): void
    {
        if ($this->process_ifs > 0) {
            $this->process_ifs--;
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    private function footnoteStartHandler(array $attrs): void
    {
        $id = '';
        if (preg_match('/[0-9] (.+) @(.+)@/', $this->gedrec, $match)) {
            $id = $match[2];
        }
        $record = Registry::gedcomRecordFactory()->make($id, $this->tree);
        if ($record && $record->canShow()) {
            $this->print_data_stack[] = $this->print_data;
            $this->print_data         = true;
            $style                    = $this->style_repository->get($attrs['style'] ?? 'footnote');
            $this->footnote_element = $this->current_element;
            $this->current_element  = $this->element_factory->createFootnote($style);
        } else {
            $this->print_data       = false;
            $this->process_footnote = false;
        }
    }

    private function footnoteEndHandler(): void
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

    private function footnoteTextsStartHandler(): void
    {
        $this->current_container->addElement(new FootnoteTextsElement());
    }

    private function brStartHandler(): void
    {
        if ($this->print_data && $this->process_gedcoms === 0) {
            $this->current_element->addText('<br>');
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    private function highlightedImageStartHandler(array $attrs): void
    {
        $id = '';
        if (preg_match('/0 @(.+)@/', $this->gedrec, $match)) {
            $id = $match[1];
        }

        $align  = $this->parseCellAlign($attrs['align'] ?? '');
        $height = (float) ($attrs['height'] ?? 0.0);
        $left   = (float) ($attrs['left'] ?? Element::CURRENT_POSITION);
        $line_continuation = ImageContinuation::tryFrom($attrs['ln'] ?? 'T') ?? ImageContinuation::SameLine;
        $top    = (float) ($attrs['top'] ?? Element::CURRENT_POSITION);
        $width  = (float) ($attrs['width'] ?? 0.0);

        $person     = Registry::individualFactory()->make($id, $this->tree);
        $media_file = $person->findHighlightedMediaFile();

        if ($media_file instanceof MediaFile && $media_file->fileExists()) {
            $gd_image = imagecreatefromstring($media_file->fileContents());
            if ($gd_image === false) {
                throw new LogicException(sprintf('Cannot decode highlighted media image for record: %s', $id));
            }

            if ($width === 0.0 && $height === 0.0) {
                throw new LogicException(sprintf('Need height, width or both for images'));
            }

            if ($height === 0.0) {
                $height = $width * imagesy($gd_image)  / imagesx($gd_image);
            }

            if ($width === 0.0) {
                $width = $height * imagesx($gd_image) / imagesy($gd_image);
            }

            $image = $this->element_factory->createImageFromObject(
                $media_file,
                $left,
                $top,
                $width,
                $height,
                $align,
                $line_continuation,
            );

            $this->current_container->addElement($image);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    private function imageStartHandler(array $attrs): void
    {
        $align  = $this->parseCellAlign($attrs['align'] ?? '');
        $file   = $attrs['file'] ?? '';
        $height = (float) ($attrs['height'] ?? 0.0);
        $left   = (float) ($attrs['left'] ?? Element::CURRENT_POSITION);
        $line_continuation = ImageContinuation::tryFrom($attrs['ln'] ?? 'T') ?? ImageContinuation::SameLine;
        $top    = (float) ($attrs['top'] ?? Element::CURRENT_POSITION);
        $width  = (float) ($attrs['width'] ?? 0.0);

        if ($file === '@FILE') {
            $match = [];
            if (preg_match("/\d OBJE @(.+)@/", $this->gedrec, $match)) {
                $mediaobject = Registry::mediaFactory()->make($match[1], $this->tree);
                $media_file  = $mediaobject->firstImageFile();

                if ($media_file instanceof MediaFile && $media_file->fileExists()) {
                    $gd_image = imagecreatefromstring($media_file->fileContents());
                    if ($gd_image === false) {
                        throw new LogicException(sprintf('Cannot decode media image for object: %s', $match[1]));
                    }

                    if ($width === 0.0 && $height === 0.0) {
                        throw new LogicException(sprintf('Need height, width or both for images'));
                    }

                    if ($height === 0.0) {
                        $height = round(imagesy($gd_image) * $width / imagesx($gd_image));
                    }

                    if ($width === 0.0) {
                        $width = round(imagesx($gd_image) * $height / imagesy($gd_image));
                    }

                    $image = $this->element_factory->createImageFromObject(
                        $media_file,
                        $left,
                        $top,
                        $width,
                        $height,
                        $align,
                        $line_continuation,
                    );

                    $this->current_container->addElement($image);
                }
            }
        } elseif (file_exists($file)) {
            $mime_type = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png'         => 'image/png',
                'gif'         => 'image/gif',
                default       => null,
            };
            if ($mime_type === null) {
                return;
            }

            $data = file_get_contents($file);
            if ($data === false) {
                throw new LogicException(sprintf('Cannot read image file: %s', $file));
            }

            $size = getimagesize($file);
            if ($size === false) {
                throw new LogicException(sprintf('Cannot determine image size: %s', $file));
            }

            if ($width > 0 && $height === 0.0) {
                $height      = $width * $size[1] / $size[0];
            } elseif ($height > 0 && $width === 0.0) {
                $width       = $height * $size[0] / $size[1];
            } else {
                $width  = $size[0];
                $height = $size[1];
            }
            $image = $this->element_factory->createImage($mime_type, $data, $left, $top, $width, $height, $align, $line_continuation);
            $this->current_container->addElement($image);
        }
    }


    /**
     * @param array<string,string> $attrs
     */
    private function lineStartHandler(array $attrs): void
    {
        $x1 = (float) ($attrs['x1'] ?? Element::CURRENT_POSITION);
        $x2 = (float) ($attrs['x2'] ?? Element::CURRENT_POSITION);
        $y1 = (float) ($attrs['y1'] ?? Element::CURRENT_POSITION);
        $y2 = (float) ($attrs['y2'] ?? Element::CURRENT_POSITION);

        $line = $this->element_factory->createLine($x1, $y1, $x2, $y2);
        $this->current_container->addElement($line);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function listStartHandler(array $attrs): void
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

        $list_builder = new ListBuilder($this->tree);
        $this->list = match ($listname) {
            'pending'    => $list_builder->buildChangeList($attrs, $sortby, $this->gedrec, $this->fact, $this->desc, $this->variables),
            'individual' => $list_builder->buildIndividualList($attrs, $sortby, $this->expander->substituteVars(...), $this->gedrec, $this->fact, $this->desc, $this->variables),
            'family'     => $list_builder->buildFamilyList($attrs, $sortby, $this->expander->substituteVars(...), $this->gedrec, $this->fact, $this->desc, $this->variables),
            default      => throw new LogicException('Invalid list name: ' . $listname),
        };

        $this->pushRepeatFrame();
        $this->repeat_xml      = $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();
    }

    private function listEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

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
    private function listTotalStartHandler(): void
    {
        if ($this->list_private === 0) {
            $this->current_element->addText((string) $this->list_total);
        } else {
            $this->current_element->addText(($this->list_total - $this->list_private) . ' / ' . $this->list_total);
        }
    }

    /**
     * @param array<string,string> $attrs
     */
    private function relativesStartHandler(array $attrs): void
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

        $individual_list = [];

        $person     = Registry::individualFactory()->make($id, $this->tree);
        if ($person instanceof Individual) {
            $individual_list[$id] = $person;
            switch ($group) {
                case 'child-family':
                    foreach ($person->childFamilies() as $family) {
                        foreach ($family->spouses() as $spouse) {
                            $individual_list[$spouse->xref()] = $spouse;
                        }

                        foreach ($family->children() as $child) {
                            $individual_list[$child->xref()] = $child;
                        }
                    }
                    break;
                case 'spouse-family':
                    foreach ($person->spouseFamilies() as $family) {
                        foreach ($family->spouses() as $spouse) {
                            $individual_list[$spouse->xref()] = $spouse;
                        }

                        foreach ($family->children() as $child) {
                            $individual_list[$child->xref()] = $child;
                        }
                    }
                    break;
                case 'direct-ancestors':
                    $this->addAncestors($individual_list, $id, false, $maxgen);
                    break;
                case 'ancestors':
                    $this->addAncestors($individual_list, $id, true, $maxgen);
                    break;
                case 'descendants':
                    $individual_list[$id]->generation = 1;
                    $this->addDescendancy($individual_list, $id, false, $maxgen);
                    break;
                case 'all':
                    $this->addAncestors($individual_list, $id, true, $maxgen);
                    $this->addDescendancy($individual_list, $id, true, $maxgen);
                    break;
            }
        }

        switch ($sortby) {
            case 'NAME':
                uasort($individual_list, GedcomRecordComparator::byName(...));
                break;
            case 'BIRT:DATE':
                uasort($individual_list, IndividualComparator::byBirthDate(...));
                break;
            case 'DEAT:DATE':
                uasort($individual_list, IndividualComparator::byDeathDate(...));
                break;
            case 'generation':
                uasort($individual_list, static fn ($x, $y): int => $x->generation <=> $y->generation);
                break;
            default:
                break;
        }

        $this->list = $individual_list;

        $this->pushRepeatFrame();
        $this->repeat_xml      = $this->xml_reader->readInnerXml();
        $this->repeat_line     = $this->currentLineNumber();
    }

    private function relativesEndHandler(): void
    {
        $this->process_repeats--;
        if ($this->process_repeats > 0) {
            return;
        }

        if (count($this->list) > 0) {
            $fragment  = '<tempdoc>' . $this->repeat_xml . '</tempdoc>';
            $oldgedrec = $this->gedrec;

            $this->list_total   = count($this->list);
            $this->list_private = 0;
            foreach ($this->list as $xref => $value) {
                if (isset($value->generation)) {
                    $this->generation = $value->generation;
                }
                $record        = Registry::gedcomRecordFactory()->make((string) $xref, $this->tree);
                $this->gedrec  = $record->privatizeGedcom(Auth::accessLevel($this->tree));

                $this->parseFragment($fragment);
            }
            $this->list   = [];
            $this->gedrec = $oldgedrec;
        }
        $this->popRepeatFrame();
    }

    private function generationStartHandler(): void
    {
        $this->current_element->addText(I18N::number($this->generation));
    }

    private function newPageStartHandler(): void
    {
        $this->current_container->addElement(new NewPageElement());
    }

    private function titleStartHandler(): void
    {
        $this->current_element = new NullElement();
    }

    private function titleEndHandler(): void
    {
        $this->report_title = $this->current_element->getValue();
    }

    private function descriptionStartHandler(): void
    {
        $this->current_element = new NullElement();
    }

    private function descriptionEndHandler(): void
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
                $list[$child->xref()] = $child;

                if (isset($list[$pid]->generation)) {
                    $list[$child->xref()]->generation = $list[$pid]->generation + 1;
                } else {
                    $list[$child->xref()]->generation = 2;
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

    private function currentRecordXref(): string
    {
        if (preg_match('/^0 (@.+@)/', $this->gedrec, $match)) {
            return $match[1];
        }

        // Walk the stack from most recent to oldest
        foreach (array_reverse($this->gedrec_stack) as $frame) {
            if (preg_match('/^0 (@.+@)/', $frame->gedrec, $match)) {
                return $match[1];
            }
        }

        return '@unknown@';
    }

    /**
     * Add context to a parse error.
     */
    private function addContextToException(Throwable $exception, string $element): LogicException
    {
        $message = sprintf(
            '%s (%s:%d, <%s>, %s)',
            $exception->getMessage(),
            basename($this->report),
            $this->currentReportLine(),
            $element,
            $this->currentRecordXref(),
        );

        return new LogicException($message, 0, $exception);
    }
}
