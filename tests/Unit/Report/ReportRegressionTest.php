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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\ImageFactoryInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Enums\ImageOperation;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\AbstractRenderer;
use Fisharebest\Webtrees\Report\Cell;
use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\CellNewline;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Report\ExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\Footnote;
use Fisharebest\Webtrees\Report\FootnoteTextsElement;
use Fisharebest\Webtrees\Report\GedcomFrame;
use Fisharebest\Webtrees\Report\GedcomTextReader;
use Fisharebest\Webtrees\Report\HexColor;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\HtmlWriter;
use Fisharebest\Webtrees\Report\Image;
use Fisharebest\Webtrees\Report\ImageContinuation;
use Fisharebest\Webtrees\Report\ImageData;
use Fisharebest\Webtrees\Report\InputDefinition;
use Fisharebest\Webtrees\Report\Line;
use Fisharebest\Webtrees\Report\ListBuilder;
use Fisharebest\Webtrees\Report\NewPageElement;
use Fisharebest\Webtrees\Report\NullElement;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\ParserGenerate;
use Fisharebest\Webtrees\Report\ParserSetup;
use Fisharebest\Webtrees\Report\PdfBlockWriter;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\PdfTextMeasurer;
use Fisharebest\Webtrees\Report\PdfWriter;
use Fisharebest\Webtrees\Report\PlaceholderExpander;
use Fisharebest\Webtrees\Report\RepeatFrame;
use Fisharebest\Webtrees\Report\Section;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TcLibPdfAdaptor;
use Fisharebest\Webtrees\Report\Text;
use Fisharebest\Webtrees\Report\TextBox;
use Fisharebest\Webtrees\Report\VariableTable;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function array_merge;
use function base64_decode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getenv;
use function is_dir;
use function mkdir;
use function preg_replace;

#[CoversClass(AbstractParser::class)]
#[CoversClass(AbstractRenderer::class)]
#[CoversClass(Cell::class)]
#[CoversClass(CellAlign::class)]
#[CoversClass(CellNewline::class)]
#[CoversClass(Config::class)]
#[CoversClass(Element::class)]
#[CoversClass(ExpressionLanguageProvider::class)]
#[CoversClass(Footnote::class)]
#[CoversClass(FootnoteTextsElement::class)]
#[CoversClass(GedcomFrame::class)]
#[CoversClass(GedcomTextReader::class)]
#[CoversClass(HexColor::class)]
#[CoversClass(HtmlRenderer::class)]
#[CoversClass(HtmlWriter::class)]
#[CoversClass(Image::class)]
#[CoversClass(ImageData::class)]
#[CoversClass(ImageContinuation::class)]
#[CoversClass(InputDefinition::class)]
#[CoversClass(Line::class)]
#[CoversClass(ListBuilder::class)]
#[CoversClass(NewPageElement::class)]
#[CoversClass(NullElement::class)]
#[CoversClass(PageOrientation::class)]
#[CoversClass(PaperSize::class)]
#[CoversClass(ParserGenerate::class)]
#[CoversClass(ParserSetup::class)]
#[CoversClass(PdfBlockWriter::class)]
#[CoversClass(PdfRenderer::class)]
#[CoversClass(PdfTextMeasurer::class)]
#[CoversClass(PdfWriter::class)]
#[CoversClass(PlaceholderExpander::class)]
#[CoversClass(RepeatFrame::class)]
#[CoversClass(Section::class)]
#[CoversClass(Style::class)]
#[CoversClass(TcLibPdfAdaptor::class)]
#[CoversClass(Text::class)]
#[CoversClass(TextBox::class)]
#[CoversClass(VariableTable::class)]
class ReportRegressionTest extends TestCase
{
    protected static bool $uses_database = true;

    private const string REPORTS_DIR  = __DIR__ . '/../../../resources/xml/reports/';
    private const string SNAPSHOT_DIR = __DIR__ . '/../../data/report_snapshots/';

    // A known individual / family / source from tests/data/demo.ged.
    private const string DEMO_INDIVIDUAL = 'X1030';
    private const string DEMO_FAMILY     = 'f1';
    private const string DEMO_SOURCE     = 'X1102';

    // A Hebrew-named individual for mixed-direction (bidi) testing.
    private const string DEMO_BIDI_INDIVIDUAL = 'X1167';

    // Fixed 1x1 thumbnail payloads used by the mocked image factory.
    private const string DUMMY_JPEG_BASE64 = '/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBxdWFsaXR5ID0gNzUK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgAAQABAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A9/ooooA//9k=';
    private const string DUMMY_PNG_BASE64  = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO7+o3kAAAAASUVORK5CYII=';

