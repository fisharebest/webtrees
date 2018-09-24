<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * A chart showing the various statistics about the family tree.
 *
 * CAUTION - The google image charts (bitmap) API is deprecated.
 * We need to migrate to the charts API (which uses SVG).
 */
class StatisticsChartController extends AbstractChartController
{
    // We generate a bitmap chart with these dimensions in image pixels.
    // These set the aspect ratio.  The actual image is sized using CSS
    // The maximum size (width x height) is 300,000
    const CHART_WIDTH  = 950;
    const CHART_HEIGHT = 315;

    const X_AXIS_INDIVIDUAL_MAP        = 1;
    const X_AXIS_BIRTH_MAP             = 2;
    const X_AXIS_DEATH_MAP             = 3;
    const X_AXIS_MARRIAGE_MAP          = 4;
    const X_AXIS_BIRTH_MONTH           = 11;
    const X_AXIS_DEATH_MONTH           = 12;
    const X_AXIS_MARRIAGE_MONTH        = 13;
    const X_AXIS_FIRST_CHILD_MONTH     = 14;
    const X_AXIS_FIRST_MARRIAGE_MONTH  = 15;
    const X_AXIS_AGE_AT_DEATH          = 18;
    const X_AXIS_AGE_AT_MARRIAGE       = 19;
    const X_AXIS_AGE_AT_FIRST_MARRIAGE = 20;
    const X_AXIS_NUMBER_OF_CHILDREN    = 21;

    const Y_AXIS_NUMBERS = 201;
    const Y_AXIS_PERCENT = 202;

    const Z_AXIS_ALL  = 300;
    const Z_AXIS_SEX  = 301;
    const Z_AXIS_TIME = 302;

    // First two colors are blue/pink, to work with Z_AXIS_SEX.
    const Z_AXIS_COLORS = ['0000FF', 'FFA0CB', '9F00FF', 'FF7000', '905030', 'FF0000', '00FF00', 'F0F000'];

    const MONTHS = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

    const DAYS_IN_YEAR = 365.25;

