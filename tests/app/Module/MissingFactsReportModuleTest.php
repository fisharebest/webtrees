<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Report\ReportBaseTextbox;
use Fisharebest\Webtrees\Report\ReportExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\ReportHtmlCell;
use Fisharebest\Webtrees\Report\ReportHtmlFootnote;
use Fisharebest\Webtrees\Report\ReportHtmlImage;
use Fisharebest\Webtrees\Report\ReportHtmlLine;
use Fisharebest\Webtrees\Report\ReportHtmlText;
use Fisharebest\Webtrees\Report\ReportHtmlTextbox;
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
use Fisharebest\Webtrees\TestCase;

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
class MissingFactsReportModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testReportRunsWithoutError(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree   = $this->importTree('demo.ged');
        $module = new MissingFactsReportModule();
        $module->setName('missing_facts_report');

        $xml  = 'resources/' . $module->xmlFilename();
        $vars = [
            'pid'       => ['id' => 'X1030'],
            'relatives' => ['id' => 'direct-ancestors'],
            'maxgen'    => ['id' => '*'],
            'pageSize'  => ['id' => 'A4'],
            'sortby'    => ['id' => 'NAME'],
            'fbirt'     => ['id' => 'on'],
            'fburi'     => ['id' => 'on'],
            'fdeat'     => ['id' => 'on'],
            'fsour'     => ['id' => 'on'],
            'fbapm'     => ['id' => 'on'],
            'fbarm'     => ['id' => 'on'],
            'fbasm'     => ['id' => 'on'],
            'fconf'     => ['id' => 'on'],
            'fenga'     => ['id' => 'on'],
            'ffcom'     => ['id' => 'on'],
            'fmarb'     => ['id' => 'on'],
            'fmarr'     => ['id' => 'on'],
            'freli'     => ['id' => 'on'],
        ];

        new ReportParserSetup($xml);

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