    /**
     * One data-provider row per bundled XML report.
     *
     * The values supplied here override the defaults parsed by ParserSetup.
     * Pick small values for `maxgen` so the snapshots stay readable and the
     * tests stay fast; pick fixed date ranges so reports that filter on dates
     * do not depend on "now".
     *
     * @return array<string,array{string,array<string,string>,string}>
     */
    public static function reportProvider(): array
    {
        $maximum_generations = ['maxgen' => '3'];

        $wide_date_range = [
            'changeRangeStart' => '1 JAN 1900',
            'changeRangeEnd'   => '31 DEC 2100',
            'pending'          => 'no',
        ];

        return [
            'ahnentafel_report'      => ['ahnentafel_report.xml', array_merge(['pid' => self::DEMO_INDIVIDUAL, 'sources' => 'on', 'notes' => 'on', 'occu' => 'on', 'resi' => 'on', 'children' => 'on'], $maximum_generations), 'ahnentafel_report.xml'],
            'bdm_report'             => ['bdm_report.xml', [], 'bdm_report.xml'],
            'birth_report'           => ['birth_report.xml', [], 'birth_report.xml'],
            'cemetery_report'        => ['cemetery_report.xml', [], 'cemetery_report.xml'],
            'change_report'          => ['change_report.xml', $wide_date_range, 'change_report.xml'],
            'death_report'           => ['death_report.xml', [], 'death_report.xml'],
            'descendancy_report'     => ['descendancy_report.xml', array_merge(['pid' => self::DEMO_INDIVIDUAL, 'sources' => 'on'], $maximum_generations), 'descendancy_report.xml'],
            'fact_sources'           => ['fact_sources.xml', ['sid' => self::DEMO_SOURCE], 'fact_sources.xml'],
            'family_group_report'    => ['family_group_report.xml', ['famid' => self::DEMO_FAMILY, 'sources' => 'on', 'notes' => 'on', 'photos' => 'on', 'blanks' => 'on', 'colors' => 'on'], 'family_group_report.xml'],
            'individual_ext_report'  => ['individual_ext_report.xml', array_merge(['pid' => self::DEMO_INDIVIDUAL, 'relatives' => 'child-family', 'sources' => 'on', 'notes' => 'on', 'photos' => 'highlighted', 'colors' => 'on'], $maximum_generations), 'individual_ext_report.xml'],
            'individual_report'      => ['individual_report.xml', ['pid' => self::DEMO_INDIVIDUAL, 'sources' => 'on', 'notes' => 'on', 'photos' => 'highlighted', 'colors' => 'on'], 'individual_report.xml'],
            'marriage_report'        => ['marriage_report.xml', [], 'marriage_report.xml'],
            'missing_facts_report'   => ['missing_facts_report.xml', array_merge(['pid' => self::DEMO_INDIVIDUAL, 'fsour' => 'on', 'fbirt' => 'on', 'fbapm' => 'on', 'fbarm' => 'on', 'fbasm' => 'on', 'fconf' => 'on', 'fenga' => 'on', 'ffcom' => 'on', 'fmarb' => 'on', 'fmarr' => 'on', 'fdeat' => 'on', 'fburi' => 'on', 'freli' => 'on'], $maximum_generations), 'missing_facts_report.xml'],
            'occupation_report'      => ['occupation_report.xml', [], 'occupation_report.xml'],
            'pedigree_report'        => ['pedigree_report.xml', ['pid' => self::DEMO_INDIVIDUAL, 'layout' => 'A4-landscape'], 'pedigree_report.xml'],
            'relative_ext_report'    => ['relative_ext_report.xml', ['pid' => self::DEMO_INDIVIDUAL, 'relatives' => 'child-family'], 'relative_ext_report.xml'],
            'individual_report_bidi' => ['individual_report.xml', ['pid' => self::DEMO_BIDI_INDIVIDUAL], 'individual_report_bidi.xml'],
        ];
    }

    /**
     * @param array<string,string> $overrides
     */
    #[DataProvider('reportProvider')]
    public function testReportHtmlOutputMatchesSnapshot(string $report_file, array $overrides, string $snapshot_key): void
    {
        $xml_filename = self::REPORTS_DIR . $report_file;
        $vars         = $this->buildVars($xml_filename, $overrides);
        $tree         = $this->prepareTreeAndLogin();
        $author       = Webtrees::NAME;
        $timestamp    = Registry::timestampFactory()->make(0);

        $renderer = new HtmlRenderer();
        (new ParserGenerate($xml_filename, $renderer, $vars, $tree, $author, $timestamp))->process();
        $html = $renderer->output();

        $this->assertSnapshot($snapshot_key, $html, 'html');
    }

    /**
     * @param array<string,string> $overrides
     */
    #[DataProvider('reportProvider')]
    public function testReportPdfOutputMatchesSnapshot(string $report_file, array $overrides, string $snapshot_key): void
    {
        $xml_filename = self::REPORTS_DIR . $report_file;
        $vars         = $this->buildVars($xml_filename, $overrides);
        $tree         = $this->prepareTreeAndLogin();
        $author       = Webtrees::NAME;
        $timestamp    = Registry::timestampFactory()->make(0);

        $renderer = new PdfRenderer();
        (new ParserGenerate($xml_filename, $renderer, $vars, $tree, $author, $timestamp))->process();
        $pdf = $renderer->output();

        self::assertStringStartsWith('%PDF', $pdf, 'PDF output is missing the %PDF header for ' . $report_file);

        $pdf = $this->normalisePdf($pdf);

        $this->assertSnapshot($snapshot_key, $pdf, 'pdf');
    }

