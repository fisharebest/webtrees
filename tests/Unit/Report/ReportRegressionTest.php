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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Report\AbstractCell;
use Fisharebest\Webtrees\Report\AbstractElement;
use Fisharebest\Webtrees\Report\AbstractFootnote;
use Fisharebest\Webtrees\Report\AbstractImage;
use Fisharebest\Webtrees\Report\AbstractLine;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\AbstractRenderer;
use Fisharebest\Webtrees\Report\AbstractText;
use Fisharebest\Webtrees\Report\AbstractTextBox;
use Fisharebest\Webtrees\Report\ExpressionLanguageProvider;
use Fisharebest\Webtrees\Report\HtmlCell;
use Fisharebest\Webtrees\Report\HtmlFootnote;
use Fisharebest\Webtrees\Report\HtmlImage;
use Fisharebest\Webtrees\Report\HtmlLine;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\HtmlText;
use Fisharebest\Webtrees\Report\HtmlTextBox;
use Fisharebest\Webtrees\Report\NullElement;
use Fisharebest\Webtrees\Report\ParserGenerate;
use Fisharebest\Webtrees\Report\ParserSetup;
use Fisharebest\Webtrees\Report\PdfCell;
use Fisharebest\Webtrees\Report\PdfFootnote;
use Fisharebest\Webtrees\Report\PdfImage;
use Fisharebest\Webtrees\Report\PdfLine;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\PdfText;
use Fisharebest\Webtrees\Report\PdfTextBox;
use Fisharebest\Webtrees\Report\RightToLeftFormatter;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\TcpdfWrapper;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function array_merge;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getenv;
use function is_dir;
use function mkdir;
use function ob_get_clean;
use function ob_start;
use function preg_replace;
use function str_replace;

#[CoversClass(AbstractCell::class)]
#[CoversClass(AbstractElement::class)]
#[CoversClass(AbstractFootnote::class)]
#[CoversClass(AbstractImage::class)]
#[CoversClass(AbstractLine::class)]
#[CoversClass(AbstractParser::class)]
#[CoversClass(AbstractRenderer::class)]
#[CoversClass(AbstractText::class)]
#[CoversClass(AbstractTextBox::class)]
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
#[CoversClass(RightToLeftFormatter::class)]
#[CoversClass(Style::class)]
#[CoversClass(TcpdfWrapper::class)]
class ReportRegressionTest extends TestCase
{
    protected static bool $uses_database = true;

    private const string REPORTS_DIR  = __DIR__ . '/../../../resources/xml/reports/';
    private const string SNAPSHOT_DIR = __DIR__ . '/../../data/report_snapshots/';

    // A known individual / family / source from tests/data/demo.ged.
    private const string DEMO_INDIVIDUAL = 'X1030';
    private const string DEMO_FAMILY     = 'f1';
    private const string DEMO_SOURCE     = 'X1102';

    /**
     * One data-provider row per bundled XML report.
     *
     * The values supplied here override the defaults parsed by ParserSetup.
     * Pick small values for `maxgen` so the snapshots stay readable and the
     * tests stay fast; pick fixed date ranges so reports that filter on dates
     * do not depend on "now".
     *
     * @return array<string,array{string,array<string,string>}>
     */
    public static function reportProvider(): array
    {
        $small_generations = ['maxgen' => '3'];

        $wide_date_range = [
            'changeRangeStart' => '1 JAN 1900',
            'changeRangeEnd'   => '31 DEC 2100',
            'pending'          => 'no',
        ];

        return [
            'ahnentafel_report'    => ['ahnentafel_report.xml',    array_merge(['pid' => self::DEMO_INDIVIDUAL], $small_generations)],
            'bdm_report'           => ['bdm_report.xml',           []],
            'birth_report'         => ['birth_report.xml',         []],
            'cemetery_report'      => ['cemetery_report.xml',      []],
            'change_report'        => ['change_report.xml',        $wide_date_range],
            'death_report'         => ['death_report.xml',         []],
            'descendancy_report'   => ['descendancy_report.xml',   array_merge(['pid' => self::DEMO_INDIVIDUAL], $small_generations)],
            'fact_sources'         => ['fact_sources.xml',         ['sid' => self::DEMO_SOURCE]],
            'family_group_report'  => ['family_group_report.xml',  ['famid' => self::DEMO_FAMILY]],
            'individual_ext_report' => ['individual_ext_report.xml', array_merge(['pid' => self::DEMO_INDIVIDUAL, 'relatives' => 'child-family'], $small_generations)],
            'individual_report'    => ['individual_report.xml',    ['pid' => self::DEMO_INDIVIDUAL]],
            'marriage_report'      => ['marriage_report.xml',      []],
            'missing_facts_report' => ['missing_facts_report.xml', array_merge(['pid' => self::DEMO_INDIVIDUAL], $small_generations)],
            'occupation_report'    => ['occupation_report.xml',    []],
            'pedigree_report'      => ['pedigree_report.xml',      ['pid' => self::DEMO_INDIVIDUAL]],
            'relative_ext_report'  => ['relative_ext_report.xml',  ['pid' => self::DEMO_INDIVIDUAL, 'relatives' => 'child-family']],
        ];
    }

