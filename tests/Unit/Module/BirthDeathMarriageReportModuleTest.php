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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Report\Cell;
use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Report\Footnote;
use Fisharebest\Webtrees\Report\Image;
use Fisharebest\Webtrees\Report\Line;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\AbstractRenderer;
use Fisharebest\Webtrees\Report\Text;
use Fisharebest\Webtrees\Report\TextBox;
use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\CellNewline;
use Fisharebest\Webtrees\Report\ExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\FootnoteTextsElement;
use Fisharebest\Webtrees\Report\GedcomFrame;
use Fisharebest\Webtrees\Report\GedcomTextReader;
use Fisharebest\Webtrees\Report\HexColor;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\ImageContinuation;
use Fisharebest\Webtrees\Report\InputDefinition;
use Fisharebest\Webtrees\Report\NewPageElement;
use Fisharebest\Webtrees\Report\NullElement;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\ParserGenerate;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;
use Fisharebest\Webtrees\Report\ParserSetup;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\PlaceholderExpander;
use Fisharebest\Webtrees\Report\RepeatFrame;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\ListBuilder;
use Fisharebest\Webtrees\Report\Section;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TcLibPdfAdaptor;
use Fisharebest\Webtrees\Report\VariableTable;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Fisharebest\Webtrees\Module\BirthDeathMarriageReportModule;
use Fisharebest\Webtrees\Module\PedigreeReportModule;

#[CoversClass(Cell::class)]
#[CoversClass(Element::class)]
#[CoversClass(Footnote::class)]
#[CoversClass(Image::class)]
#[CoversClass(Line::class)]
#[CoversClass(AbstractParser::class)]
#[CoversClass(AbstractRenderer::class)]
#[CoversClass(Text::class)]
#[CoversClass(TextBox::class)]
#[CoversClass(CellAlign::class)]
#[CoversClass(CellNewline::class)]
#[CoversClass(ExpressionLanguageProvider::class)]
#[CoversClass(FootnoteTextsElement::class)]
#[CoversClass(GedcomFrame::class)]
#[CoversClass(GedcomTextReader::class)]
#[CoversClass(HexColor::class)]
#[CoversClass(HtmlRenderer::class)]
#[CoversClass(ImageContinuation::class)]
#[CoversClass(InputDefinition::class)]
#[CoversClass(NewPageElement::class)]
#[CoversClass(NullElement::class)]
#[CoversClass(PageOrientation::class)]
#[CoversClass(PaperSize::class)]
#[CoversClass(ParserGenerate::class)]
#[CoversClass(ParserSetup::class)]
#[CoversClass(PdfRenderer::class)]
#[CoversClass(PedigreeReportModule::class)]
#[CoversClass(PlaceholderExpander::class)]
#[CoversClass(RepeatFrame::class)]
#[CoversClass(Config::class)]
#[CoversClass(ListBuilder::class)]
#[CoversClass(Section::class)]
#[CoversClass(Style::class)]
#[CoversClass(TcLibPdfAdaptor::class)]
#[CoversClass(VariableTable::class)]
class BirthDeathMarriageReportModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return array<int,array<string,string>>
     */
    public static function reportOptions(): array
    {
        return [
            [
                'name'       => '',
                'bdmplace'   => '',
                'birthdate1' => '01 JAN 1900',
                'birthdate2' => '31 DEC 1999',
                'deathdate1' => '',
                'deathdate2' => '',
                'sortby'     => 'BIRT:DATE',
                'page_size'  => 'A4',
            ],
            [
                'name'       => '',
                'bdmplace'   => '',
                'birthdate1' => '',
                'birthdate2' => '',
                'deathdate1' => '01 JAN 1900',
                'deathdate2' => '31 DEC 1999',
                'sortby'     => 'DEAT:DATE',
                'page_size'  => 'US-Letter',
            ],
            [
                'name'       => 'Windsor',
                'bdmplace'   => 'England',
                'birthdate1' => '',
                'birthdate2' => '',
                'deathdate1' => '',
                'deathdate2' => '',
                'sortby'     => '',
                'page_size'  => 'A4',
            ],
            [
                'name'       => '',
                'bdmplace'   => '',
                'birthdate1' => '',
                'birthdate2' => '',
                'deathdate1' => '',
                'deathdate2' => '',
                'sortby'     => '',
                'page_size'  => '',
            ],
        ];
    }

    #[DataProvider('reportOptions')]
    public function testReportRunsWithoutError(
        string $name,
        string $bdmplace,
        string $birthdate1,
        string $birthdate2,
        string $deathdate1,
        string $deathdate2,
        string $sortby,
        string $page_size,
    ): void {
        $tree   = $this->importTree('demo.ged');
        $module = new BirthDeathMarriageReportModule();
        $module->setName('bdm_report');

        $xml  = 'resources/' . $module->xmlFilename();
        $vars = [
            'name'       => $name,
            'bdmplace'   => $bdmplace,
            'birthdate1' => $birthdate1,
            'birthdate2' => $birthdate2,
            'deathdate1' => $deathdate1,
            'deathdate2' => $deathdate2,
            'sortby'     => $sortby,
            'pageSize'   => $page_size,
        ];

        $parser = new ParserSetup($xml);
        $parser->process();
        $this->assertNotEmpty($parser->reportDescription());
        $this->assertNotEmpty($parser->reportTitle());
        $this->assertNotEmpty($parser->reportInputs());

        Site::setPreference('INDEX_DIRECTORY', 'tests/data/');

        $renderer = new HtmlRenderer();
        (new ParserGenerate($xml, $renderer, $vars, $tree, Webtrees::NAME . ' ' . Webtrees::VERSION, Registry::timestampFactory()->now()))->process();
        $html = $renderer->output();
        self::assertStringStartsWith('<', $html);
        self::assertStringEndsWith('>', $html);

        $renderer = new PdfRenderer();
        (new ParserGenerate($xml, $renderer, $vars, $tree, Webtrees::NAME . ' ' . Webtrees::VERSION, Registry::timestampFactory()->now()))->process();
        $pdf = $renderer->output();
        self::assertStringStartsWith('%PDF', $pdf);
        self::assertStringEndsWith("%%EOF\n", $pdf);
    }
}
