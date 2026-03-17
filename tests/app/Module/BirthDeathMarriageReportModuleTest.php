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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Report\AbstractRenderer;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\ReportBaseCell;
use Fisharebest\Webtrees\Report\ReportBaseElement;
use Fisharebest\Webtrees\Report\ReportBaseFootnote;
use Fisharebest\Webtrees\Report\ReportBaseImage;
use Fisharebest\Webtrees\Report\ReportBaseLine;
use Fisharebest\Webtrees\Report\ReportBaseText;
use Fisharebest\Webtrees\Report\ReportBaseTextBox;
use Fisharebest\Webtrees\Report\ReportExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\ReportHtmlCell;
use Fisharebest\Webtrees\Report\ReportHtmlFootnote;
use Fisharebest\Webtrees\Report\ReportHtmlImage;
use Fisharebest\Webtrees\Report\ReportHtmlLine;
use Fisharebest\Webtrees\Report\ReportHtmlText;
use Fisharebest\Webtrees\Report\ReportHtmlTextBox;
use Fisharebest\Webtrees\Report\ReportParserBase;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Report\ReportParserSetup;
use Fisharebest\Webtrees\Report\ReportPdfCell;
use Fisharebest\Webtrees\Report\ReportPdfFootnote;
use Fisharebest\Webtrees\Report\ReportPdfImage;
use Fisharebest\Webtrees\Report\ReportPdfLine;
use Fisharebest\Webtrees\Report\ReportPdfText;
use Fisharebest\Webtrees\Report\ReportPdfTextBox;
use Fisharebest\Webtrees\Report\TcpdfWrapper;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(PedigreeReportModule::class)]
#[CoversClass(AbstractRenderer::class)]
#[CoversClass(HtmlRenderer::class)]
#[CoversClass(PdfRenderer::class)]
#[CoversClass(ReportBaseCell::class)]
#[CoversClass(ReportBaseElement::class)]
#[CoversClass(ReportBaseFootnote::class)]
#[CoversClass(ReportBaseImage::class)]
#[CoversClass(ReportBaseLine::class)]
#[CoversClass(ReportBaseText::class)]
#[CoversClass(ReportBaseTextBox::class)]
#[CoversClass(ReportExpressionLanguageProvider::class)]
#[CoversClass(ReportHtmlCell::class)]
#[CoversClass(ReportHtmlFootnote::class)]
#[CoversClass(ReportHtmlImage::class)]
#[CoversClass(ReportHtmlLine::class)]
#[CoversClass(ReportHtmlText::class)]
#[CoversClass(ReportHtmlTextBox::class)]
#[CoversClass(ReportParserBase::class)]
#[CoversClass(ReportParserGenerate::class)]
#[CoversClass(ReportParserSetup::class)]
#[CoversClass(ReportPdfCell::class)]
#[CoversClass(ReportPdfFootnote::class)]
#[CoversClass(ReportPdfImage::class)]
#[CoversClass(ReportPdfLine::class)]
#[CoversClass(ReportPdfText::class)]
#[CoversClass(ReportPdfTextBox::class)]
#[CoversClass(TcpdfWrapper::class)]
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

        $parser = new ReportParserSetup($xml);
        $this->assertNotEmpty($parser->reportDescription());
        $this->assertNotEmpty($parser->reportTitle());
        $this->assertNotEmpty($parser->reportInputs());

        Site::setPreference('INDEX_DIRECTORY', 'tests/data/');

        ob_start();
        new ReportParserGenerate($xml, new HtmlRenderer(), $vars, $tree);
        $html = ob_get_clean();
        self::assertIsString($html);
        self::assertStringStartsWith('<', $html);
        self::assertStringEndsWith('>', $html);

        ob_start();
        new ReportParserGenerate($xml, new PdfRenderer(), $vars, $tree);
        $pdf = ob_get_clean();
        self::assertIsString($pdf);
        self::assertStringStartsWith('%PDF', $pdf);
        self::assertStringEndsWith("%%EOF\n", $pdf);
    }
}