    /**
     * @param array<string,string> $overrides
     */
    #[DataProvider('reportProvider')]
    public function testReportHtmlOutputMatchesSnapshot(string $report_file, array $overrides): void
    {
        $xml_filename = self::REPORTS_DIR . $report_file;
        $vars         = $this->buildVars($xml_filename, $overrides);
        $tree         = $this->prepareTreeAndLogin();

        ob_start();
        new ParserGenerate($xml_filename, new HtmlRenderer(), $vars, $tree);
        $html = (string) ob_get_clean();

        $normalised = $this->normaliseHtml($html);

        $this->assertSnapshot($report_file, $normalised);
    }

    /**
     * Smoke-test the PDF backend. PDF byte-output is not deterministic across
     * runs (TCPDF embeds a creation timestamp and assigns generation-dependent
     * object ids), so we deliberately only check the structural envelope.
     *
     * @param array<string,string> $overrides
     */
    #[DataProvider('reportProvider')]
    public function testReportPdfOutputIsWellFormed(string $report_file, array $overrides): void
    {
        $xml_filename = self::REPORTS_DIR . $report_file;
        $vars         = $this->buildVars($xml_filename, $overrides);
        $tree         = $this->prepareTreeAndLogin();

        ob_start();
        new ParserGenerate($xml_filename, new PdfRenderer(), $vars, $tree);
        $pdf = (string) ob_get_clean();

        self::assertNotSame('', $pdf, 'PDF output is empty for ' . $report_file);
        self::assertStringStartsWith('%PDF', $pdf, 'PDF output is missing the %PDF header for ' . $report_file);
        self::assertStringEndsWith("%%EOF\n", $pdf, 'PDF output is missing the %%EOF trailer for ' . $report_file);
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
        $setup = new ParserSetup($xml_filename);

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

        return $this->importTree('demo.ged');
    }

    /**
     * Replace volatile substrings with stable placeholders so that snapshots
     * survive across machines, days and webtrees releases.
     */
    private function normaliseHtml(string $html): string
    {
        // The "Generated by webtrees X.Y.Z" footer embeds the current version.
        $html = str_replace(Webtrees::VERSION, '{{VERSION}}', $html);

        // <Now/> expands to a localised long-form timestamp via
        // Registry::timestampFactory()->now()->isoFormat('LLLL').
        // In the en-US locale (set by TestCase::setUp) this produces output
        // such as "Monday, June 1, 2026 12:00 AM".
        $html = (string) preg_replace(
            '/[A-Z][a-z]+, [A-Z][a-z]+ \d{1,2}, \d{4} \d{1,2}:\d{2} (?:AM|PM)/',
            '{{NOW}}',
            $html
        );

        return $html;
    }

    /**
     * Compare the rendered output to a stored snapshot. When the snapshot file
     * does not exist, create it and fail with an explanatory message so that
     * new baselines must be reviewed and committed deliberately. When the
     * UPDATE_SNAPSHOTS=1 environment variable is set, silently overwrite the
     * snapshot to support intentional refactor-induced changes.
     */
    private function assertSnapshot(string $report_file, string $actual): void
    {
        if (!is_dir(self::SNAPSHOT_DIR)) {
            mkdir(self::SNAPSHOT_DIR, 0o755, true);
        }

        $snapshot_file = self::SNAPSHOT_DIR . $report_file . '.html';

        if (getenv('UPDATE_SNAPSHOTS') === '1') {
            file_put_contents($snapshot_file, $actual);
            self::assertTrue(true, 'Snapshot regenerated: ' . $snapshot_file);

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
            'Rendered HTML for ' . $report_file . ' differs from the stored snapshot. '
            . 'If the change is intentional, re-run with UPDATE_SNAPSHOTS=1 to regenerate.'
        );
    }
}