    /**
     * A form to request the chart parameters.
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function page(Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'statistics_chart');

        $title = I18N::translate('Statistics');

        return $this->viewResponse('statistics-page', [
            'title' => $title,
        ]);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function chartIndividuals(Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-individuals', [
            'show_oldest_living' => Auth::check(),
            'stats'              => new Stats($tree),
        ]);

        return new Response($html);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function chartFamilies(Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-families', [
            'stats' => new Stats($tree),
        ]);

        return new Response($html);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function chartOther(Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-other', [
            'stats' => new Stats($tree),
        ]);

        return new Response($html);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function chartCustomOptions(Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-custom');

        return new Response($html);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function chartCustomChart(Request $request, Tree $tree): Response
    {

        $this->checkModuleIsActive($tree, 'statistics_chart');

        $x_axis_type = (int) $request->get('x-as');
        $y_axis_type = (int) $request->get('y-as');
        $z_axis_type = (int) $request->get('z-as');
        $ydata       = [];
        $stats       = new Stats($tree);

        switch ($x_axis_type) {
            case self::X_AXIS_INDIVIDUAL_MAP:
                return new Response($stats->chartDistribution([
                    $request->get('chart_shows'),
                    $request->get('chart_type'),
                    $request->get('SURN'),
                ]));

            case self::X_AXIS_BIRTH_MAP:
                return new Response($stats->chartDistribution([
                    $request->get('chart_shows'),
                    'birth_distribution_chart',
                ]));

            case self::X_AXIS_DEATH_MAP:
                return new Response($stats->chartDistribution([
                    $request->get('chart_shows'),
                    'death_distribution_chart',
                ]));

            case self::X_AXIS_MARRIAGE_MAP:
                return new Response($stats->chartDistribution([
                    $request->get('chart_shows'),
                    'marriage_distribution_chart',
                ]));

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
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $stats->statsBirthQuery(false, false);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        $rows = $stats->statsBirthQuery(false, true);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, $row->i_sex, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsBirthQuery(false, false, $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                $this->fillYData($boundary, $row->d_month, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

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
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $stats->statsDeathQuery(false, false);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        $rows = $stats->statsDeathQuery(false, true);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, $row->i_sex, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsDeathQuery(false, false, $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                $this->fillYData($boundary, $row->d_month, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

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
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $stats->statsMarrQuery(false, false);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsMarrQuery(false, false, $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                $this->fillYData($boundary, $row->d_month, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

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
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $stats->monthFirstChildQuery(false, false);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        $rows = $stats->monthFirstChildQuery(false, true);
                        foreach ($rows as $row) {
                            $this->fillYData($row->d_month, $row->i_sex, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsMarrQuery(false, false, $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                $this->fillYData($row->d_month, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

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
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows   = $stats->statsMarrQuery(false, true);
                        $indi   = [];
                        $fam    = [];
                        foreach ($rows as $row) {
                            if (!in_array($row->indi, $indi) && !in_array($row->fams, $fam)) {
                                $this->fillYData($row->month, 0, 1, $x_axis, $z_axis, $ydata);
                            }
                            $indi[] = $row->indi;
                            $fam[]  = $row->fams;
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        $indi           = [];
                        $fam            = [];
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsMarrQuery(false, true, $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                if (!in_array($row->indi, $indi) && !in_array($row->fams, $fam)) {
                                    $this->fillYData($row->month, $boundary, 1, $x_axis, $z_axis, $ydata);
                                }
                                $indi[] = $row->indi;
                                $fam[]  = $row->fams;
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_DEATH:
                $chart_title    = I18N::translate('Average age at death');
                $x_axis_title   = I18N::translate('age');
                $boundaries_csv = $request->get('x-axis-boundaries-ages');
                $x_axis         = $this->axisNumbers($boundaries_csv);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows = $stats->statsAgeQuery(false, 'DEAT');
                        foreach ($rows as $row) {
                            foreach ($row as $age) {
                                $this->fillYData($age / self::DAYS_IN_YEAR, 0, 1, $x_axis, $z_axis, $ydata);
                            }
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        foreach (array_keys($z_axis) as $sex) {
                            $rows = $stats->statsAgeQuery(false, 'DEAT', $sex);
                            foreach ($rows as $row) {
                                foreach ($row as $age) {
                                    $this->fillYData($age / self::DAYS_IN_YEAR, $sex, 1, $x_axis, $z_axis, $ydata);
                                }
                            }
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary  = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsAgeQuery(false, 'DEAT', 'BOTH', $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                foreach ($row as $age_value) {
                                    $this->fillYData($age_value / self::DAYS_IN_YEAR, $boundary, 1, $x_axis, $z_axis, $ydata);
                                }
                            }
                            $prev_boundary = $boundary + 1;
                        }

                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_MARRIAGE:
                $chart_title    = I18N::translate('Age in year of marriage');
                $x_axis_title   = I18N::translate('age');
                $boundaries_csv = $request->get('x-axis-boundaries-ages_m');
                $x_axis         = $this->axisNumbers($boundaries_csv);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        // The stats query doesn't have an "all" function, so query M/F/U separately
                        foreach (['M', 'F', 'U'] as $sex) {
                            $rows = $stats->statsMarrAgeQuery(false, $sex);
                            foreach ($rows as $row) {
                                $this->fillYData($row->age / self::DAYS_IN_YEAR, 0, 1, $x_axis, $z_axis, $ydata);
                            }
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        foreach (array_keys($z_axis) as $sex) {
                            $rows = $stats->statsMarrAgeQuery(false, $sex);
                            foreach ($rows as $row) {
                                $this->fillYData($row->age / self::DAYS_IN_YEAR, $sex, 1, $x_axis, $z_axis, $ydata);
                            }
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        // The stats query doesn't have an "all" function, so query M/F/U separately
                        foreach (['M', 'F', 'U'] as $sex) {
                            $prev_boundary = 0;
                            foreach (array_keys($z_axis) as $boundary) {
                                $rows = $stats->statsMarrAgeQuery(false, $sex, $prev_boundary, $boundary);
                                foreach ($rows as $row) {
                                    $this->fillYData($row->age / self::DAYS_IN_YEAR, $boundary, 1, $x_axis, $z_axis, $ydata);
                                }
                                $prev_boundary = $boundary + 1;
                            }
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_FIRST_MARRIAGE:
                $chart_title    = I18N::translate('Age in year of first marriage');
                $x_axis_title   = I18N::translate('age');
                $boundaries_csv = $request->get('x-axis-boundaries-ages_m');
                $x_axis         = $this->axisNumbers($boundaries_csv);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        // The stats query doesn't have an "all" function, so query M/F/U separately
                        foreach (['M', 'F', 'U'] as $sex) {
                            $rows = $stats->statsMarrAgeQuery(false, $sex);
                            $indi = [];
                            foreach ($rows as $row) {
                                if (!in_array($row->d_gid, $indi)) {
                                    $this->fillYData($row->age / self::DAYS_IN_YEAR, 0, 1, $x_axis, $z_axis, $ydata);
                                    $indi[] = $row->d_gid;
                                }
                            }
                        }
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = $this->axisSexes();
                        foreach (array_keys($z_axis) as $sex) {
                            $rows = $stats->statsMarrAgeQuery(false, $sex);
                            $indi = [];
                            foreach ($rows as $row) {
                                if (!in_array($row->d_gid, $indi)) {
                                    $this->fillYData($row->age / self::DAYS_IN_YEAR, $sex, 1, $x_axis, $z_axis, $ydata);
                                    $indi[] = $row->d_gid;
                                }
                            }
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        // The stats query doesn't have an "all" function, so query M/F/U separately
                        foreach (['M', 'F', 'U'] as $sex) {
                            $prev_boundary = 0;
                            $indi = [];
                            foreach (array_keys($z_axis) as $boundary) {
                                $rows = $stats->statsMarrAgeQuery(false, $sex, $prev_boundary, $boundary);
                                foreach ($rows as $row) {
                                    if (!in_array($row->d_gid, $indi)) {
                                        $this->fillYData($row->age / self::DAYS_IN_YEAR, $boundary, 1, $x_axis, $z_axis, $ydata);
                                        $indi[] = $row->d_gid;
                                    }
                                }
                                $prev_boundary = $boundary + 1;
                            }
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_NUMBER_OF_CHILDREN:
                $chart_title  = I18N::translate('Number of children');
                $x_axis_title = I18N::translate('Children');
                $x_axis       = $this->axisNumbers('1,2,3,4,5,6,7,8,9,10');

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = $this->axisAll();
                        $rows = $stats->statsChildrenQuery(false);
                        foreach ($rows as $row) {
                            $this->fillYData($row->f_numchil, 0, $row->total, $x_axis, $z_axis, $ydata);
                        }
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->axisYears($boundaries_csv);
                        $prev_boundary = 0;
                        foreach (array_keys($z_axis) as $boundary) {
                            $rows = $stats->statsChildrenQuery(false, 'BOTH', $prev_boundary, $boundary);
                            foreach ($rows as $row) {
                                $this->fillYData($row->f_numchil, $boundary, $row->total, $x_axis, $z_axis, $ydata);
                            }
                            $prev_boundary = $boundary + 1;
                        }
                        break;
                    default:
                        throw new NotFoundHttpException();
                }

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            default:
                throw new NotFoundHttpException();
                break;
        }
    }

    /**
     * @return string[]
     */
    private function axisAll(): array
    {
        return [
            I18N::translate('Total'),
        ];
    }

