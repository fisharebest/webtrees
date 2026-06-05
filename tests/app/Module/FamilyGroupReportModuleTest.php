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
use Fisharebest\Webtrees\Report\AbstractCell;
use Fisharebest\Webtrees\Report\AbstractElement;
use Fisharebest\Webtrees\Report\AbstractFootnote;
use Fisharebest\Webtrees\Report\AbstractImage;
use Fisharebest\Webtrees\Report\AbstractLine;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\AbstractRenderer;
use Fisharebest\Webtrees\Report\AbstractText;
use Fisharebest\Webtrees\Report\AbstractTextBox;
use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\CellNewline;
use Fisharebest\Webtrees\Report\ExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\FootnoteTextsElement;
use Fisharebest\Webtrees\Report\GedcomFrame;
use Fisharebest\Webtrees\Report\GedcomTextReader;
use Fisharebest\Webtrees\Report\HexColor;
use Fisharebest\Webtrees\Report\HtmlCell;
use Fisharebest\Webtrees\Report\HtmlFootnote;
use Fisharebest\Webtrees\Report\HtmlImage;
use Fisharebest\Webtrees\Report\HtmlLine;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\HtmlText;
use Fisharebest\Webtrees\Report\HtmlTextBox;
use Fisharebest\Webtrees\Report\ImageContinuation;
use Fisharebest\Webtrees\Report\InputDefinition;
use Fisharebest\Webtrees\Report\NewPageElement;
use Fisharebest\Webtrees\Report\NullElement;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PageSize;
use Fisharebest\Webtrees\Report\ParserGenerate;
use Fisharebest\Webtrees\Report\ParserSetup;
use Fisharebest\Webtrees\Report\PdfCell;
use Fisharebest\Webtrees\Report\PdfFootnote;
use Fisharebest\Webtrees\Report\PdfImage;
use Fisharebest\Webtrees\Report\PdfLine;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\PdfText;
use Fisharebest\Webtrees\Report\PdfTextBox;
use Fisharebest\Webtrees\Report\PlaceholderExpander;
use Fisharebest\Webtrees\Report\RepeatFrame;
use Fisharebest\Webtrees\Report\ReportConfig;
use Fisharebest\Webtrees\Report\ReportListBuilder;
use Fisharebest\Webtrees\Report\ReportSection;
use Fisharebest\Webtrees\Report\RightToLeftFormatter;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TcpdfWrapper;
use Fisharebest\Webtrees\Report\Utf8WordWrap;
use Fisharebest\Webtrees\Report\VariableTable;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function ob_get_clean;

#[CoversClass(AbstractCell::class)]
#[CoversClass(AbstractElement::class)]
#[CoversClass(AbstractFootnote::class)]
#[CoversClass(AbstractImage::class)]
#[CoversClass(AbstractLine::class)]
#[CoversClass(AbstractParser::class)]
#[CoversClass(AbstractRenderer::class)]
#[CoversClass(AbstractText::class)]
#[CoversClass(AbstractTextBox::class)]
#[CoversClass(CellAlign::class)]
#[CoversClass(CellNewline::class)]
#[CoversClass(ExpressionLanguageProvider::class)]
#[CoversClass(FootnoteTextsElement::class)]
#[CoversClass(GedcomFrame::class)]
#[CoversClass(GedcomTextReader::class)]
#[CoversClass(HexColor::class)]
#[CoversClass(HtmlCell::class)]
#[CoversClass(HtmlFootnote::class)]
#[CoversClass(HtmlImage::class)]
#[CoversClass(HtmlLine::class)]
#[CoversClass(HtmlRenderer::class)]
#[CoversClass(HtmlText::class)]
#[CoversClass(HtmlTextBox::class)]
#[CoversClass(ImageContinuation::class)]
#[CoversClass(InputDefinition::class)]
#[CoversClass(NewPageElement::class)]
#[CoversClass(NullElement::class)]
#[CoversClass(PageOrientation::class)]
#[CoversClass(PageSize::class)]
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
#[CoversClass(PlaceholderExpander::class)]
#[CoversClass(RepeatFrame::class)]
#[CoversClass(ReportConfig::class)]
#[CoversClass(ReportListBuilder::class)]
#[CoversClass(ReportSection::class)]
#[CoversClass(RightToLeftFormatter::class)]
#[CoversClass(Style::class)]
#[CoversClass(TcpdfWrapper::class)]
#[CoversClass(Utf8WordWrap::class)]
#[CoversClass(VariableTable::class)]
class FamilyGroupReportModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return array<int,array<string,string>>
     */
    public static function reportOptions(): array
    {
        return [
            [
                'blanks'    => 'on',
                'colors'    => 'on',
                'id'        => 'f1',
                'notes'     => 'on',
                'page_size' => 'A4',
                'photos'    => 'on',
                'sources'   => 'on',
            ],
            [
                'blanks'    => '',
                'colors'    => '',
                'id'        => 'f1',
                'notes'     => '',
                'page_size' => 'US-Letter',
                'photos'    => '',
                'sources'   => '',
            ],
            [
                'blanks'    => '',
                'colors'    => '',
                'id'        => '',
                'notes'     => '',
                'page_size' => '',
                'photos'    => '',
                'sources'   => '',
            ],
        ];
    }

    #[DataProvider('reportOptions')]
    public function testReportRunsWithoutError(
        string $blanks,
        string $colors,
        string $id,
        string $notes,
        string $page_size,
        string $photos,
        string $sources,
    ): void {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree   = $this->importTree('demo.ged');
        $module = new FamilyGroupReportModule();
        $module->setName('family_group_report');

        $xml  = 'resources/' . $module->xmlFilename();
        $vars = [
            'blanks'   => $blanks,
            'colors'   => $colors,
            'id'       => $id,
            'notes'    => $notes,
            'pageSize' => $page_size,
            'photos'   => $photos,
            'sources'  => $sources,
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
