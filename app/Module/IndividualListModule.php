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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function array_filter;
use function array_keys;
use function array_sum;
use function assert;
use function e;
use function implode;
use function ob_get_clean;
use function ob_start;
use function route;
use function uksort;
use function view;

use const ARRAY_FILTER_USE_KEY;

/**
 * Class IndividualListModule
 */
class IndividualListModule extends AbstractModule implements ModuleListInterface, RequestHandlerInterface
{
    use ModuleListTrait;

    protected const ROUTE_URL = '/tree/{tree}/individual-list';

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
            ->get(static::class, static::ROUTE_URL, $this);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Individuals');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Individuals” module */
        return I18N::translate('A list of individuals.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return 'menu-list-indi';
    }

    /**
     * @param Tree                                      $tree
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function listUrl(Tree $tree, array $parameters = []): string
    {
        $request = app(ServerRequestInterface::class);
        assert($request instanceof ServerRequestInterface);

        $xref = Validator::attributes($request)->isXref()->string('xref', '');

        if ($xref !== '') {
            $individual = Registry::individualFactory()->make($xref, $tree);

            if ($individual instanceof Individual && $individual->canShow()) {
                $primary_name = $individual->getPrimaryName();

                $parameters['surname'] ??= $individual->getAllNames()[$primary_name]['surn'] ?? null;
            }
        }

        $parameters['tree'] = $tree->name();

        return route(static::class, $parameters);
    }

    /**
     * @return array<string>
     */
    public function listUrlAttributes(): array
    {
        return [];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        Auth::checkComponentAccess($this, ModuleListInterface::class, $tree, $user);

        $surname_param = Validator::queryParams($request)->string('surname', '');
        $surname       = I18N::strtoupper(I18N::language()->normalize($surname_param));

        $params = [
            'alpha'               => Validator::queryParams($request)->string('alpha', ''),
            'falpha'              => Validator::queryParams($request)->string('falpha', ''),
            'show'                => Validator::queryParams($request)->string('show', 'surn'),
            'show_all'            => Validator::queryParams($request)->string('show_all', 'no'),
            'show_all_firstnames' => Validator::queryParams($request)->string('show_all_firstnames', 'no'),
            'show_marnm'          => Validator::queryParams($request)->string('show_marnm', ''),
            'surname'             => $surname,
        ];

        if ($surname_param !== $surname) {
            return Registry::responseFactory()->redirectUrl($this->listUrl($tree, $params));
        }

        return $this->createResponse($tree, $user, $params, false);
    }

