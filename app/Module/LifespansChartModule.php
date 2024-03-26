<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\ColorGenerator;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function array_intersect;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_unique;
use function count;
use function date;
use function explode;
use function implode;
use function intdiv;
use function max;
use function md5;
use function min;
use function redirect;
use function response;
use function route;
use function usort;
use function view;

use const PHP_INT_MAX;

/**
 * Class LifespansChartModule
 */
class LifespansChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL = '/tree/{tree}/lifespans';

    // In theory, only "@" is a safe separator, but it gives longer and uglier URLs.
    // Unless some other application generates XREFs with a ".", we are safe.
    protected const SEPARATOR = '.';

    // Defaults
    protected const DEFAULT_PARAMETERS = [];

    // Parameters for generating colors
    protected const RANGE      = 120; // degrees
    protected const SATURATION = 100; // percent
    protected const LIGHTNESS  = 30; // percent
    protected const ALPHA      = 0.25;

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
            ->get(static::class, static::ROUTE_URL, $this)
            ->allows(RequestMethodInterface::METHOD_POST);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Lifespans');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “LifespansChart” module */
        return I18N::translate('A chart of individuals’ lifespans.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-lifespan';
    }

    /**
     * The URL for this chart.
     *
     * @param Individual                                $individual
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(static::class, [
                'tree'  => $individual->tree()->name(),
                'xrefs' => $individual->xref(),
            ] + $parameters + self::DEFAULT_PARAMETERS);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $user  = Validator::attributes($request)->user();
        $xrefs = Validator::queryParams($request)->string('xrefs', '');
        $ajax  = Validator::queryParams($request)->boolean('ajax', false);

        if ($xrefs === '') {
            try {
                // URLs created by webtrees 2.0 and earlier used an array.
                $xrefs = Validator::queryParams($request)->array('xrefs');
            } catch (HttpBadRequestException) {
                // Not a 2.0 request, just an empty parameter.
                $xrefs = [];
            }
        } else {
            $xrefs = explode(self::SEPARATOR, $xrefs);
        }

        $addxref   = Validator::parsedBody($request)->string('addxref', '');
        $addfam    = Validator::parsedBody($request)->boolean('addfam', false);
        $place_id  = Validator::parsedBody($request)->integer('place_id', 0);
        $start     = Validator::parsedBody($request)->string('start', '');
        $end       = Validator::parsedBody($request)->string('end', '');

        $place      = Place::find($place_id, $tree);
        $start_date = new Date($start);
        $end_date   = new Date($end);

        $xrefs = array_unique($xrefs);

        // Add an individual, and family members
        $individual = Registry::individualFactory()->make($addxref, $tree);
        if ($individual !== null) {
            $xrefs[] = $addxref;
            if ($addfam) {
                $xrefs = array_merge($xrefs, $this->closeFamily($individual));
            }
        }

        // Select by date and/or place.
        if ($place_id !== 0 && $start_date->isOK() && $end_date->isOK()) {
            $date_xrefs  = $this->findIndividualsByDate($start_date, $end_date, $tree);
            $place_xrefs = $this->findIndividualsByPlace($place, $tree);
            $xrefs       = array_intersect($date_xrefs, $place_xrefs);
        } elseif ($start_date->isOK() && $end_date->isOK()) {
            $xrefs = $this->findIndividualsByDate($start_date, $end_date, $tree);
        } elseif ($place_id !== 0) {
            $xrefs = $this->findIndividualsByPlace($place, $tree);
        }

        // Filter duplicates and private individuals.
        $xrefs = array_unique($xrefs);
        $xrefs = array_filter($xrefs, static function (string $xref) use ($tree): bool {
            $individual = Registry::individualFactory()->make($xref, $tree);

            return $individual !== null && $individual->canShow();
        });

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree'  => $tree->name(),
                'xrefs' => implode(self::SEPARATOR, $xrefs),
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        if ($ajax) {
            $this->layout = 'layouts/ajax';

            return $this->chart($tree, $xrefs);
        }

        $reset_url = route(static::class, ['tree' => $tree->name()]);

        $ajax_url = route(static::class, [
            'ajax'  => true,
            'tree'  => $tree->name(),
            'xrefs' => implode(self::SEPARATOR, $xrefs),
        ]);

        return $this->viewResponse('modules/lifespans-chart/page', [
            'ajax_url'  => $ajax_url,
            'module'    => $this->name(),
            'reset_url' => $reset_url,
            'title'     => $this->title(),
            'tree'      => $tree,
            'xrefs'     => $xrefs,
        ]);
    }

    /**
     * @param Tree          $tree
     * @param array<string> $xrefs
     *
     * @return ResponseInterface
     */
    protected function chart(Tree $tree, array $xrefs): ResponseInterface
    {
        /** @var Individual[] $individuals */
        $individuals = array_map(static fn (string $xref): Individual|null => Registry::individualFactory()->make($xref, $tree), $xrefs);

        $individuals = array_filter($individuals, static fn (Individual|null $individual): bool => $individual instanceof Individual && $individual->canShow());

        // Sort the array in order of birth year
        usort($individuals, Individual::birthDateComparator());

        // Round to whole decades
        $start_year = intdiv($this->minYear($individuals), 10) * 10;
        $end_year   = intdiv($this->maxYear($individuals) + 9, 10) * 10;

        $lifespans = $this->layoutIndividuals($individuals);

        $callback = static fn (int $carry, object $item): int => max($carry, $item->row);
        $max_rows = array_reduce($lifespans, $callback, 0);

        $count    = count($xrefs);
        $subtitle = I18N::plural('%s individual', '%s individuals', $count, I18N::number($count));

        $html = view('modules/lifespans-chart/chart', [
            'dir'        => I18N::direction(),
            'end_year'   => $end_year,
            'lifespans'  => $lifespans,
            'max_rows'   => $max_rows,
            'start_year' => $start_year,
            'subtitle'   => $subtitle,
        ]);

        return response($html);
    }

    /**
     * Find the latest event year for individuals
     *
     * @param array<Individual> $individuals
     *
     * @return int
     */
    protected function maxYear(array $individuals): int
    {
        $jd = array_reduce($individuals, static function (int $carry, Individual $item): int {
            if ($item->getEstimatedDeathDate()->isOK()) {
                return max($carry, $item->getEstimatedDeathDate()->maximumJulianDay());
            }

            return $carry;
        }, 0);

        $year = $this->jdToYear($jd);

        // Don't show future dates
        return min($year, (int) date('Y'));
    }

    /**
     * Find the earliest event year for individuals
     *
     * @param array<Individual> $individuals
     *
     * @return int
     */
    protected function minYear(array $individuals): int
    {
        $jd = array_reduce($individuals, static function (int $carry, Individual $item): int {
            if ($item->getEstimatedBirthDate()->isOK()) {
                return min($carry, $item->getEstimatedBirthDate()->minimumJulianDay());
            }

            return $carry;
        }, PHP_INT_MAX);

        return $this->jdToYear($jd);
    }

    /**
     * Convert a julian day to a gregorian year
     *
     * @param int $jd
     *
     * @return int
     */
    protected function jdToYear(int $jd): int
    {
        if ($jd === 0) {
            return 0;
        }

        $gregorian = new GregorianCalendar();
        [$y] = $gregorian->jdToYmd($jd);

        return $y;
    }

    /**
     * @param Date $start
     * @param Date $end
     * @param Tree $tree
     *
     * @return array<string>
     */
    protected function findIndividualsByDate(Date $start, Date $end, Tree $tree): array
    {
        return DB::table('individuals')
            ->join('dates', static function (JoinClause $join): void {
                $join
                    ->on('d_file', '=', 'i_file')
                    ->on('d_gid', '=', 'i_id');
            })
            ->where('i_file', '=', $tree->id())
            ->where('d_julianday1', '<=', $end->maximumJulianDay())
            ->where('d_julianday2', '>=', $start->minimumJulianDay())
            ->whereNotIn('d_fact', ['BAPL', 'ENDL', 'SLGC', 'SLGS', '_TODO', 'CHAN'])
            ->pluck('i_id')
            ->all();
    }

    /**
     * @param Place $place
     * @param Tree  $tree
     *
     * @return array<string>
     */
    protected function findIndividualsByPlace(Place $place, Tree $tree): array
    {
        return DB::table('individuals')
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            })
            ->where('i_file', '=', $tree->id())
            ->where('pl_p_id', '=', $place->id())
            ->pluck('i_id')
            ->all();
    }

    /**
     * Find the close family members of an individual.
     *
     * @param Individual $individual
     *
     * @return array<string>
     */
    protected function closeFamily(Individual $individual): array
    {
        $xrefs = [];

        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->children() as $child) {
                $xrefs[] = $child->xref();
            }

            foreach ($family->spouses() as $spouse) {
                $xrefs[] = $spouse->xref();
            }
        }

        foreach ($individual->childFamilies() as $family) {
            foreach ($family->children() as $child) {
                $xrefs[] = $child->xref();
            }

            foreach ($family->spouses() as $spouse) {
                $xrefs[] = $spouse->xref();
            }
        }

        return $xrefs;
    }

    /**
     * @param array<Individual> $individuals
     *
     * @return array<object>
     */
    private function layoutIndividuals(array $individuals): array
    {
        $color_generators = [
            'M' => new ColorGenerator(240, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE * -1),
            'F' => new ColorGenerator(000, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE),
            'U' => new ColorGenerator(120, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE),
        ];

        $current_year = (int) date('Y');

        // Latest year used in each row
        $rows = [];

        $lifespans = [];

        foreach ($individuals as $individual) {
            $birth_jd   = $individual->getEstimatedBirthDate()->minimumJulianDay();
            $birth_year = $this->jdToYear($birth_jd);
            $death_jd   = $individual->getEstimatedDeathDate()->maximumJulianDay();
            $death_year = $this->jdToYear($death_jd);

            // Died before they were born?  Swapping the dates allows them to be shown.
            if ($death_year < $birth_year) {
                $death_year = $birth_year;
            }

            // Don't show death dates in the future.
            $death_year = min($death_year, $current_year);

            // Add this individual to the next row in the chart...
            $next_row = count($rows);
            // ...unless we can find an existing row where it fits.
            foreach ($rows as $row => $year) {
                if ($year < $birth_year) {
                    $next_row = $row;
                    break;
                }
            }

            // Fill the row up to the year (leaving a small gap)
            $rows[$next_row] = $death_year;

            $color_generator = $color_generators[$individual->sex()] ?? $color_generators['U'];

            $lifespans[] = (object) [
                'background' => $color_generator->getNextColor(),
                'birth_year' => $birth_year,
                'death_year' => $death_year,
                'id'         => 'individual-' . md5($individual->xref()),
                'individual' => $individual,
                'row'        => $next_row,
            ];
        }

        return $lifespans;
    }
}
