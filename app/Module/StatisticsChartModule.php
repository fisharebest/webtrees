<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function app;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_sum;
use function array_values;
use function array_walk;
use function assert;
use function count;
use function explode;
use function in_array;
use function is_numeric;
use function sprintf;

/**
 * Class StatisticsChartModule
 */
class StatisticsChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    public const X_AXIS_INDIVIDUAL_MAP        = 1;
    public const X_AXIS_BIRTH_MAP             = 2;
    public const X_AXIS_DEATH_MAP             = 3;
    public const X_AXIS_MARRIAGE_MAP          = 4;
    public const X_AXIS_BIRTH_MONTH           = 11;
    public const X_AXIS_DEATH_MONTH           = 12;
    public const X_AXIS_MARRIAGE_MONTH        = 13;
    public const X_AXIS_FIRST_CHILD_MONTH     = 14;
    public const X_AXIS_FIRST_MARRIAGE_MONTH  = 15;
    public const X_AXIS_AGE_AT_DEATH          = 18;
    public const X_AXIS_AGE_AT_MARRIAGE       = 19;
    public const X_AXIS_AGE_AT_FIRST_MARRIAGE = 20;
    public const X_AXIS_NUMBER_OF_CHILDREN    = 21;

    public const Y_AXIS_NUMBERS = 201;
    public const Y_AXIS_PERCENT = 202;

    public const Z_AXIS_ALL  = 300;
    public const Z_AXIS_SEX  = 301;
    public const Z_AXIS_TIME = 302;

    // First two colors are blue/pink, to work with Z_AXIS_SEX.
    private const Z_AXIS_COLORS = ['0000FF', 'FFA0CB', '9F00FF', 'FF7000', '905030', 'FF0000', '00FF00', 'F0F000'];

    private const DAYS_IN_YEAR = 365.25;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Statistics');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “StatisticsChart” module */
        return I18N::translate('Various statistics charts.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-statistics';
    }

    /**
     * The URL for this chart.
     *
     * @param Individual $individual
     * @param mixed[]    $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'Chart',
                'tree'    => $individual->tree()->name(),
            ] + $parameters);
    }

    /**
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $tabs = [
            I18N::translate('Individuals') => route('module', [
                'module' => $this->name(),
                'action' => 'Individuals',
                'tree'    => $tree->name(),
            ]),
            I18N::translate('Families')    => route('module', [
                'module' => $this->name(),
                'action' => 'Families',
                'tree'    => $tree->name(),
            ]),
            I18N::translate('Other')       => route('module', [
                'module' => $this->name(),
                'action' => 'Other',
                'tree'    => $tree->name(),
            ]),
            I18N::translate('Custom')      => route('module', [
                'module' => $this->name(),
                'action' => 'Custom',
                'tree'    => $tree->name(),
            ]),
        ];

        return $this->viewResponse('modules/statistics-chart/page', [
            'module' => $this->name(),
            'tabs'   => $tabs,
            'title'  => $this->title(),
            'tree'   => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getIndividualsAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('modules/statistics-chart/individuals', [
            'show_oldest_living' => Auth::check(),
            'stats'              => app(Statistics::class),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getFamiliesAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('modules/statistics-chart/families', [
            'stats' => app(Statistics::class),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getOtherAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('modules/statistics-chart/other', [
            'stats' => app(Statistics::class),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getCustomAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        return $this->viewResponse('modules/statistics-chart/custom', [
            'module' => $this,
            'tree'   => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postCustomChartAction(ServerRequestInterface $request): ResponseInterface
    {
        $statistics = app(Statistics::class);
        assert($statistics instanceof Statistics);

        $params = (array) $request->getParsedBody();

        $x_axis_type = (int) $params['x-as'];
        $y_axis_type = (int) $params['y-as'];
        $z_axis_type = (int) $params['z-as'];
        $ydata       = [];

        switch ($x_axis_type) {
            case self::X_AXIS_INDIVIDUAL_MAP:
                return response($statistics->chartDistribution(
                    $params['chart_shows'],
                    $params['chart_type'],
                    $params['SURN']
                ));

            case self::X_AXIS_BIRTH_MAP:
                return response($statistics->chartDistribution(
                    $params['chart_shows'],
                    'birth_distribution_chart'
                ));

            case self::X_AXIS_DEATH_MAP:
                return response($statistics->chartDistribution(
                    $params['chart_shows'],
                    'death_distribution_chart'
                ));

            case self::X_AXIS_MARRIAGE_MAP:
                return response($statistics->chartDistribution(
                    $params['chart_shows'],
                    'marriage_distribution_chart'
                ));

            case self::X_AXIS_BIRTH_MONTH:
                $chart_title  = I18N::translate('Month of birth');
                $x_axis_title = I18N::translate('Month');
                $x_axis       = $this->axisMonths();

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->statsBirthQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        $rows   = $statistics->statsBirthBySexQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, $row->i_sex, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->statsBirthQuery($prev_boundary, $boundary)->get();
                            foreach ($rows as $row) {
                                $this->fillYData($row->d_month, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_DEATH_MONTH:
                $chart_title  = I18N::translate('Month of death');
                $x_axis_title = I18N::translate('Month');
                $x_axis       = $this->axisMonths();

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->statsDeathQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        $rows   = $statistics->statsDeathBySexQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, $row->i_sex, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->statsDeathQuery($prev_boundary, $boundary)->get();
                            foreach ($rows as $row) {
                                $this->fillYData($row->d_month, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_MARRIAGE_MONTH:
                $chart_title  = I18N::translate('Month of marriage');
                $x_axis_title = I18N::translate('Month');
                $x_axis       = $this->axisMonths();

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->statsMarriageQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->statsMarriageQuery($prev_boundary, $boundary)->get();
                            foreach ($rows as $row) {
                                $this->fillYData($row->d_month, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_FIRST_CHILD_MONTH:
                $chart_title  = I18N::translate('Month of birth of first child in a relation');
                $x_axis_title = I18N::translate('Month');
                $x_axis       = $this->axisMonths();

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Children');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->monthFirstChildQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        $rows   = $statistics->monthFirstChildBySexQuery()->get();
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, $row->i_sex, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->monthFirstChildQuery($prev_boundary, $boundary)->get();
                            foreach ($rows as $row) {
                                $this->fillYData($row->d_month, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_FIRST_MARRIAGE_MONTH:
                $chart_title  = I18N::translate('Month of first marriage');
                $x_axis_title = I18N::translate('Month');
                $x_axis       = $this->axisMonths();

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->statsFirstMarriageQuery()->get();
                        $indi   = [];
                        foreach ($rows as $row) {
                            if (!in_array($row->f_husb, $indi, true) && !in_array($row->f_wife, $indi, true)) {
                                $this->fillYData($row->month, 0, 1, $x_axis, $z_axis, $ydata);
                            }
                            $indi[]  = $row->f_husb;
                            $indi[]  = $row->f_wife;
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        $indi           = [];
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->statsFirstMarriageQuery($prev_boundary, $boundary)->get();
                            foreach ($rows as $row) {
                                if (!in_array($row->f_husb, $indi, true) && !in_array($row->f_wife, $indi, true)) {
                                    $this->fillYData($row->month, $boundary, 1, $x_axis, $z_axis, $ydata);
                                }
                                $indi[]  = $row->f_husb;
                                $indi[]  = $row->f_wife;
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_DEATH:
                $chart_title    = I18N::translate('Average age at death');
                $x_axis_title   = I18N::translate('age');
                $boundaries_csv = $params['x-axis-boundaries-ages'];
                $x_axis         = $this->axisNumbers($boundaries_csv);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->statsAgeQuery('DEAT');
                        foreach ($rows as $row) {
                            foreach ($row as $age) {
                                $years = (int) ($age / self::DAYS_IN_YEAR);
                                $this->fillYData($years, 0, 1, $x_axis, $z_axis, $ydata);
                            }
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        foreach (array_keys($z_axis) as $sex) {
                            $rows = $statistics->statsAgeQuery('DEAT', $sex);
                            foreach ($rows as $row) {
                                foreach ($row as $age) {
                                    $years = (int) ($age / self::DAYS_IN_YEAR);
                                    $this->fillYData($years, $sex, 1, $x_axis, $z_axis, $ydata);
                                }
                            }
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->statsAgeQuery('DEAT', 'BOTH', $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                foreach ($row as $age) {
                                    $years = (int) ($age / self::DAYS_IN_YEAR);
                                    $this->fillYData($years, $boundary, 1, $x_axis, $z_axis, $ydata);
                                }
                            }
                            $prev_boundary = $boundary + 1;
                        }

                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_MARRIAGE:
                $chart_title    = I18N::translate('Age in year of marriage');
                $x_axis_title   = I18N::translate('age');
                $boundaries_csv = $params['x-axis-boundaries-ages_m'];
                $x_axis         = $this->axisNumbers($boundaries_csv);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        // The stats query doesn't have an "all" function, so query M/F separately
                        foreach (['M', 'F'] as $sex) {
                            $rows = $statistics->statsMarrAgeQuery($sex);
                            foreach ($rows as $row) {
                                $years = (int) ($row->age / self::DAYS_IN_YEAR);
                                $this->fillYData($years, 0, 1, $x_axis, $z_axis, $ydata);
                            }
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        foreach (array_keys($z_axis) as $sex) {
                            $rows = $statistics->statsMarrAgeQuery($sex);
                            foreach ($rows as $row) {
                                $years = (int) ($row->age / self::DAYS_IN_YEAR);
                                $this->fillYData($years, $sex, 1, $x_axis, $z_axis, $ydata);
                            }
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        // The stats query doesn't have an "all" function, so query M/F separately
                        foreach (['M', 'F'] as $sex) {
                            $prev_boundary = 0;
                            foreach (array_keys($z_axis) as $boundary) {
                                $rows = $statistics->statsMarrAgeQuery($sex, $prev_boundary, $boundary);
                                foreach ($rows as $row) {
                                    $years = (int) ($row->age / self::DAYS_IN_YEAR);
                                    $this->fillYData($years, $boundary, 1, $x_axis, $z_axis, $ydata);
                                }
                                $prev_boundary = $boundary + 1;
                            }
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_FIRST_MARRIAGE:
                $chart_title    = I18N::translate('Age in year of first marriage');
                $x_axis_title   = I18N::translate('age');
                $boundaries_csv = $params['x-axis-boundaries-ages_m'];
                $x_axis         = $this->axisNumbers($boundaries_csv);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        // The stats query doesn't have an "all" function, so query M/F separately
                        foreach (['M', 'F'] as $sex) {
                            $rows = $statistics->statsMarrAgeQuery($sex);
                            $indi = [];
                            foreach ($rows as $row) {
                                if (!in_array($row->d_gid, $indi, true)) {
                                    $years = (int) ($row->age / self::DAYS_IN_YEAR);
                                    $this->fillYData($years, 0, 1, $x_axis, $z_axis, $ydata);
                                    $indi[] = $row->d_gid;
                                }
                            }
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        foreach (array_keys($z_axis) as $sex) {
                            $rows = $statistics->statsMarrAgeQuery($sex);
                            $indi = [];
                            foreach ($rows as $row) {
                                if (!in_array($row->d_gid, $indi, true)) {
                                    $years = (int) ($row->age / self::DAYS_IN_YEAR);
                                    $this->fillYData($years, $sex, 1, $x_axis, $z_axis, $ydata);
                                    $indi[] = $row->d_gid;
                                }
                            }
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        // The stats query doesn't have an "all" function, so query M/F separately
                        foreach (['M', 'F'] as $sex) {
                            $prev_boundary = 0;
                            $indi          = [];
                            foreach (array_keys($z_axis) as $boundary) {
                                $rows = $statistics->statsMarrAgeQuery($sex, $prev_boundary, $boundary);
                                foreach ($rows as $row) {
                                    if (!in_array($row->d_gid, $indi, true)) {
                                        $years = (int) ($row->age / self::DAYS_IN_YEAR);
                                        $this->fillYData($years, $boundary, 1, $x_axis, $z_axis, $ydata);
                                        $indi[] = $row->d_gid;
                                    }
                                }
                                $prev_boundary = $boundary + 1;
                            }
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_NUMBER_OF_CHILDREN:
                $chart_title  = I18N::translate('Number of children');
                $x_axis_title = I18N::translate('Children');
                $x_axis       = $this->axisNumbers('0,1,2,3,4,5,6,7,8,9,10');

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $statistics->statsChildrenQuery();
                        foreach ($rows as $row) {
                            $this->fillYData($row->f_numchil, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $params['z-axis-boundaries-periods'];
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $statistics->statsChildrenQuery($prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                $this->fillYData($row->f_numchil, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new HttpNotFoundException();
                }

                return response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            default:
                throw new HttpNotFoundException();
        }
    }

    /**
     * @return array<string>
     */
    private function axisAll(): array
    {
        return [
            I18N::translate('Total'),
        ];
    }

    /**
     * @return array<string>
     */
    private function axisSexes(): array
    {
        return [
            'M' => I18N::translate('Male'),
            'F' => I18N::translate('Female'),
        ];
    }

    /**
     * Labels for the X axis
     *
     * @return array<string>
     */
    private function axisMonths(): array
    {
        return [
            'JAN' => I18N::translateContext('NOMINATIVE', 'January'),
            'FEB' => I18N::translateContext('NOMINATIVE', 'February'),
            'MAR' => I18N::translateContext('NOMINATIVE', 'March'),
            'APR' => I18N::translateContext('NOMINATIVE', 'April'),
            'MAY' => I18N::translateContext('NOMINATIVE', 'May'),
            'JUN' => I18N::translateContext('NOMINATIVE', 'June'),
            'JUL' => I18N::translateContext('NOMINATIVE', 'July'),
            'AUG' => I18N::translateContext('NOMINATIVE', 'August'),
            'SEP' => I18N::translateContext('NOMINATIVE', 'September'),
            'OCT' => I18N::translateContext('NOMINATIVE', 'October'),
            'NOV' => I18N::translateContext('NOMINATIVE', 'November'),
            'DEC' => I18N::translateContext('NOMINATIVE', 'December'),
        ];
    }

    /**
     * Convert a list of N year-boundaries into N+1 year-ranges for the z-axis.
     *
     * @param string $boundaries_csv
     *
     * @return array<string>
     */
    private function axisYears(string $boundaries_csv): array
    {
        $boundaries = explode(',', $boundaries_csv);

        $axis = [];
        foreach ($boundaries as $n => $boundary) {
            if ($n === 0) {
                $axis[$boundary - 1] = '–' . I18N::digits($boundary);
            } else {
                $axis[$boundary - 1] = I18N::digits($boundaries[$n - 1]) . '–' . I18N::digits($boundary);
            }
        }

        $axis[PHP_INT_MAX] = I18N::digits($boundaries[count($boundaries) - 1]) . '–';

        return $axis;
    }

    /**
     * Create the X axis.
     *
     * @param string $boundaries_csv
     *
     * @return array<string>
     */
    private function axisNumbers(string $boundaries_csv): array
    {
        $boundaries = explode(',', $boundaries_csv);

        $boundaries = array_map(static function (string $x): int {
            return (int) $x;
        }, $boundaries);

        $axis = [];
        foreach ($boundaries as $n => $boundary) {
            if ($n === 0) {
                $prev_boundary = 0;
            } else {
                $prev_boundary = $boundaries[$n - 1] + 1;
            }

            if ($prev_boundary === $boundary) {
                /* I18N: A range of numbers */
                $axis[$boundary] = I18N::number($boundary);
            } else {
                /* I18N: A range of numbers */
                $axis[$boundary] = I18N::translate('%1$s–%2$s', I18N::number($prev_boundary), I18N::number($boundary));
            }
        }

        /* I18N: Label on a graph; 40+ means 40 or more */
        $axis[PHP_INT_MAX] = I18N::translate('%s+', I18N::number($boundaries[count($boundaries) - 1]));

        return $axis;
    }

    /**
     * Calculate the Y axis.
     *
     * @param int|string $x
     * @param int|string $z
     * @param int|string $value
     * @param array      $x_axis
     * @param array      $z_axis
     * @param int[][]    $ydata
     *
     * @return void
     */
    private function fillYData($x, $z, $value, array $x_axis, array $z_axis, array &$ydata): void
    {
        $x = $this->findAxisEntry($x, $x_axis);
        $z = $this->findAxisEntry($z, $z_axis);

        if (!array_key_exists($z, $z_axis)) {
            foreach (array_keys($z_axis) as $key) {
                if ($value <= $key) {
                    $z = $key;
                    break;
                }
            }
        }

        // Add the value to the appropriate data point.
        $ydata[$z][$x] = ($ydata[$z][$x] ?? 0) + $value;
    }

    /**
     * Find the axis entry for a given value.
     * Some are direct lookup (e.g. M/F, JAN/FEB/MAR).
     * Others need to find the appropriate range.
     *
     * @param int|float|string $value
     * @param string[]         $axis
     *
     * @return int|string
     */
    private function findAxisEntry($value, array $axis)
    {
        if (is_numeric($value)) {
            $value = (int) $value;

            if (!array_key_exists($value, $axis)) {
                foreach (array_keys($axis) as $boundary) {
                    if ($value <= $boundary) {
                        $value = $boundary;
                        break;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * Plot the data.
     *
     * @param string   $chart_title
     * @param string[] $x_axis
     * @param string   $x_axis_title
     * @param int[][]  $ydata
     * @param string   $y_axis_title
     * @param string[] $z_axis
     * @param int      $y_axis_type
     *
     * @return string
     */
    private function myPlot(
        string $chart_title,
        array $x_axis,
        string $x_axis_title,
        array $ydata,
        string $y_axis_title,
        array $z_axis,
        int $y_axis_type
    ): string {
        if (!count($ydata)) {
            return I18N::translate('This information is not available.');
        }

        // Colors for z-axis
        $colors = [];
        $index  = 0;
        while (count($colors) < count($ydata)) {
            $colors[] = self::Z_AXIS_COLORS[$index];
            $index    = ($index + 1) % count(self::Z_AXIS_COLORS);
        }

        // Convert our sparse dataset into a fixed-size array
        $tmp = [];
        foreach (array_keys($z_axis) as $z) {
            foreach (array_keys($x_axis) as $x) {
                $tmp[$z][$x] = $ydata[$z][$x] ?? 0;
            }
        }
        $ydata = $tmp;

        // Convert the chart data to percentage
        if ($y_axis_type === self::Y_AXIS_PERCENT) {
            // Normalise each (non-zero!) set of data to total 100%
            array_walk($ydata, static function (array &$x) {
                $sum = array_sum($x);
                if ($sum > 0) {
                    $x = array_map(static function ($y) use ($sum) {
                        return $y * 100.0 / $sum;
                    }, $x);
                }
            });
        }

        $data = [
            array_merge(
                [I18N::translate('Century')],
                array_values($z_axis)
            ),
        ];

        $intermediate = [];
        foreach ($ydata as $century => $months) {
            foreach ($months as $month => $value) {
                $intermediate[$month][] = [
                    'v' => $value,
                    'f' => ($y_axis_type === self::Y_AXIS_PERCENT) ? sprintf('%.1f%%', $value) : $value,
                ];
            }
        }

        foreach ($intermediate as $key => $values) {
            $data[] = array_merge(
                [$x_axis[$key]],
                $values
            );
        }

        $chart_options = [
            'title'    => '',
            'subtitle' => '',
            'height'   => 400,
            'width'    => '100%',
            'legend'   => [
                'position'  => count($z_axis) > 1 ? 'right' : 'none',
                'alignment' => 'center',
            ],
            'tooltip'  => [
                'format' => '\'%\'',
            ],
            'vAxis'    => [
                'title' => $y_axis_title ?? '',
            ],
            'hAxis'    => [
                'title' => $x_axis_title ?? '',
            ],
            'colors'   => $colors,
        ];

        return view('statistics/other/charts/custom', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }
}