    /**
     * @return string[]
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
     * @return string[]
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
     * @return string[]
     */
    private function axisYears($boundaries_csv): array
    {
        $boundaries = explode(',', $boundaries_csv);

        $axis = [];
        foreach ($boundaries as $n => $boundary) {
            if ($n === 0) {
                $date = new Date('BEF ' . $boundary);
            } else {
                $date = new Date('BET ' . $boundaries[$n - 1] . ' AND ' . ($boundary - 1));
            }
            $axis[$boundary - 1] = strip_tags($date->display());
        }

        $date              = new Date('AFT ' . $boundaries[count($boundaries) - 1]);
        $axis[PHP_INT_MAX] = strip_tags($date->display());

        return $axis;
    }

    /**
     * Create the X axis.
     *
     * @param string $boundaries_csv
     *
     * @return array
     */
    private function axisNumbers(string $boundaries_csv): array
    {
        $boundaries = explode(',', $boundaries_csv);

        $boundaries = array_map(function (string $x): int {
            return (int) $x;
        }, $boundaries);

        $axis = [];
        foreach ($boundaries as $n => $boundary) {
            if ($n === 0) {
                /* I18N: A range of numbers */
                $axis[$boundary - 1] = I18N::translate('%1$s–%2$s', I18N::number(0), I18N::number($boundary));
            } else {
                /* I18N: A range of numbers */
                $axis[$boundary - 1] = I18N::translate('%1$s–%2$s', I18N::number($boundaries[$n - 1]), I18N::number($boundary));
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
    private function fillYData($x, $z, $value, array $x_axis, array $z_axis, array &$ydata)
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
     * Others need to find the approprate range.
     *
     * @param int|float|string $value
     * @param string[]         $axis
     *
     * @return int|string
     */
    private function findAxisEntry($value, $axis)
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
     * @param int[]    $x_axis
     * @param string   $x_axis_title
     * @param int[][]  $ydata
     * @param string   $y_axis_title
     * @param string[] $z_axis
     * @param int      $y_axis_type
     *
     * @return string
     */
    private function myPlot(string $chart_title, array $x_axis, string $x_axis_title, array $ydata, string $y_axis_title, array $z_axis, int $y_axis_type): string
    {
        // Bar dimensions
        if (count($ydata) > 3) {
            $chbh = '5,1';
        } elseif (count($ydata) < 2) {
            $chbh = '45,1';
        } else {
            $chbh = '20,3';
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

        // The chart data
        if ($y_axis_type === self::Y_AXIS_PERCENT) {
            // Normalise each (non-zero!) set of data to total 100%
            array_walk($ydata, function (array &$x) {
                $sum = array_sum($x);
                if ($sum > 0) {
                    $x = array_map(function ($y) use ($sum) {
                        return $y * 100.0 / $sum;
                    }, $x);
                }
            });
        }

        // Find the maximum value, so we can draw the scale
        $ymax = max(array_map(function (array $x) {
            return max($x);
        }, $ydata));

        // Google charts API requires data to be scaled 0 - 100.
        $scale = max(array_map(function (array $x) {
            return max($x);
        }, $ydata));

        if ($scale > 0) {
            $scalefactor = 100.0 / $scale;
            array_walk_recursive($ydata, function (&$n) use ($scalefactor) {
                $n *= $scalefactor;
            });
        }

        // Lables for the two axes.
        $x_axis_labels = implode('|', $x_axis);
        $y_axis_labels = '';

        if ($y_axis_type === self::Y_AXIS_PERCENT) {
            // Draw 10 intervals on the Y axis.
            $intervals = 10;
            for ($i = 1; $i <= $intervals; $i++) {
                if ($ymax <= 20.0) {
                    $y_axis_labels .= round($ymax * $i / $intervals, 1) . '|';
                } else {
                    $y_axis_labels .= round($ymax * $i / $intervals, 0) . '|';
                }
            }
        } elseif ($y_axis_type === self::Y_AXIS_NUMBERS) {
            // Draw up to 10 intervals on the Y axis.
            $intervals = min(10, $ymax);
            for ($i = 1; $i <= $intervals; $i++) {
                $y_axis_labels .= round($ymax * $i / $intervals, 0) . '|';
            }
        }

        $data = implode('|', array_map(function (array $x) {
            return implode(',', $x);
        }, $ydata));

        $attributes = [
            'chbh' => $chbh,
            'chd'  => 't:' . $data,
            'chf'  => 'bg,s,ffffff00|c,s,ffffff00',
            'chco' => implode(',', $colors),
            'chs'  => self::CHART_WIDTH . 'x' . self::CHART_HEIGHT,
            'cht'  => 'bvg',
            'chtt' => $chart_title,
            'chxl' => '0:|' . $x_axis_labels . '|1:||||' . $x_axis_title . '|2:|0|' . $y_axis_labels . '3:||' . $y_axis_title . '|',
            'chxt' => 'x,x,y,y',
        ];

        // More than one Z axis?  Show a legend for them.
        if (count($z_axis) > 1) {
            $attributes['chdl'] = implode('|', $z_axis);
        }

        $url = Html::url('https://chart.googleapis.com/chart', $attributes);

        return '<img src="' . e($url) . '" class="img-fluid" alt="' . e($chart_title) . '">';
    }
}
