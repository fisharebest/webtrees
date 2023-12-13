<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Report\ReportParserSetup;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

use function ob_get_clean;

/**
 * Test harness for the class FactSourcesReportModule
 */
class FactSourcesReportModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Module\ModuleReportTrait
     * @covers \Fisharebest\Webtrees\Module\PedigreeReportModule
     * @covers \Fisharebest\Webtrees\Report\AbstractRenderer
     * @covers \Fisharebest\Webtrees\Report\HtmlRenderer
     * @covers \Fisharebest\Webtrees\Report\PdfRenderer
     * @covers \Fisharebest\Webtrees\Report\ReportBaseCell
     * @covers \Fisharebest\Webtrees\Report\ReportBaseElement
     * @covers \Fisharebest\Webtrees\Report\ReportBaseFootnote
     * @covers \Fisharebest\Webtrees\Report\ReportBaseImage
     * @covers \Fisharebest\Webtrees\Report\ReportBaseLine
     * @covers \Fisharebest\Webtrees\Report\ReportBaseText
     * @covers \Fisharebest\Webtrees\Report\ReportBaseTextbox
     * @covers \Fisharebest\Webtrees\Report\ReportExpressionLanguageProvider
     * @covers \Fisharebest\Webtrees\Report\ReportHtmlCell
     * @covers \Fisharebest\Webtrees\Report\ReportHtmlFootnote
     * @covers \Fisharebest\Webtrees\Report\ReportHtmlImage
     * @covers \Fisharebest\Webtrees\Report\ReportHtmlLine
     * @covers \Fisharebest\Webtrees\Report\ReportHtmlText
     * @covers \Fisharebest\Webtrees\Report\ReportHtmlTextbox
     * @covers \Fisharebest\Webtrees\Report\ReportParserBase
     * @covers \Fisharebest\Webtrees\Report\ReportParserGenerate
     * @covers \Fisharebest\Webtrees\Report\ReportParserSetup
     * @covers \Fisharebest\Webtrees\Report\ReportPdfCell
     * @covers \Fisharebest\Webtrees\Report\ReportPdfFootnote
     * @covers \Fisharebest\Webtrees\Report\ReportPdfImage
     * @covers \Fisharebest\Webtrees\Report\ReportPdfLine
     * @covers \Fisharebest\Webtrees\Report\ReportPdfText
     * @covers \Fisharebest\Webtrees\Report\ReportPdfTextBox
     * @covers \Fisharebest\Webtrees\Report\TcpdfWrapper
     */
    public function testReportRunsWithoutError(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree   = $this->importTree('demo.ged');
        $module = new FactSourcesReportModule();
        $module->setName('fact_sources');

        $xml  = 'resources/' . $module->xmlFilename();
        $vars = [
            'id'       => ['id' => 's1'],
            'stype'    => ['id' => 'facts'],
            'sortby'   => ['id' => 'BIRT:DATE'],
            'pageSize' => ['id' => 'A4'],
        ];

        $report = new ReportParserSetup($xml);
        self::assertNotEmpty($report->reportProperties());

        ob_start();
        new ReportParserGenerate($xml, new HtmlRenderer(), $vars, $tree);
        $html = ob_get_clean();
        self::assertStringStartsWith('<', $html);
        self::assertStringEndsWith('>', $html);

        ob_start();
        new ReportParserGenerate($xml, new PdfRenderer(), $vars, $tree);
        $pdf = ob_get_clean();
        self::assertStringStartsWith('%PDF', $pdf);
        self::assertStringEndsWith("%%EOF\n", $pdf);
    }
}
