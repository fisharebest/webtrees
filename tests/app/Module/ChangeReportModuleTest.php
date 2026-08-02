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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function ob_get_clean;

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
class ChangeReportModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return array<int,array<string,string>>
     */
    public static function reportOptions(): array
    {
        return [
            [
                'change_range_start' => '01 JAN 2020',
                'change_range_end'   => '31 DEC 2030',
                'pending'            => 'yes',
                'sortby'             => 'CHAN',
                'page_size'          => 'A4',
                'pageorient'         => 'landscape',
            ],
            [
                'change_range_start' => '01 JAN 2020',
                'change_range_end'   => '31 DEC 2030',
                'pending'            => 'no',
                'sortby'             => 'NAME',
                'page_size'          => 'US-Letter',
                'pageorient'         => 'portrait',
            ],
            [
                'change_range_start' => '01 JAN 2020',
                'change_range_end'   => '31 DEC 2030',
                'pending'            => 'yes',
                'sortby'             => 'BIRT:DATE',
                'page_size'          => 'A4',
                'pageorient'         => 'landscape',
            ],
            [
                'change_range_start' => '',
                'change_range_end'   => '',
                'pending'            => '',
                'sortby'             => '',
                'page_size'          => '',
                'pageorient'         => '',
            ],
        ];
    }

    #[DataProvider('reportOptions')]
    public function testReportRunsWithoutError(
        string $change_range_start,
        string $change_range_end,
        string $pending,
        string $sortby,
        string $page_size,
        string $pageorient,
    ): void {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree   = $this->importTree('demo.ged');
        $module = new ChangeReportModule();
        $module->setName('change_report');

        $xml  = 'resources/' . $module->xmlFilename();
        $vars = [
            'changeRangeStart' => $change_range_start,
            'changeRangeEnd'   => $change_range_end,
            'pending'          => $pending,
            'sortby'           => $sortby,
            'pageSize'         => $page_size,
            'pageorient'       => $pageorient,
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