    /**
     * @param Tree          $tree
     * @param UserInterface $user
     * @param array<string> $params
     * @param bool          $families
     *
     * @return ResponseInterface
     */
    protected function createResponse(Tree $tree, UserInterface $user, array $params, bool $families): ResponseInterface
    {
        // We show three different lists: initials, surnames and individuals

        // All surnames beginning with this letter, where "@" is unknown and "," is none
        $alpha = $params['alpha'];

        // All individuals with this surname
        $surname = $params['surname'];

        // All individuals
        $show_all = $params['show_all'] === 'yes';

        // Include/exclude married names
        $show_marnm = $params['show_marnm'];

        // What type of list to display, if any
        $show = $params['show'];

        // Break long lists down by given name
        $show_all_firstnames = $params['show_all_firstnames'] === 'yes';

        // All first names beginning with this letter where "@" is unknown
        $falpha = $params['falpha'];

        // Make sure parameters are consistent with each other.
        if ($show_all_firstnames) {
            $falpha = '';
        }

        if ($show_all) {
            $alpha   = '';
            $surname = '';
        }

        if ($surname !== '') {
            $alpha = I18N::language()->initialLetter($surname);
        }

        $all_surnames     = $this->allSurnames($tree, $show_marnm === 'yes', $families);
        $surname_initials = $this->surnameInitials($all_surnames);

        switch ($show_marnm) {
            case 'no':
            case 'yes':
                $user->setPreference($families ? 'family-list-marnm' : 'individual-list-marnm', $show_marnm);
                break;
            default:
                $show_marnm = $user->getPreference($families ? 'family-list-marnm' : 'individual-list-marnm');
        }

        // Make sure selections are consistent.
        // i.e. can’t specify show_all and surname at the same time.
        if ($show_all) {
            if ($show_all_firstnames) {
                $legend  = I18N::translate('All');
                $params = ['tree' => $tree->name(), 'show_all' => 'yes'];
                $show    = 'indi';
            } elseif ($falpha !== '') {
                $legend  = I18N::translate('All') . ', ' . e($falpha) . '…';
                $params = ['tree' => $tree->name(), 'show_all' => 'yes'];
                $show    = 'indi';
            } else {
                $legend  = I18N::translate('All');
                $params = ['tree' => $tree->name(), 'show_all' => 'yes'];
            }
        } elseif ($surname !== '') {
            $show_all = false;
            if ($surname === Individual::NOMEN_NESCIO) {
                $legend = I18N::translateContext('Unknown surname', '…');
                $show   = 'indi'; // The surname list makes no sense with only one surname.
            } else {
                // The surname parameter is a root/canonical form. Display the actual surnames found.
                $variants = array_keys($all_surnames[$surname] ?? [$surname => $surname]);
                usort($variants, I18N::comparator());
                $variants = array_map(static fn (string $x): string => $x === '' ? I18N::translate('No surname') : $x, $variants);
                $legend   = implode('/', $variants);
                $show     = 'indi'; // The surname list makes no sense with only one surname.
            }
            $params = ['tree' => $tree->name(), 'surname' => $surname, 'falpha' => $falpha];
            switch ($falpha) {
                case '':
                    break;
                case '@':
                    $legend .= ', ' . I18N::translateContext('Unknown given name', '…');
                    break;
                default:
                    $legend .= ', ' . e($falpha) . '…';
                    break;
            }
        } elseif ($alpha === '@') {
            $show_all = false;
            $legend   = I18N::translateContext('Unknown surname', '…');
            $params   = ['alpha' => $alpha, 'tree' => $tree->name()];
            $surname  = Individual::NOMEN_NESCIO;
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha === ',') {
            $show_all = false;
            $legend   = I18N::translate('No surname');
            $params = ['alpha' => $alpha, 'tree' => $tree->name()];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha !== '') {
            $show_all = false;
            $legend   = e($alpha) . '…';
            $params = ['alpha' => $alpha, 'tree' => $tree->name()];
        } else {
            $show_all = false;
            $legend   = '…';
            $params   = ['tree' => $tree->name()];
            $show     = 'none'; // Don't show lists until something is chosen
        }
        $legend = '<bdi>' . $legend . '</bdi>';

        if ($families) {
            $title = I18N::translate('Families') . ' — ' . $legend;
        } else {
            $title = I18N::translate('Individuals') . ' — ' . $legend;
        }

        ob_start(); ?>
        <div class="d-flex flex-column wt-page-options wt-page-options-individual-list d-print-none">
            <ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-surname">

                <?php foreach ($surname_initials as $letter => $count) : ?>
                    <li class="wt-initials-list-item d-flex">
                        <?php if ($count > 0) : ?>
                            <a href="<?= e($this->listUrl($tree, ['alpha' => $letter, 'tree' => $tree->name()])) ?>" class="wt-initial px-1<?= $letter === $alpha ? ' active' : '' ?> '" title="<?= I18N::number($count) ?>"><?= $this->displaySurnameInitial((string) $letter) ?></a>
                        <?php else : ?>
                            <span class="wt-initial px-1 text-muted"><?= $this->displaySurnameInitial((string) $letter) ?></span>

                        <?php endif ?>
                    </li>
                <?php endforeach ?>

                <?php if (Session::has('initiated')) : ?>
                    <!-- Search spiders don't get the "show all" option as the other links give them everything. -->
                    <li class="wt-initials-list-item d-flex">
                        <a class="wt-initial px-1<?= $show_all ? ' active' : '' ?>" href="<?= e($this->listUrl($tree, ['show_all' => 'yes'] + $params)) ?>"><?= I18N::translate('All') ?></a>
                    </li>
                <?php endif ?>
            </ul>

            <!-- Search spiders don't get an option to show/hide the surname sublists, nor does it make sense on the all/unknown/surname views -->
            <?php if ($show !== 'none' && Session::has('initiated')) : ?>
                <?php if ($show_marnm === 'yes') : ?>
                    <p>
                        <a href="<?= e($this->listUrl($tree, ['show' => $show, 'show_marnm' => 'no'] + $params)) ?>">
                            <?= I18N::translate('Exclude individuals with “%s” as a married name', $legend) ?>
                        </a>
                    </p>
                <?php else : ?>
                    <p>
                        <a href="<?= e($this->listUrl($tree, ['show' => $show, 'show_marnm' => 'yes'] + $params)) ?>">
                            <?= I18N::translate('Include individuals with “%s” as a married name', $legend) ?>
                        </a>
                    </p>
                <?php endif ?>

                <?php if ($alpha !== '@' && $alpha !== ',' && $surname === '') : ?>
                    <?php if ($show === 'surn') : ?>
                        <p>
                            <a href="<?= e($this->listUrl($tree, ['show' => 'indi', 'show_marnm' => 'no'] + $params)) ?>">
                                <?= I18N::translate('Show the list of individuals') ?>
                            </a>
                        </p>
                    <?php else : ?>
                        <p>
                            <a href="<?= e($this->listUrl($tree, ['show' => 'surn', 'show_marnm' => 'no'] + $params)) ?>">
                                <?= I18N::translate('Show the list of surnames') ?>
                            </a>
                        </p>
                    <?php endif ?>
                <?php endif ?>
            <?php endif ?>
        </div>

        <div class="wt-page-content">
            <?php
            if ($show === 'indi' || $show === 'surn') {
                switch ($alpha) {
                    case '@':
                        $surns = array_filter($all_surnames, static fn (string $x): bool => $x === Individual::NOMEN_NESCIO, ARRAY_FILTER_USE_KEY);
                        break;
                    case ',':
                        $surns = array_filter($all_surnames, static fn (string $x): bool => $x === '', ARRAY_FILTER_USE_KEY);
                        break;
                    case '':
                        if ($show_all) {
                            $surns = array_filter($all_surnames, static fn (string $x): bool => $x !== '' && $x !== Individual::NOMEN_NESCIO, ARRAY_FILTER_USE_KEY);
                        } else {
                            $surns = array_filter($all_surnames, static fn (string $x): bool => $x === $surname, ARRAY_FILTER_USE_KEY);
                        }
                        break;
                    default:
                        if ($surname === '') {
                            $surns = array_filter($all_surnames, static fn (string $x): bool => I18N::language()->initialLetter($x) === $alpha, ARRAY_FILTER_USE_KEY);
                        } else {
                            $surns = array_filter($all_surnames, static fn (string $x): bool => $x === $surname, ARRAY_FILTER_USE_KEY);
                        }
                        break;
                }

                if ($show === 'surn') {
                    // Show the surname list
                    switch ($tree->getPreference('SURNAME_LIST_STYLE')) {
                        case 'style1':
                            echo view('lists/surnames-column-list', [
                                'module'   => $this,
                                'surnames' => $surns,
                                'totals'   => true,
                                'tree'     => $tree,
                            ]);
                            break;
                        case 'style3':
                            echo view('lists/surnames-tag-cloud', [
                                'module'   => $this,
                                'surnames' => $surns,
                                'totals'   => true,
                                'tree'     => $tree,
                            ]);
                            break;
                        case 'style2':
                        default:
                            echo view('lists/surnames-table', [
                                'families' => $families,
                                'module'   => $this,
                                'order'    => [[0, 'asc']],
                                'surnames' => $surns,
                                'tree'     => $tree,
                            ]);
                            break;
                    }
                } else {
                    // Show the list
                    $count = array_sum(array_map(static fn (array $x): int => array_sum($x), $surns));

                    // Don't sublist short lists.
                    if ($count < $tree->getPreference('SUBLIST_TRIGGER_I')) {
                        $falpha = '';
                    } else {
                        $givn_initials = $this->givenNameInitials($tree, array_keys($surns), $show_marnm === 'yes', $families);
                        // Break long lists by initial letter of given name
                        if ($surname !== '' || $show_all) {
                            if (!$show_all) {
                                echo '<h2 class="wt-page-title">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
                            }
                            // Don't show the list until we have some filter criteria
                            $show = $falpha !== '' || $show_all_firstnames ? 'indi' : 'none';
                            echo '<ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-given-names">';
                            foreach ($givn_initials as $givn_initial => $given_count) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($given_count > 0) {
                                    if ($show === 'indi' && $givn_initial === $falpha && !$show_all_firstnames) {
                                        echo '<a class="wt-initial px-1 active" href="' . e($this->listUrl($tree, ['falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($given_count) . '">' . $this->displayGivenNameInitial((string) $givn_initial) . '</a>';
                                    } else {
                                        echo '<a class="wt-initial px-1" href="' . e($this->listUrl($tree, ['falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($given_count) . '">' . $this->displayGivenNameInitial((string) $givn_initial) . '</a>';
                                    }
                                } else {
                                    echo '<span class="wt-initial px-1 text-muted">' . $this->displayGivenNameInitial((string) $givn_initial) . '</span>';
                                }
                                echo '</li>';
                            }
                            // Search spiders don't get the "show all" option as the other links give them everything.
                            if (Session::has('initiated')) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($show_all_firstnames) {
                                    echo '<span class="wt-initial px-1 active">' . I18N::translate('All') . '</span>';
                                } else {
                                    echo '<a class="wt-initial px-1" href="' . e($this->listUrl($tree, ['show_all_firstnames' => 'yes'] + $params)) . '" title="' . I18N::number($count) . '">' . I18N::translate('All') . '</a>';
                                }
                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                    }
                    if ($show === 'indi') {
                        if ($families) {
                            echo view('lists/families-table', [
                                'families' => $this->families($tree, $surname, array_keys($all_surnames[$surname] ?? []), $falpha, $show_marnm === 'yes'),
                                'tree'     => $tree,
                            ]);
                        } else {
                            echo view('lists/individuals-table', [
                                'individuals' => $this->individuals($tree, $surname, array_keys($all_surnames[$surname] ?? []), $falpha, $show_marnm === 'yes', false),
                                'sosa'        => false,
                                'tree'        => $tree,
                            ]);
                        }
                    }
                }
            } ?>
        </div>
        <?php

        $html = ob_get_clean();

        return $this->viewResponse('modules/individual-list/page', [
            'content' => $html,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }

    /**
     * Some initial letters have a special meaning
     *
     * @param string $initial
     *
     * @return string
     */
    protected function displayGivenNameInitial(string $initial): string
    {
        if ($initial === '@') {
            return I18N::translateContext('Unknown given name', '…');
        }

        return e($initial);
    }

    /**
     * Some initial letters have a special meaning
     *
     * @param string $initial
     *
     * @return string
     */
    protected function displaySurnameInitial(string $initial): string
    {
        if ($initial === '@') {
            return I18N::translateContext('Unknown surname', '…');
        }

        if ($initial === ',') {
            return I18N::translate('No surname');
        }

        return e($initial);
    }

    /**
     * Restrict a query to individuals that are a spouse in a family record.
     *
     * @param bool    $fams
     * @param Builder $query
     */
    protected function whereFamily(bool $fams, Builder $query): void
    {
        if ($fams) {
            $query->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_from', '=', 'n_id')
                    ->on('l_file', '=', 'n_file')
                    ->where('l_type', '=', 'FAMS');
            });
        }
    }

    /**
     * Restrict a query to include/exclude married names.
     *
     * @param bool    $marnm
     * @param Builder $query
     */
    protected function whereMarriedName(bool $marnm, Builder $query): void
    {
        if (!$marnm) {
            $query->where('n_type', '<>', '_MARNM');
        }
    }

    /**
     * Get a count of individuals with each initial letter
     *
     * @param Tree          $tree
     * @param array<string> $surns if set, only consider people with this surname
     * @param bool          $marnm if set, include married names
     * @param bool          $fams  if set, only consider individuals with FAMS records
     *
     * @return array<int>
     */
    public function givenNameInitials(Tree $tree, array $surns, bool $marnm, bool $fams): array
    {
        $initials = [];

        // Ensure our own language comes before others.
        foreach (I18N::language()->alphabet() as $initial) {
            $initials[$initial] = 0;
        }

        $query = DB::table('name')
            ->where('n_file', '=', $tree->id());

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surns !== []) {
            $query->whereIn('n_surn', $surns);
        }

        $query
            ->select($this->binaryColumn('n_givn', 'n_givn'), new Expression('COUNT(*) AS count'))
            ->groupBy([$this->binaryColumn('n_givn')]);

        foreach ($query->get() as $row) {
            $initial = I18N::strtoupper(I18N::language()->initialLetter($row->n_givn));
            $initials[$initial] ??= 0;
            $initials[$initial] += (int) $row->count;
        }

        $count_unknown = $initials['@'] ?? 0;

        if ($count_unknown > 0) {
            unset($initials['@']);
            $initials['@'] = $count_unknown;
        }

        return $initials;
    }

    /**
     * Get a count of all surnames and variants.
     *
     * @param Tree $tree
     * @param bool $marnm if set, include married names
     * @param bool $fams  if set, only consider individuals with FAMS records
     *
     * @return array<array<int>>
     */
    protected function allSurnames(Tree $tree, bool $marnm, bool $fams): array
    {
        $query = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->whereNotNull('n_surn') // Filters old records for sources, repositories, etc.
            ->whereNotNull('n_surname')
            ->select([
                $this->binaryColumn('n_surn', 'n_surn'),
                $this->binaryColumn('n_surname', 'n_surname'),
                new Expression('COUNT(*) AS total'),
            ]);

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        $query->groupBy([
            $this->binaryColumn('n_surn'),
            $this->binaryColumn('n_surname'),
        ]);

        /** @var array<array<int>> $list */
        $list = [];

        foreach ($query->get() as $row) {
            $row->n_surn = $row->n_surn === '' ? $row->n_surname : $row->n_surn;
            $row->n_surn = I18N::strtoupper(I18N::language()->normalize($row->n_surn));

            $list[$row->n_surn][$row->n_surname] ??= 0;
            $list[$row->n_surn][$row->n_surname] += (int) $row->total;
        }

        uksort($list, I18N::comparator());

        return $list;
    }

    /**
     * Extract initial letters and counts for all surnames.
     *
     * @param array<array<int>> $all_surnames
     *
     * @return array<int>
     */
    protected function surnameInitials(array $all_surnames): array
    {
        $initials    = [];

        // Ensure our own language comes before others.
        foreach (I18N::language()->alphabet() as $initial) {
            $initials[$initial]    = 0;
        }

        foreach ($all_surnames as $surn => $surnames) {
            $initial = I18N::language()->initialLetter((string) $surn);

            $initials[$initial] ??= 0;
            $initials[$initial] += array_sum($surnames);
        }

        // Move specials to the end
        $count_none = $initials[''] ?? 0;

        if ($count_none > 0) {
            unset($initials['']);
            $initials[','] = $count_none;
        }

        $count_unknown = $initials['@'] ?? 0;

        if ($count_unknown > 0) {
            unset($initials['@']);
            $initials['@'] = $count_unknown;
        }

        return $initials;
    }

    /**
     * Fetch a list of individuals with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param Tree            $tree
     * @param string          $surname  if set, only fetch people with this n_surn
     * @param array<string>   $surnames if set, only fetch people with this n_surname
     * @param string          $galpha   if set, only fetch given names starting with this letter
     * @param bool            $marnm    if set, include married names
     * @param bool            $fams     if set, only fetch individuals with FAMS records
     *
     * @return Collection<int,Individual>
     */
    protected function individuals(Tree $tree, string $surname, array $surnames, string $galpha, bool $marnm, bool $fams): Collection
    {
        $query = DB::table('individuals')
            ->join('name', static function (JoinClause $join): void {
                $join
                    ->on('n_id', '=', 'i_id')
                    ->on('n_file', '=', 'i_file');
            })
            ->where('i_file', '=', $tree->id())
            ->select(['i_id AS xref', 'i_gedcom AS gedcom', 'n_givn', 'n_surn']);

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surnames === []) {
            // SURN, with no surname
            $query->where('n_surn', '=', $surname);
        } else {
            $query->whereIn($this->binaryColumn('n_surname'), $surnames);
        }

        $query
            ->orderBy(new Expression("CASE n_surn WHEN '" . Individual::NOMEN_NESCIO . "' THEN 1 ELSE 0 END"))
            ->orderBy('n_surn')
            ->orderBy(new Expression("CASE n_givn WHEN '" . Individual::NOMEN_NESCIO . "' THEN 1 ELSE 0 END"))
            ->orderBy('n_givn');

        $individuals = new Collection();

        foreach ($query->get() as $row) {
            $individual = Registry::individualFactory()->make($row->xref, $tree, $row->gedcom);
            assert($individual instanceof Individual);

            // The name from the database may be private - check the filtered list...
            foreach ($individual->getAllNames() as $n => $name) {
                if ($name['givn'] === $row->n_givn && $name['surn'] === $row->n_surn) {
                    if ($galpha === '' || I18N::strtoupper(I18N::language()->initialLetter($row->n_givn)) === $galpha) {
                        $individual->setPrimaryName($n);
                        // We need to clone $individual, as we may have multiple references to the
                        // same individual in this list, and the "primary name" would otherwise
                        // be shared amongst all of them.
                        $individuals->push(clone $individual);
                        break;
                    }
                }
            }
        }

        return $individuals;
    }

    /**
     * Fetch a list of families with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param Tree          $tree
     * @param string        $surname  if set, only fetch people with this n_surn
     * @param array<string> $surnames if set, only fetch people with this n_surname
     * @param string        $galpha   if set, only fetch given names starting with this letter
     * @param bool          $marnm    if set, include married names
     *
     * @return Collection<int,Family>
     */
    protected function families(Tree $tree, string $surname, array $surnames, string $galpha, bool $marnm): Collection
    {
        $families = new Collection();

        foreach ($this->individuals($tree, $surname, $surnames, $galpha, $marnm, true) as $indi) {
            foreach ($indi->spouseFamilies() as $family) {
                $families->push($family);
            }
        }

        return $families->unique();
    }

    /**
     * This module assumes the database will use binary collation on the name columns.
     * Until we convert MySQL databases to use utf8_bin, we need to do this at run-time.
     *
     * @param string      $column
     * @param string|null $alias
     *
     * @return Expression
     */
    private function binaryColumn(string $column, string $alias = null): Expression
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $sql = 'CAST(' . $column . ' AS binary)';
        } else {
            $sql = $column;
        }

        if ($alias !== null) {
            $sql .= ' AS ' . $alias;
        }

        return new Expression($sql);
    }
}
