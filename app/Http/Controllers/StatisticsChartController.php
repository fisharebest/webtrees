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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * A chart showing the various statistics about the family tree.
 */
class StatisticsChartController extends AbstractChartController
{
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

    const MONTHS = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

    /**
     * A form to request the chart parameters.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function page(Request $request): Response
    {
        /** @var Tree $tree */
        $tree = $request->attributes->get('tree');

        $this->checkModuleIsActive($tree, 'statistics_chart');

        $title = I18N::translate('Statistics');

        return $this->viewResponse('statistics-page', [
            'title' => $title,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function chartIndividuals(Request $request): Response
    {
        /** @var Tree $tree */
        $tree = $request->attributes->get('tree');

        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-individuals', [
            'show_oldest_living' => Auth::check(),
            'stats'              => new Stats($tree),
        ]);

        return new Response($html);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function chartFamilies(Request $request): Response
    {
        /** @var Tree $tree */
        $tree = $request->attributes->get('tree');

        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-families', [
            'stats' => new Stats($tree),
        ]);

        return new Response($html);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function chartOther(Request $request): Response
    {
        /** @var Tree $tree */
        $tree = $request->attributes->get('tree');

        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-other', [
            'stats' => new Stats($tree),
        ]);

        return new Response($html);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function chartCustomOptions(Request $request): Response
    {
        /** @var Tree $tree */
        $tree = $request->attributes->get('tree');

        $this->checkModuleIsActive($tree, 'statistics_chart');

        $html = view('statistics-chart-custom');

        return new Response($html);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function chartCustomChart(Request $request): Response
    {
        /** @var Tree $tree */
        $tree = $request->attributes->get('tree');

        $this->checkModuleIsActive($tree, 'statistics_chart');

        // @TODO - remove globals
        global $legend, $xdata, $ydata, $xmax, $z_boundaries;

        $x_axis       = (int) $request->get('x-as');
        $y_axis       = (int) $request->get('y-as');
        $z_axis       = (int) $request->get('z-as');
        $stats        = new Stats($tree);
        $z_boundaries = [];
        $legend       = [];

        switch ($x_axis) {
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
                $title  = I18N::translate('Month of birth');
                $xtitle = I18N::translate('Month');
                $ytitle = I18N::translate('numbers');

                $xdata = $this->xAxisMonths();
                $xmax  = count($xdata);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Individuals');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, count($xdata), 0));

                $this->monthOfBirth($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_DEATH_MONTH:
                $title  = I18N::translate('Month of death');
                $xtitle = I18N::translate('Month');
                $ytitle = I18N::translate('numbers');

                $xdata = $this->xAxisMonths();
                $xmax  = count($xdata);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Individuals');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, count($xdata), 0));

                $this->monthOfDeath($z_axis, $z_boundaries, $stats, $z_axis === self::Z_AXIS_SEX);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_MARRIAGE_MONTH:
                $title  = I18N::translate('Month of marriage');
                $xtitle = I18N::translate('Month');
                $ytitle = I18N::translate('numbers');

                $xdata = $this->xAxisMonths();
                $xmax  = count($xdata);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Families');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    // This option is not used - it will be the same for male/female.
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, count($xdata), 0));

                $this->monthOfMarriage($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_FIRST_CHILD_MONTH:
                $title  = I18N::translate('Month of birth of first child in a relation');
                $xtitle = I18N::translate('Month');
                $ytitle = I18N::translate('numbers');

                $xdata = $this->xAxisMonths();
                $xmax  = count($xdata);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Children');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, count($xdata), 0));

                $this->monthOfBirthOfFirstChild($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_FIRST_MARRIAGE_MONTH:
                $title  = I18N::translate('Month of first marriage');
                $xtitle = I18N::translate('Month');
                $ytitle = I18N::translate('numbers');

                $xdata = $this->xAxisMonths();
                $xmax  = count($xdata);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Families');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    // This option is not used - it will be the same for male/female.
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, count($xdata), 0));

                $this->monthOfFirstMarriage($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_AGE_AT_DEATH:
                $title  = I18N::translate('Average age at death');
                $xtitle = I18N::translate('age');
                $ytitle = I18N::translate('numbers');

                $boundaries_x_axis = $request->get('x-axis-boundaries-ages');
                $this->calculateAxis($boundaries_x_axis);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Individuals');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, count($xdata), 0));

                $this->averageAgeAtDeath($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_AGE_AT_MARRIAGE:
                $title  = I18N::translate('Age in year of marriage');
                $xtitle = I18N::translate('age');
                $ytitle = I18N::translate('numbers');

                $boundaries_x_axis = $request->get('x-axis-boundaries-ages_m');
                $this->calculateAxis($boundaries_x_axis);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Individuals');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, $xmax, 0));

                $this->ageAtMarriage($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_AGE_AT_FIRST_MARRIAGE:
                $title  = I18N::translate('Age in year of first marriage');
                $xtitle = I18N::translate('age');
                $ytitle = I18N::translate('numbers');

                $boundaries_x_axis = $request->get('x-axis-boundaries-ages_m');
                $this->calculateAxis($boundaries_x_axis);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Individuals');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, $xmax, 0));

                $this->ageAtFirstMarriage($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            case self::X_AXIS_NUMBER_OF_CHILDREN:
                $title  = I18N::translate('Number of children');
                $xtitle = I18N::translate('children');
                $ytitle = I18N::translate('numbers');

                $boundaries_x_axis = $request->get('x-axis-boundaries-numbers');
                $this->calculateAxis($boundaries_x_axis);

                if ($y_axis === self::Y_AXIS_NUMBERS) {
                    $ytitle = I18N::translate('Families');
                } elseif ($y_axis === self::Y_AXIS_PERCENT) {
                    $ytitle = I18N::translate('percentage');
                }

                if ($z_axis === self::Z_AXIS_ALL) {
                    $legend       = [I18N::translate(I18N::translate('All'))];
                    $z_boundaries = [PHP_INT_MAX];
                } elseif ($z_axis === self::Z_AXIS_SEX) {
                    $legend = [I18N::translate('Male'), I18N::translate('Female')];
                } elseif ($z_axis === self::Z_AXIS_TIME) {
                    $boundaries_z_axis = $request->get('z-axis-boundaries-periods');
                    $legend            = $this->calculateLegend($boundaries_z_axis);
                }

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($legend), array_fill(0, $xmax, 0));

                $this->numberOfChildren($z_axis, $z_boundaries, $stats);

                return new Response($this->myPlot($title, $xdata, $xtitle, $ydata, $ytitle, $legend, $y_axis));

            default:
                throw new NotFoundHttpException;
                break;
        }
    }

    /**
     * Labels for the X axis
     *
     * @return string[]
     */
    private function xAxisMonths(): array
    {
        return [
            GregorianDate::monthNameNominativeCase(1, false),
            GregorianDate::monthNameNominativeCase(2, false),
            GregorianDate::monthNameNominativeCase(3, false),
            GregorianDate::monthNameNominativeCase(4, false),
            GregorianDate::monthNameNominativeCase(5, false),
            GregorianDate::monthNameNominativeCase(6, false),
            GregorianDate::monthNameNominativeCase(7, false),
            GregorianDate::monthNameNominativeCase(8, false),
            GregorianDate::monthNameNominativeCase(9, false),
            GregorianDate::monthNameNominativeCase(10, false),
            GregorianDate::monthNameNominativeCase(11, false),
            GregorianDate::monthNameNominativeCase(12, false),
        ];
    }

    /**
     * Month of birth
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfBirth($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->statsBirthQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, false);
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num = $stats->statsBirthQuery(false, true);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        if ($values['i_sex'] === 'M') {
                            $this->fillYData(0, $key, $values['total'], true, true);
                        } elseif ($values['i_sex'] === 'F') {
                            $this->fillYData(1, $key, $values['total'], true, true);
                        }
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsBirthQuery(false, false, $zstart, $boundary);
                foreach ($num as $values) {
                    foreach (self::MONTHS as $key => $month) {
                        if ($month === $values['d_month']) {
                            $this->fillYData($boundary, $key, $values['total'], true, false);
                        }
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Find the correct place in the chart for a given datapoint.
     *
     * @param $value
     * @param $legend
     *
     * @return int|string
     */
    private function lookup($value, $legend)
    {
        foreach (array_keys($legend) as $key) {
            if ($value <= $key) {
                return $key;
            }
        }

        throw new LogicException('Found data outside range of expected values');
    }

    /**
     * Month of birth of first child in a relation
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfBirthOfFirstChild($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->monthFirstChildQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, false);
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num = $stats->monthFirstChildQuery(false, true);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        if ($values['i_sex'] === 'M') {
                            $this->fillYData(0, $key, $values['total'], true, true);
                        } elseif ($values['i_sex'] === 'F') {
                            $this->fillYData(1, $key, $values['total'], true, true);
                        }
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->monthFirstChildQuery(false, false, $zstart, $boundary);
                foreach ($num as $values) {
                    foreach (self::MONTHS as $key => $month) {
                        if ($month === $values['d_month']) {
                            $this->fillYData($boundary, $key, $values['total'], true, false);
                        }
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Month of death
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     * @param bool  $zgiven
     */
    private function monthOfDeath($z_axis, array $z_boundaries, Stats $stats, bool $zgiven)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->statsDeathQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, $zgiven);
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num = $stats->statsDeathQuery(false, true);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        if ($values['i_sex'] === 'M') {
                            $this->fillYData(0, $key, $values['total'], true, $zgiven);
                        } elseif ($values['i_sex'] === 'F') {
                            $this->fillYData(1, $key, $values['total'], true, $zgiven);
                        }
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsDeathQuery(false, false, $zstart, $boundary);
                foreach ($num as $values) {
                    foreach (self::MONTHS as $key => $month) {
                        if ($month === $values['d_month']) {
                            $this->fillYData($boundary, $key, $values['total'], true, $zgiven);
                        }
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Month of marriage
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfMarriage($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->statsMarrQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, false);
                    }
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsMarrQuery(false, false, $zstart, $boundary);
                foreach ($num as $values) {
                    foreach (self::MONTHS as $key => $month) {
                        if ($month === $values['d_month']) {
                            $this->fillYData($boundary, $key, $values['total'], true, false);
                        }
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Month of first marriage
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfFirstMarriage($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num  = $stats->statsMarrQuery(false, true);
            $indi = [];
            $fam  = [];
            foreach ($num as $values) {
                if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
                    foreach (self::MONTHS as $key => $month) {
                        if ($month === $values['month']) {
                            $this->fillYData(0, $key, 1, true, false);
                        }
                    }
                    $indi[] = $values['indi'];
                    $fam[]  = $values['fams'];
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            $indi   = [];
            $fam    = [];
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsMarrQuery(false, true, $zstart, $boundary);
                foreach ($num as $values) {
                    if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
                        foreach (self::MONTHS as $key => $month) {
                            if ($month === $values['month']) {
                                $this->fillYData($boundary, $key, 1, true, false);
                            }
                        }
                        $indi[] = $values['indi'];
                        $fam[]  = $values['fams'];
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Average age at death
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function averageAgeAtDeath($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->statsAgeQuery(false, 'DEAT');
            foreach ($num as $values) {
                foreach ($values as $age_value) {
                    $this->fillYData(0, (int) ($age_value / 365.25), 1, false, false);
                }
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num = $stats->statsAgeQuery(false, 'DEAT', 'M');
            foreach ($num as $values) {
                foreach ($values as $age_value) {
                    $this->fillYData(0, (int) ($age_value / 365.25), 1, false, true);
                }
            }
            $num = $stats->statsAgeQuery(false, 'DEAT', 'F');
            foreach ($num as $values) {
                foreach ($values as $age_value) {
                    $this->fillYData(1, (int) ($age_value / 365.25), 1, false, true);
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsAgeQuery(false, 'DEAT', 'BOTH', $zstart, $boundary);
                foreach ($num as $values) {
                    foreach ($values as $age_value) {
                        $this->fillYData($boundary, (int) ($age_value / 365.25), 1, false, false);
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Age in year of marriage
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function ageAtMarriage($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->statsMarrAgeQuery(false, 'M');
            foreach ($num as $values) {
                $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, false);
            }
            $num = $stats->statsMarrAgeQuery(false, 'F');
            foreach ($num as $values) {
                $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, false);
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num = $stats->statsMarrAgeQuery(false, 'M');
            foreach ($num as $values) {
                $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, true);
            }
            $num = $stats->statsMarrAgeQuery(false, 'F');
            foreach ($num as $values) {
                $this->fillYData(1, (int) ($values['age'] / 365.25), 1, false, true);
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
                foreach ($num as $values) {
                    $this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, false, false);
                }
                $num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
                foreach ($num as $values) {
                    $this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, false, false);
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Age in year of first marriage
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function ageAtFirstMarriage($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num  = $stats->statsMarrAgeQuery(false, 'M');
            $indi = [];
            foreach ($num as $values) {
                if (!in_array($values['d_gid'], $indi)) {
                    $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, false);
                    $indi[] = $values['d_gid'];
                }
            }
            $num  = $stats->statsMarrAgeQuery(false, 'F');
            $indi = [];
            foreach ($num as $values) {
                if (!in_array($values['d_gid'], $indi)) {
                    $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, false);
                    $indi[] = $values['d_gid'];
                }
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num  = $stats->statsMarrAgeQuery(false, 'M');
            $indi = [];
            foreach ($num as $values) {
                if (!in_array($values['d_gid'], $indi)) {
                    $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, true);
                    $indi[] = $values['d_gid'];
                }
            }
            $num  = $stats->statsMarrAgeQuery(false, 'F');
            $indi = [];
            foreach ($num as $values) {
                if (!in_array($values['d_gid'], $indi)) {
                    $this->fillYData(1, (int) ($values['age'] / 365.25), 1, false, true);
                    $indi[] = $values['d_gid'];
                }
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            $indi   = [];
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
                foreach ($num as $values) {
                    if (!in_array($values['d_gid'], $indi)) {
                        $this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, false, false);
                        $indi[] = $values['d_gid'];
                    }
                }
                $num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
                foreach ($num as $values) {
                    if (!in_array($values['d_gid'], $indi)) {
                        $this->fillYData($boundary, (int) ($values['age'] / 365.25), 1, false, false);
                        $indi[] = $values['d_gid'];
                    }
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Number of children
     *
     * @param int   $z_axis
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function numberOfChildren($z_axis, array $z_boundaries, Stats $stats)
    {
        if ($z_axis === self::Z_AXIS_ALL) {
            $num = $stats->statsChildrenQuery(false);
            foreach ($num as $values) {
                $this->fillYData(0, $values['f_numchil'], $values['total'], false, false);
            }
        } elseif ($z_axis === self::Z_AXIS_SEX) {
            $num = $stats->statsChildrenQuery(false, 'M');
            foreach ($num as $values) {
                $this->fillYData(0, $values['num'], $values['total'], false, false);
            }
            $num = $stats->statsChildrenQuery(false, 'F');
            foreach ($num as $values) {
                $this->fillYData(1, $values['num'], $values['total'], false, false);
            }
        } elseif ($z_axis === self::Z_AXIS_TIME) {
            $zstart = 0;
            foreach ($z_boundaries as $boundary) {
                $num = $stats->statsChildrenQuery(false, 'BOTH', $zstart, $boundary);
                foreach ($num as $values) {
                    $this->fillYData($boundary, $values['f_numchil'], $values['total'], false, false);
                }
                $zstart = $boundary + 1;
            }
        }
    }

    /**
     * Calculate the Y axis.
     *
     * @param int  $z
     * @param int  $x
     * @param int  $val
     * @param bool $xgiven
     * @param bool $zgiven
     */
    private function fillYData($z, $x, $val, bool $xgiven, bool $zgiven)
    {
        global $ydata, $xmax, $x_boundaries, $zmax, $z_boundaries;
        //-- calculate index $i out of given z value
        //-- calculate index $j out of given x value
        if ($xgiven) {
            $j = $x;
        } else {
            $j = 0;
            while (($x > $x_boundaries[$j]) && ($j < $xmax)) {
                $j++;
            }
        }
        if ($zgiven) {
            $i = $z;
        } else {
            $i = 0;
            while (($z > $z_boundaries[$i]) && ($i < $zmax)) {
                $i++;
            }
        }
        if (isset($ydata[$i][$j])) {
            $ydata[$i][$j] += $val;
        } else {
            $ydata[$i][$j] = $val;
        }
    }

    /**
     * Plot the data.
     *
     * @param string   $chart_title
     * @param int[][]  $xdata
     * @param string   $xtitle
     * @param int[][]  $ydata
     * @param string   $ytitle
     * @param string[] $legend
     * @param int      $y_axis
     *
     * @return string
     */
    private function myPlot(string $chart_title, array $xdata, string $xtitle, array $ydata, string $ytitle, array $legend, int $y_axis): string
    {
        $stop = count($ydata);

        if ($y_axis === self::Y_AXIS_PERCENT) {
            // Normalise each (non-zero!) set of data to total 100%
            array_walk($ydata, function (array &$x) {
                $sum = array_sum($x);
                if ($sum > 0) {
                    $x = array_map(function ($y) use ($sum) { return $y * 100.0 / $sum; }, $x);
                }
            });
        }

        // Find the maximum value, so we can draw the scale
        $ymax = max(array_map(function (array $x) { return max($x); }, $ydata));

        // Google charts API requires data to be scaled 0 - 100.
        $scale = max(array_map(function (array $x) { return max($x); }, $ydata));

        if ($scale > 0) {
            $scalefactor = 100.0 / $scale;
            array_walk_recursive($ydata, function (& $n) use ($scalefactor) { $n *= $scalefactor; });
        }

        $datastring = 'chd=t:' . implode('|', array_map(function (array $x) { return implode(',', $x); }, $ydata));

        $colors = [
            '0000FF',
            'FFA0CB',
            '9F00FF',
            'FF7000',
            '905030',
            'FF0000',
            '00FF00',
            'F0F000',
        ];

        $colorstring = 'chco=';
        for ($i = 0; $i < $stop; $i++) {
            if (isset($colors[$i])) {
                $colorstring .= $colors[$i];
                if ($i !== ($stop - 1)) {
                    $colorstring .= ',';
                }
            }
        }

        $imgurl = 'https://chart.googleapis.com/chart?cht=bvg&amp;chs=950x300&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=' . rawurlencode($chart_title) . '&amp;' . $datastring . '&amp;' . $colorstring . '&amp;chbh=';
        if (count($ydata) > 3) {
            $imgurl .= '5,1';
        } elseif (count($ydata) < 2) {
            $imgurl .= '45,1';
        } else {
            $imgurl .= '20,3';
        }
        $imgurl .= '&amp;chxt=x,x,y,y&amp;chxl=0:|';
        foreach ($xdata as $data) {
            $imgurl .= rawurlencode($data) . '|';
        }

        $imgurl .= '1:||||' . rawurlencode($xtitle) . '|2:|';
        $imgurl .= '0|';

        if ($y_axis === self::Y_AXIS_PERCENT) {
            for ($i = 1; $i < 11; $i++) {
                if ($ymax < 11) {
                    $imgurl .= round($ymax * $i / 10, 1) . '|';
                } else {
                    $imgurl .= round($ymax * $i / 10, 0) . '|';
                }
            }
            $imgurl .= '3:||%|';
        } elseif ($y_axis === self::Y_AXIS_NUMBERS) {
            if ($ymax < 11) {
                for ($i = 1; $i < $ymax + 1; $i++) {
                    $imgurl .= round($ymax * $i / ($ymax), 0) . '|';
                }
            } else {
                for ($i = 1; $i < 11; $i++) {
                    $imgurl .= round($ymax * $i / 10, 0) . '|';
                }
            }
            $imgurl .= '3:||' . rawurlencode($ytitle) . '|';
        }

        // Only show legend if y-data is non-2-dimensional
        if (count($ydata) > 1) {
            $imgurl .= '&amp;chdl=';
            foreach ($legend as $i => $data) {
                if ($i > 0) {
                    $imgurl .= '|';
                }
                $imgurl .= rawurlencode($data);
            }
        }

        return '<img src="' . $imgurl . '" width="950" height="300" alt="' . e($chart_title) . '">';
    }

    /**
     * Create the X axis.
     *
     * @param string $x_axis_boundaries
     */
    private function calculateAxis($x_axis_boundaries)
    {
        global $x_axis, $xdata, $xmax, $x_boundaries;

        // Calculate xdata and zdata elements out of chart values
        $hulpar = explode(',', $x_axis_boundaries);
        $i      = 1;
        if ($x_axis === 21 && $hulpar[0] == 1) {
            $xdata[0] = 0;
        } else {
            $xdata[0] = $this->formatRangeOfNumbers(0, $hulpar[0]);
        }
        $x_boundaries[0] = $hulpar[0] - 1;
        while (isset($hulpar[$i])) {
            $i1 = $i - 1;
            if (($hulpar[$i] - $hulpar[$i1]) === 1) {
                $xdata[$i]        = $hulpar[$i1];
                $x_boundaries[$i] = $hulpar[$i1];
            } elseif ($hulpar[$i1] === $hulpar[0]) {
                $xdata[$i]        = $this->formatRangeOfNumbers($hulpar[$i1], $hulpar[$i]);
                $x_boundaries[$i] = $hulpar[$i];
            } else {
                $xdata[$i]        = $this->formatRangeOfNumbers($hulpar[$i1] + 1, $hulpar[$i]);
                $x_boundaries[$i] = $hulpar[$i];
            }
            $i++;
        }
        $xdata[$i]        = $hulpar[$i - 1];
        $x_boundaries[$i] = $hulpar[$i - 1];
        if ($hulpar[$i - 1] === $i) {
            $xmax = $i + 1;
        } else {
            $xmax = $i;
        }
        $xdata[$xmax]        = /* I18N: Label on a graph; 40+ means 40 or more */
            I18N::translate('%s+', I18N::number($hulpar[$i - 1]));
        $x_boundaries[$xmax] = PHP_INT_MAX;
        $xmax                = $xmax + 1;
    }

    /**
     * A range of integers.
     *
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    private function formatRangeOfNumbers($x, $y): string
    {
        return /* I18N: A range of numbers */
            I18N::translate(
                '%1$s–%2$s',
                I18N::number($x),
                I18N::number($y)
            );
    }

    /**
     * Generate the Z axis legend from a list of boundary years
     *
     * @param string $boundaries_z_axis
     *
     * @return string[]
     */
    private function calculateLegend($boundaries_z_axis): array
    {
        global $zmax, $z_boundaries;

        // calculate the legend values
        $hulpar          = explode(',', $boundaries_z_axis);
        $i               = 1;
        $date            = new Date('BEF ' . $hulpar[0]);
        $legend[0]       = strip_tags($date->display());
        $z_boundaries[0] = $hulpar[0] - 1;

        while (isset($hulpar[$i])) {
            $date             = new Date('BET ' . $hulpar[$i - 1] . ' AND ' . ($hulpar[$i] - 1));
            $legend[$i]       = strip_tags($date->display());
            $z_boundaries[$i] = $hulpar[$i] - 1;
            $i++;
        }
        $date             = new Date('AFT ' . $hulpar[$i - 1]);
        $legend[$i]       = strip_tags($date->display());
        $z_boundaries[$i] = PHP_INT_MAX;

        $zmax = count($legend);

        return $legend;
    }
}
