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
use Fisharebest\Webtrees\Report\AbstractCell;
use Fisharebest\Webtrees\Report\AbstractElement;
use Fisharebest\Webtrees\Report\NullElement;
use Fisharebest\Webtrees\Report\AbstractFootnote;
use Fisharebest\Webtrees\Report\AbstractImage;
use Fisharebest\Webtrees\Report\AbstractLine;
use Fisharebest\Webtrees\Report\AbstractText;
use Fisharebest\Webtrees\Report\AbstractTextBox;
use Fisharebest\Webtrees\Report\ExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\HtmlCell;
use Fisharebest\Webtrees\Report\HtmlFootnote;
use Fisharebest\Webtrees\Report\HtmlImage;
use Fisharebest\Webtrees\Report\HtmlLine;
use Fisharebest\Webtrees\Report\HtmlText;
use Fisharebest\Webtrees\Report\HtmlTextBox;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\ParserGenerate;
use Fisharebest\Webtrees\Report\ParserSetup;
use Fisharebest\Webtrees\Report\PdfCell;
use Fisharebest\Webtrees\Report\PdfFootnote;
use Fisharebest\Webtrees\Report\PdfImage;
use Fisharebest\Webtrees\Report\PdfLine;
use Fisharebest\Webtrees\Report\PdfText;
use Fisharebest\Webtrees\Report\PdfTextBox;
use Fisharebest\Webtrees\Report\TcpdfWrapper;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AbstractCell::class)]
#[CoversClass(AbstractFootnote::class)]
#[CoversClass(AbstractImage::class)]
#[CoversClass(AbstractLine::class)]
#[CoversClass(AbstractParser::class)]
#[CoversClass(AbstractRenderer::class)]
#[CoversClass(AbstractText::class)]
#[CoversClass(AbstractTextBox::class)]
#[CoversClass(AbstractElement::class)]
#[CoversClass(ExpressionLanguageProvider::class)]
#[CoversClass(HtmlCell::class)]
#[CoversClass(HtmlFootnote::class)]
#[CoversClass(HtmlImage::class)]
#[CoversClass(HtmlLine::class)]
#[CoversClass(HtmlRenderer::class)]
#[CoversClass(HtmlText::class)]
#[CoversClass(HtmlTextBox::class)]
#[CoversClass(NullElement::class)]
#[CoversClass(ParserGenerate::class)]
#[CoversClass(ParserSetup::class)]
#[CoversClass(PdfCell::class)]
#[CoversClass(PdfFootnote::class)]
#[CoversClass(PdfImage::class)]
#[CoversClass(PdfLine::class)]
#[CoversClass(PdfRenderer::class)]
#[CoversClass(PdfText::class)]
#[CoversClass(PdfTextBox::class)]
#[CoversClass(PedigreeReportModule::class)]
#[CoversClass(TcpdfWrapper::class)]
class OccupationReportModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return array<int,array<string,string>>
     */
    public static function reportOptions(): array
    {
        return [
            [
                'occupation' => 'Queen',
                'page_size'  => 'A4',
                'sortby'     => 'NAME',
            ],
            [
                'occupation' => 'Queen',
                'page_size'  => 'US-Letter',
                'sortby'     => 'NAME',
            ],
            [
                'occupation' => '',
                'page_size'  => '',
                'sortby'     => '',
            ],
        ];
    }

    #[DataProvider('reportOptions')]
    public function testReportRunsWithoutError(
        string $occupation,
        string $page_size,
        string $sortby,
    ): void {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree   = $this->importTree('demo.ged');
        $module = new OccupationReportModule();
        $module->setName('occupation_report');

        $xml  = 'resources/' . $module->xmlFilename();
        $vars = [
            'occupation' => $occupation,
            'pageSize'   => $page_size,
            'sortby'     => $sortby,
        ];

        $parser = new ParserSetup($xml);
        $this->assertNotEmpty($parser->reportDescription());
        $this->assertNotEmpty($parser->reportTitle());
        $this->assertNotEmpty($parser->reportInputs());

        Site::setPreference('INDEX_DIRECTORY', 'tests/data/');

        ob_start();
        new ParserGenerate($xml, new HtmlRenderer(), $vars, $tree);
        $html = ob_get_clean();
        self::assertIsString($html);
        self::assertStringStartsWith('<', $html);
        self::assertStringEndsWith('>', $html);

        ob_start();
        new ParserGenerate($xml, new PdfRenderer(), $vars, $tree);
        $pdf = ob_get_clean();
        self::assertIsString($pdf);
        self::assertStringStartsWith('%PDF', $pdf);
        self::assertStringEndsWith("%%EOF\n", $pdf);
    }
}
