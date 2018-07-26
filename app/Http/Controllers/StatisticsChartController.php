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
        global $x_axis, $ydata, $xmax, $z_boundaries, $zmax;

        $x_axis_type = (int) $request->get('x-as');
        $y_axis_type = (int) $request->get('y-as');
        $z_axis_type = (int) $request->get('z-as');
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

                $x_axis = $this->xAxisMonths();
                $xmax   = count($x_axis);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = [I18N::translate('Male'), I18N::translate('Female')];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, count($x_axis), 0));

                $this->monthOfBirth($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_DEATH_MONTH:
                $chart_title  = I18N::translate('Month of death');
                $x_axis_title = I18N::translate('Month');

                $x_axis = $this->xAxisMonths();
                $xmax   = count($x_axis);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = [I18N::translate('Male'), I18N::translate('Female')];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, count($x_axis), 0));

                $this->monthOfDeath($z_axis_type, $z_boundaries, $stats, $z_axis_type === self::Z_AXIS_SEX);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_MARRIAGE_MONTH:
                $chart_title  = I18N::translate('Month of marriage');
                $x_axis_title = I18N::translate('Month');

                $x_axis = $this->xAxisMonths();
                $xmax   = count($x_axis);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, count($x_axis), 0));

                $this->monthOfMarriage($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_FIRST_CHILD_MONTH:
                $chart_title  = I18N::translate('Month of birth of first child in a relation');
                $x_axis_title = I18N::translate('Month');

                $x_axis = $this->xAxisMonths();
                $xmax   = count($x_axis);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Children');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = [I18N::translate('Male'), I18N::translate('Female')];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, count($x_axis), 0));

                $this->monthOfBirthOfFirstChild($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_FIRST_MARRIAGE_MONTH:
                $chart_title  = I18N::translate('Month of first marriage');
                $x_axis_title = I18N::translate('Month');

                $x_axis = $this->xAxisMonths();
                $xmax   = count($x_axis);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, count($x_axis), 0));

                $this->monthOfFirstMarriage($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_DEATH:
                $chart_title  = I18N::translate('Average age at death');
                $x_axis_title = I18N::translate('age');

                $boundaries_x_axis = $request->get('x-axis-boundaries-ages');
                $x_axis            = $this->calculateAxis($boundaries_x_axis, $x_axis_type);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = [I18N::translate('Male'), I18N::translate('Female')];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, count($x_axis), 0));

                $this->averageAgeAtDeath($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_MARRIAGE:
                $chart_title  = I18N::translate('Age in year of marriage');
                $x_axis_title = I18N::translate('age');

                $boundaries_x_axis = $request->get('x-axis-boundaries-ages_m');
                $x_axis            = $this->calculateAxis($boundaries_x_axis, $x_axis_type);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = [I18N::translate('Male'), I18N::translate('Female')];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, $xmax, 0));

                $this->ageAtMarriage($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_AGE_AT_FIRST_MARRIAGE:
                $chart_title  = I18N::translate('Age in year of first marriage');
                $x_axis_title = I18N::translate('age');

                $boundaries_x_axis = $request->get('x-axis-boundaries-ages_m');
                $x_axis            = $this->calculateAxis($boundaries_x_axis, $x_axis_type);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Individuals');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_SEX:
                        $z_axis = [I18N::translate('Male'), I18N::translate('Female')];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, $xmax, 0));

                $this->ageAtFirstMarriage($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

            case self::X_AXIS_NUMBER_OF_CHILDREN:
                $chart_title  = I18N::translate('Number of children');
                $x_axis_title = I18N::translate('Children');

                $boundaries_x_axis = '1,2,3,4,5,6,7,8,9,10';
                $x_axis            = $this->calculateAxis($boundaries_x_axis, $x_axis_type);

                switch ($y_axis_type) {
                    case self::Y_AXIS_NUMBERS:
                        $y_axis_title = I18N::translate('Families');
                        break;
                    case self::Y_AXIS_PERCENT:
                        $y_axis_title = '%';
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                switch ($z_axis_type) {
                    case self::Z_AXIS_ALL:
                        $z_axis = [I18N::translate(I18N::translate('All'))];
                        break;
                    case self::Z_AXIS_TIME:
                        $boundaries_csv = $request->get('z-axis-boundaries-periods');
                        $z_axis         = $this->createAxisFromYearBoundaries($boundaries_csv);
                        break;
                    default:
                        throw new NotFoundHttpException;
                }

                $z_boundaries = array_keys($z_axis);
                $zmax         = count($z_axis);

                // Initialise the counts to zero.
                $ydata = array_fill(0, count($z_axis), array_fill(0, $xmax, 0));
                $this->numberOfChildren($z_axis_type, $z_boundaries, $stats);

                return new Response($this->myPlot($chart_title, $x_axis, $x_axis_title, $ydata, $y_axis_title, $z_axis, $y_axis_type));

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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfBirth($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->statsBirthQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, false);
                    }
                }
            }
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
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
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * Month of birth of first child in a relation
     *
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfBirthOfFirstChild($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->monthFirstChildQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, false);
                    }
                }
            }
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
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
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     * @param bool  $zgiven
     */
    private function monthOfDeath($z_axis_type, array $z_boundaries, Stats $stats, bool $zgiven)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->statsDeathQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, $zgiven);
                    }
                }
            }
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
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
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfMarriage($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->statsMarrQuery(false, false);
            foreach ($num as $values) {
                foreach (self::MONTHS as $key => $month) {
                    if ($month === $values['d_month']) {
                        $this->fillYData(0, $key, $values['total'], true, false);
                    }
                }
            }
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function monthOfFirstMarriage($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
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
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function averageAgeAtDeath($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->statsAgeQuery(false, 'DEAT');
            foreach ($num as $values) {
                foreach ($values as $age_value) {
                    $this->fillYData(0, (int) ($age_value / 365.25), 1, false, false);
                }
            }
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
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
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function ageAtMarriage($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->statsMarrAgeQuery(false, 'M');
            foreach ($num as $values) {
                $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, false);
            }
            $num = $stats->statsMarrAgeQuery(false, 'F');
            foreach ($num as $values) {
                $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, false);
            }
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
            $num = $stats->statsMarrAgeQuery(false, 'M');
            foreach ($num as $values) {
                $this->fillYData(0, (int) ($values['age'] / 365.25), 1, false, true);
            }
            $num = $stats->statsMarrAgeQuery(false, 'F');
            foreach ($num as $values) {
                $this->fillYData(1, (int) ($values['age'] / 365.25), 1, false, true);
            }
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function ageAtFirstMarriage($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
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
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
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
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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
     * @param int   $z_axis_type
     * @param int[] $z_boundaries
     * @param Stats $stats
     */
    private function numberOfChildren($z_axis_type, array $z_boundaries, Stats $stats)
    {
        if ($z_axis_type === self::Z_AXIS_ALL) {
            $num = $stats->statsChildrenQuery(false);
            foreach ($num as $values) {
                $this->fillYData(0, $values['f_numchil'], $values['total'], false, false);
            }
        } elseif ($z_axis_type === self::Z_AXIS_SEX) {
            $num = $stats->statsChildrenQuery(false, 'M');
            foreach ($num as $values) {
                $this->fillYData(0, $values['num'], $values['total'], false, false);
            }
            $num = $stats->statsChildrenQuery(false, 'F');
            foreach ($num as $values) {
                $this->fillYData(1, $values['num'], $values['total'], false, false);
            }
        } elseif ($z_axis_type === self::Z_AXIS_TIME) {
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

        // The chart data
        if ($y_axis_type === self::Y_AXIS_PERCENT) {
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

        $attributes = [
            'chbh' => $chbh,
            'chd'  => 't:' . implode('|', array_map(function (array $x) { return implode(',', $x); }, $ydata)),
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

    /**
     * Create the X axis.
     *
     * @param string $x_axis_boundaries
     * @param int    $x_axis_type
     *
     * @return array
     */
    private function calculateAxis(string $x_axis_boundaries, int $x_axis_type): array
    {
        global $xmax, $x_boundaries;

        // Calculate xdata and zdata elements out of chart values
        $hulpar = explode(',', $x_axis_boundaries);
        $i      = 1;
        if ($x_axis_type === self::X_AXIS_NUMBER_OF_CHILDREN && $hulpar[0] == 1) {
            $axis[0] = 0;
        } else {
            $axis[0] = $this->formatRangeOfNumbers(0, $hulpar[0]);
        }
        $x_boundaries[0] = $hulpar[0] - 1;
        while (isset($hulpar[$i])) {
            $i1 = $i - 1;
            if (($hulpar[$i] - $hulpar[$i1]) === 1) {
                $axis[$i]         = $hulpar[$i1];
                $x_boundaries[$i] = $hulpar[$i1];
            } elseif ($hulpar[$i1] === $hulpar[0]) {
                $axis[$i]         = $this->formatRangeOfNumbers($hulpar[$i1], $hulpar[$i]);
                $x_boundaries[$i] = $hulpar[$i];
            } else {
                $axis[$i]         = $this->formatRangeOfNumbers($hulpar[$i1] + 1, $hulpar[$i]);
                $x_boundaries[$i] = $hulpar[$i];
            }
            $i++;
        }
        $axis[$i]         = $hulpar[$i - 1];
        $x_boundaries[$i] = $hulpar[$i - 1];
        if ($hulpar[$i - 1] === $i) {
            $xmax = $i + 1;
        } else {
            $xmax = $i;
        }
        $axis[$xmax]         = /* I18N: Label on a graph; 40+ means 40 or more */
            I18N::translate('%s+', I18N::number($hulpar[$i - 1]));
        $x_boundaries[$xmax] = PHP_INT_MAX;
        $xmax                = $xmax + 1;

        return $axis;
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
     * Convert a list of N year-boundaries into N+1 year-ranges for the z-axis.
     *
     * @param string $boundaries_csv
     *
     * @return string[]
     */
    private function createAxisFromYearBoundaries($boundaries_csv): array
    {
        $boundaries = explode(',', $boundaries_csv);

        $axis = [];
        foreach ($boundaries as $n => $boundary) {
            if ($n === 0) {
                $date = new Date('BEF ' . $boundary);
            } else {
                $date = new Date('BET ' . $boundaries[$n - 1] . ' AND ' . $boundary);
            }
            $axis[$boundary - 1] = strip_tags($date->display());
        }

        $date              = new Date('AFT ' . $boundaries[count($boundaries) - 1]);
        $axis[PHP_INT_MAX] = strip_tags($date->display());

        return $axis;
    }
}
