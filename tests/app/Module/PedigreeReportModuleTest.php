<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Report\ReportHtml;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Report\ReportPdf;
use Fisharebest\Webtrees\Tree;

/**
 * Test harness for the class PedigreeReportModule
 *
 * @covers \Fisharebest\Webtrees\Report\ReportHtml
 * @covers \Fisharebest\Webtrees\Report\ReportParserGenerate;
 * @covers \Fisharebest\Webtrees\Report\ReportPdf;
 */
class PedigreeReportModuleTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testReportRunsWithoutError(): void
    {
        $tree = $this->importTree('demo.ged');
        app()->instance(Tree::class, $tree);
        $xml  = WT_ROOT . 'resources/xml/reports/pedigree_report.xml';
        $vars = [
            'pid'       => ['id' => 'i1'],
            'orientation' => ['id' => 'portrait'],
        ];

        ob_start();
        new ReportParserGenerate($xml, new ReportHtml(), $vars, $tree);
        $html = ob_get_clean();
        $this->assertStringStartsWith('<', $html);
        $this->assertStringEndsWith('>', $html);

        ob_start();
        new ReportParserGenerate($xml, new ReportPdf(), $vars, $tree);
        $pdf = ob_get_clean();
        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertStringEndsWith("%%EOF\n", $pdf);
    }
}