    /**
     * Build the runtime variables for a report by starting from the defaults
     * declared in the report's own <Input> elements (resolved by ParserSetup)
     * and then layering the test-specific overrides on top.
     *
     * @param array<string,string> $overrides
     *
     * @return array<string,string>
     */
    private function buildVars(string $xml_filename, array $overrides): array
    {
        $setup = (new ParserSetup($xml_filename))->process();

        $vars = [];
        foreach ($setup->reportInputs() as $input) {
            $vars[$input->name] = $input->default;
        }

        return array_merge($vars, $overrides);
    }

    private function prepareTreeAndLogin(): \Fisharebest\Webtrees\Tree
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        // Some reports (those that embed media) rely on this preference.
        Site::setPreference('INDEX_DIRECTORY', 'tests/data/');

        // Report snapshots validate report layout/content, not image encoding.
        // Use fixed image bytes so snapshots remain stable across platforms.
        $image_factory = self::createStub(ImageFactoryInterface::class);
        $image_factory->method('mediaFileThumbnail')->willReturnCallback(
            fn (
                mixed $media_file,
                int $width,
                int $height,
                ImageOperation $operation,
                bool $add_watermark,
            ): string => $this->dummyThumbnailForMime($media_file->mimeType())
        );
        Registry::imageFactory($image_factory);

        $tree = $this->importTree('demo.ged');
        $tree->setPreference('SHOW_NO_WATERMARK', (string) Auth::PRIV_NONE);

        return $tree;
    }

    private function dummyThumbnailForMime(string $mime_type): string
    {
        return match ($mime_type) {
            'image/png' => (string) base64_decode(self::DUMMY_PNG_BASE64, true),
            default     => (string) base64_decode(self::DUMMY_JPEG_BASE64, true),
        };
    }

    /**
     * Replace non-deterministic fields in the PDF output with fixed placeholders.
     *
     * The tc-lib-pdf library embeds the current time and a random file ID
     * into the PDF document metadata.  We normalise these so that snapshot
     * comparisons remain stable regardless of when the tests run.
     */
    private function normalisePdf(string $pdf): string
    {
        // PDF date strings: (D:YYYYMMDDHHmmSS+HH'MM') or (D:YYYYMMDDHHMMSSZ)
        $pdf = (string) preg_replace(
            '/\(D:\d{14}[^)]*\)/',
            '(D:19700101000000Z)',
            $pdf
        );

        // PDF trailer file ID: /ID [ <32-hex-chars> <32-hex-chars> ]
        $pdf = (string) preg_replace(
            '/\/ID \[ <[0-9a-f]{32}> <[0-9a-f]{32}> \]/',
            '/ID [ <cfcd208495d565ef66e7dff9f98764da> <cfcd208495d565ef66e7dff9f98764da> ]',
            $pdf
        );

        // TCPDF version string embedded in Producer metadata
        $pdf = (string) preg_replace(
            '/TCPDF \d+\.\d+\.\d+/',
            'TCPDF 0.0.0',
            $pdf
        );

        // XMP UUID references: uuid:xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
        $pdf = (string) preg_replace(
            '/uuid:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/',
            'uuid:cfcd2084-95d5-65ef-66e7-dff9f98764da',
            $pdf
        );

        // XMP ISO date strings: YYYY-MM-DDTHH:MM:SS(Z or +offset)
        $pdf = (string) preg_replace(
            '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[Z+][^<]*/',
            '1970-01-01T00:00:00Z',
            $pdf
        );

        return $pdf;
    }

    /**
     * Compare the rendered output to a stored snapshot. When the snapshot file
     * does not exist, create it and fail with an explanatory message so that
     * new baselines must be reviewed and committed deliberately. When the
     * UPDATE_SNAPSHOTS=1 environment variable is set, silently overwrite the
     * snapshot to support intentional refactor-induced changes.
     */
    private function assertSnapshot(string $report_file, string $actual, string $extension): void
    {
        if (!is_dir(self::SNAPSHOT_DIR)) {
            mkdir(self::SNAPSHOT_DIR, 0o755, true);
        }

        $snapshot_file = self::SNAPSHOT_DIR . $report_file . '.' . $extension;

        if (getenv('UPDATE_SNAPSHOTS') === '1') {
            file_put_contents($snapshot_file, $actual);

            return;
        }

        if (!file_exists($snapshot_file)) {
            file_put_contents($snapshot_file, $actual);
            self::fail(
                'No snapshot existed for ' . $report_file . '. '
                . 'A new baseline has been written to ' . $snapshot_file
                . ' — please review the contents and commit it.'
            );
        }

        $expected = (string) file_get_contents($snapshot_file);

        self::assertSame(
            $expected,
            $actual,
            'Rendered ' . $extension . ' for ' . $report_file . ' differs from the stored snapshot. '
            . 'If the change is intentional, re-run with UPDATE_SNAPSHOTS=1 to regenerate.'
        );
    }
}
