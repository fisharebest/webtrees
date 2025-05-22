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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\StatisticsData;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(StatisticsChartModule::class)]
#[CoversClass(Statistics::class)]
#[CoversClass(StatisticsData::class)]
class StatisticsChartModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return array<int,array{x_as:int,y_as:int,z_as:int}>
     */
    public static function customChartFamilyAndIndividualOptions(): array
    {
        return [
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_DEATH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_DEATH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_DEATH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_DEATH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_DEATH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_DEATH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_FIRST_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_FIRST_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_FIRST_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_FIRST_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_FIRST_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_FIRST_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_AGE_AT_MARRIAGE, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_CHILD_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_CHILD_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_CHILD_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_CHILD_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_CHILD_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_SEX],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_CHILD_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_FIRST_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MONTH, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_NUMBER_OF_CHILDREN, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_NUMBER_OF_CHILDREN, 'y_as' => StatisticsChartModule::Y_AXIS_NUMBERS, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
            ['x_as' => StatisticsChartModule::X_AXIS_NUMBER_OF_CHILDREN, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_ALL],
            ['x_as' => StatisticsChartModule::X_AXIS_NUMBER_OF_CHILDREN, 'y_as' => StatisticsChartModule::Y_AXIS_PERCENT, 'z_as' => StatisticsChartModule::Z_AXIS_TIME],
        ];
    }

    /**
     * @return array<int,array{x_as:int,chart_shows:string,chart_type:string,surn:string}>
     */
    public static function customChartMapOptions(): array
    {
        return [
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => 'world', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '150', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '021', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '005', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '142', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '145', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '002', 'chart_type' => 'indi_distribution_chart', 'surn' => ''],

            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => 'world', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '150', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '021', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '005', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '142', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '145', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],
            ['x_as' => StatisticsChartModule::X_AXIS_INDIVIDUAL_MAP, 'chart_shows' => '002', 'chart_type' => 'surname_distribution_chart', 'surn' => 'smith'],

            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => 'world', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => '150', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => '021', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => '005', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => '142', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => '145', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_BIRTH_MAP, 'chart_shows' => '002', 'chart_type' => '', 'surn' => ''],

            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => 'world', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => '150', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => '021', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => '005', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => '142', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => '145', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_DEATH_MAP, 'chart_shows' => '002', 'chart_type' => '', 'surn' => ''],

            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => 'world', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => '150', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => '021', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => '005', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => '142', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => '145', 'chart_type' => '', 'surn' => ''],
            ['x_as' => StatisticsChartModule::X_AXIS_MARRIAGE_MAP, 'chart_shows' => '002', 'chart_type' => '', 'surn' => ''],
        ];
    }

    public function testTabContent(): void
    {
        $tree = $this->importTree('demo.ged');
        Registry::container()->set(Tree::class, $tree);

        $module  = new StatisticsChartModule();
        $request = self::createRequest(RequestMethodInterface::METHOD_POST)
            ->withAttribute('tree', $tree);

        $response = $module->getChartAction($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());

        //$response = $module->getFamiliesAction($request);
        //self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        //self::assertNotEmpty($response->getBody()->getContents());

        $response = $module->getIndividualsAction($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());

        $response = $module->getOtherAction($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());
    }

    #[DataProvider('customChartFamilyAndIndividualOptions')]
    public function testCustomFamilyAndIndividualCharts(int $x_as, int $y_as, int $z_as): void
    {
        $tree = $this->importTree('demo.ged');
        Registry::container()->set(Tree::class, $tree);

        $module  = new StatisticsChartModule();

        $request = self::createRequest()->withAttribute('tree', $tree);

        $response = $module->getCustomAction($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());

        $request = self::createRequest(RequestMethodInterface::METHOD_POST)
            ->withAttribute('tree', $tree)
            ->withParsedBody([
                'x-as'                      => $x_as,
                'y-as'                      => $y_as,
                'z-as'                      => $z_as,
                'x-axis-boundaries-ages'    => '1,5,10,20,30,40,50,60,70,80,90,100',
                'x-axis-boundaries-ages_m'  => '16,18,20,22,24,26,28,30,32,35,40,50',
                'z-axis-boundaries-periods' => '1700,1750,1800,1850,1900,1950,2000',
            ]);

        $response = $module->postCustomChartAction($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());
    }

    #[DataProvider('customChartMapOptions')]
    public function testCustomMapCharts(int $x_as, string $chart_shows, string $chart_type, string $surn): void
    {
        $tree = $this->importTree('demo.ged');
        Registry::container()->set(Tree::class, $tree);

        $module  = new StatisticsChartModule();
        $request = self::createRequest(RequestMethodInterface::METHOD_POST)
            ->withAttribute('tree', $tree)
            ->withParsedBody([
                'x-as'        => $x_as,
                'y-as'        => '0',
                'z-as'        => '0',
                'chart_shows' => $chart_shows,
                'chart_type'  => $chart_type,
                'SURN'        => $surn,
            ]);

        $response = $module->postCustomChartAction($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());
    }
}
