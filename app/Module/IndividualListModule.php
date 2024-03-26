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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
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
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_sum;
use function array_values;
use function assert;
use function e;
use function implode;
use function ob_get_clean;
use function ob_start;
use function route;
use function uksort;
use function usort;
use function view;

use const ARRAY_FILTER_USE_KEY;

/**
 * Class IndividualListModule
 */
class IndividualListModule extends AbstractModule implements ModuleListInterface, RequestHandlerInterface
{
    use ModuleListTrait;

    protected const ROUTE_URL = '/tree/{tree}/individual-list';

    // The individual list and family list use the same code/logic.
    // They just display different lists.
    protected bool $families = false;

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

        // All individuals with this surname
        $surname_param = Validator::queryParams($request)->string('surname', '');
        $surname       = I18N::strtoupper(I18N::language()->normalize($surname_param));

        // All surnames beginning with this letter, where "@" is unknown and "," is none
        $alpha = Validator::queryParams($request)->string('alpha', '');

        // All first names beginning with this letter where "@" is unknown
        $falpha = Validator::queryParams($request)->string('falpha', '');

        // What type of list to display, if any
        $show = Validator::queryParams($request)->string('show', 'surn');

        // All individuals
        $show_all = Validator::queryParams($request)->string('show_all', '');

        // Include/exclude married names
        $show_marnm = Validator::queryParams($request)->string('show_marnm', '');

        // Break long lists down by given name
        $show_all_firstnames = Validator::queryParams($request)->string('show_all_firstnames', '');

        $params = [
            'alpha'               => $alpha,
            'falpha'              => $falpha,
            'show'                => $show,
            'show_all'            => $show_all,
            'show_all_firstnames' => $show_all_firstnames,
            'show_marnm'          => $show_marnm,
            'surname'             => $surname,
        ];

        if ($surname_param !== $surname) {
            return Registry::responseFactory()
                ->redirectUrl($this->listUrl($tree, $params), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        // Make sure parameters are consistent with each other.
        if ($show_all_firstnames === 'yes') {
            $falpha = '';
        }

        if ($show_all === 'yes') {
            $alpha   = '';
            $surname = '';
        }

        if ($surname !== '') {
            $alpha = I18N::language()->initialLetter($surname);
        }

        $surname_data     = $this->surnameData($tree, $show_marnm === 'yes', $this->families);
        $all_surns        = $this->allSurns($surname_data);
        $all_surnames     = $this->allSurnames($surname_data);
        $surname_initials = $this->surnameInitials($surname_data);

        // We've requested a surname that doesn't currently exist.
        if ($surname !== ''  && !array_key_exists($surname, $all_surns)) {
            $message = I18N::translate('There are no individuals with the surname “%s”', e($surname));
            FlashMessages::addMessage($message);

            return Registry::responseFactory()
                ->redirectUrl($this->listUrl($tree));
        }

        // Make sure selections are consistent.
        // i.e. can’t specify show_all and surname at the same time.
        if ($show_all === 'yes') {
            if ($show_all_firstnames === 'yes') {
                $legend = I18N::translate('All');
                $params = ['tree' => $tree->name(), 'show_all' => 'yes', 'show_marnm' => $show_marnm];
                $show   = 'indi';
            } elseif ($falpha !== '') {
                $legend = I18N::translate('All') . ', ' . e($falpha) . '…';
                $params = ['tree' => $tree->name(), 'show_all' => 'yes', 'show_marnm' => $show_marnm];
                $show   = 'indi';
            } else {
                $legend = I18N::translate('All');
                $params = ['tree' => $tree->name(), 'show_all' => 'yes', 'show_marnm' => $show_marnm];
            }
        } elseif ($surname !== '') {
            $show_all = 'no';
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
            $params = ['tree' => $tree->name(), 'surname' => $surname, 'falpha' => $falpha, 'show_marnm' => $show_marnm];
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
            $show_all = 'no';
            $legend   = I18N::translateContext('Unknown surname', '…');
            $params   = ['alpha' => $alpha, 'tree' => $tree->name(), 'show_marnm' => $show_marnm];
            $surname  = Individual::NOMEN_NESCIO;
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha === ',') {
            $show_all = 'no';
            $legend   = I18N::translate('No surname');
            $params   = ['alpha' => $alpha, 'tree' => $tree->name(), 'show_marnm' => $show_marnm];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha !== '') {
            $show_all = 'no';
            $legend   = e($alpha) . '…';
            $params   = ['alpha' => $alpha, 'tree' => $tree->name(), 'show_marnm' => $show_marnm];
        } else {
            $show_all = 'no';
            $legend   = '…';
            $params   = ['tree' => $tree->name(), 'show_marnm' => $show_marnm];
            $show     = 'none'; // Don't show lists until something is chosen
        }
        $legend = '<bdi>' . $legend . '</bdi>';

        if ($this->families) {
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
                            <a href="<?= e($this->listUrl($tree, ['alpha' => $letter, 'show_marnm' => $show_marnm, 'tree' => $tree->name()])) ?>" class="wt-initial px-1<?= $letter === $alpha ? ' active' : '' ?> '" title="<?= I18N::number($count) ?>"><?= $this->displaySurnameInitial((string) $letter) ?></a>
                        <?php else : ?>
                            <span class="wt-initial px-1 text-muted"><?= $this->displaySurnameInitial((string) $letter) ?></span>

                        <?php endif ?>
                    </li>
                <?php endforeach ?>

                <?php if (Session::has('initiated')) : ?>
                    <!-- Search spiders don't get the "show all" option as the other links give them everything. -->
                    <li class="wt-initials-list-item d-flex">
                        <a class="wt-initial px-1<?= $show_all === 'yes' ? ' active' : '' ?>" href="<?= e($this->listUrl($tree, ['show_all' => 'yes'] + $params)) ?>"><?= I18N::translate('All') ?></a>
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
                            <a href="<?= e($this->listUrl($tree, ['show' => 'indi'] + $params)) ?>">
                                <?= I18N::translate('Show the list of individuals') ?>
                            </a>
                        </p>
                    <?php else : ?>
                        <p>
                            <a href="<?= e($this->listUrl($tree, ['show' => 'surn'] + $params)) ?>">
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
                        $filter = static fn (string $x): bool => $x === Individual::NOMEN_NESCIO;
                        break;
                    case ',':
                        $filter = static fn (string $x): bool => $x === '';
                        break;
                    case '':
                        if ($show_all === 'yes') {
                            $filter = static fn (string $x): bool => $x !== '' && $x !== Individual::NOMEN_NESCIO;
                        } else {
                            $filter = static fn (string $x): bool => $x === $surname;
                        }
                        break;
                    default:
                        if ($surname === '') {
                            $filter = static fn (string $x): bool => I18N::language()->initialLetter($x) === $alpha;
                        } else {
                            $filter = static fn (string $x): bool => $x === $surname;
                        }
                        break;
                }

                $all_surnames = array_filter($all_surnames, $filter, ARRAY_FILTER_USE_KEY);

                if ($show === 'surn') {
                    // Show the surname list
                    switch ($tree->getPreference('SURNAME_LIST_STYLE')) {
                        case 'style1':
                            echo view('lists/surnames-column-list', [
                                'module'   => $this,
                                'params'   => ['show' => 'indi', 'show_all' => null] + $params,
                                'surnames' => $all_surnames,
                                'totals'   => true,
                                'tree'     => $tree,
                            ]);
                            break;
                        case 'style3':
                            echo view('lists/surnames-tag-cloud', [
                                'module'   => $this,
                                'params'   => ['show' => 'indi', 'show_all' => null] + $params,
                                'surnames' => $all_surnames,
                                'totals'   => true,
                                'tree'     => $tree,
                            ]);
                            break;
                        case 'style2':
                        default:
                            echo view('lists/surnames-table', [
                                'families' => $this->families,
                                'module'   => $this,
                                'order'    => [[0, 'asc']],
                                'params'   => ['show' => 'indi', 'show_all' => null] + $params,
                                'surnames' => $all_surnames,
                                'tree'     => $tree,
                            ]);
                            break;
                    }
                } else {
                    // Show the list
                    $count = array_sum(array_map(static fn (array $x): int => array_sum($x), $all_surnames));

                    // Don't sublist short lists.
                    $sublist_threshold = (int) $tree->getPreference('SUBLIST_TRIGGER_I');
                    if ($sublist_threshold === 0 || $count < $sublist_threshold) {
                        $falpha = '';
                    } else {
                        // Break long lists by initial letter of given name
                        $all_surnames  = array_values(array_map(static fn ($x): array => array_keys($x), $all_surnames));
                        $all_surnames  = array_merge(...$all_surnames);
                        $givn_initials = $this->givenNameInitials($tree, $all_surnames, $show_marnm === 'yes', $this->families);

                        if ($surname !== '' || $show_all === 'yes') {
                            if ($show_all !== 'yes') {
                                echo '<h2 class="wt-page-title">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
                            }
                            // Don't show the list until we have some filter criteria
                            $show = $falpha !== '' || $show_all_firstnames === 'yes' ? 'indi' : 'none';
                            echo '<ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-given-names">';
                            foreach ($givn_initials as $givn_initial => $given_count) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($given_count > 0) {
                                    if ($show === 'indi' && $givn_initial === $falpha && $show_all_firstnames !== 'yes') {
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
                                if ($show_all_firstnames === 'yes') {
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
                        if ($alpha === '@') {
                            $surns_to_show = ['@N.N.'];
                        } elseif ($alpha === ',') {
                            $surns_to_show = [''];
                        } elseif ($surname !== '') {
                            $surns_to_show = $all_surns[$surname];
                        } elseif ($alpha !== '') {
                            $tmp = array_filter(
                                $all_surns,
                                static fn (string $x): bool => I18N::language()->initialLetter($x) === $alpha,
                                ARRAY_FILTER_USE_KEY
                            );

                            $surns_to_show = array_merge(...array_values($tmp));
                        } else {
                            $surns_to_show = [];
                        }

                        if ($this->families) {
                            echo view('lists/families-table', [
                                'families' => $this->families($tree, $surns_to_show, $falpha, $show_marnm === 'yes'),
                                'tree'     => $tree,
                            ]);
                        } else {
                            echo view('lists/individuals-table', [
                                'individuals' => $this->individuals($tree, $surns_to_show, $falpha, $show_marnm === 'yes', false),
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
            ->select([$this->binaryColumn('n_givn', 'n_givn'), new Expression('COUNT(*) AS count')])
            ->groupBy([$this->binaryColumn('n_givn')]);

        foreach ($query->get() as $row) {
            $initial            = I18N::strtoupper(I18N::language()->initialLetter($row->n_givn));
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
     * @param Tree $tree
     * @param bool $marnm
     * @param bool $fams
     *
     * @return array<object{n_surn:string,n_surname:string,total:int}>
     */
    private function surnameData(Tree $tree, bool $marnm, bool $fams): array
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

        return $query
            ->get()
            ->map(static fn (object $x): object => (object) ['n_surn' => $x->n_surn, 'n_surname' => $x->n_surname, 'total' => (int) $x->total])
            ->all();
    }

    /**
     * Group n_surn values, based on collation rules for the current language.
     * We need them to find the individuals with this n_surn.
     *
     * @param array<object{n_surn:string,n_surname:string,total:int}> $surname_data
     *
     * @return array<array<int,string>>
     */
    protected function allSurns(array $surname_data): array
    {
        $list = [];

        foreach ($surname_data as $row) {
            $normalized = I18N::strtoupper(I18N::language()->normalize($row->n_surn));
            $list[$normalized][] = $row->n_surn;
        }

        uksort($list, I18N::comparator());

        return $list;
    }

    /**
     * Group n_surname values, based on collation rules for each n_surn.
     * We need them to show counts of individuals with each surname.
     *
     * @param array<object{n_surn:string,n_surname:string,total:int}> $surname_data
     *
     * @return array<array<int>>
     */
    protected function allSurnames(array $surname_data): array
    {
        $list = [];

        foreach ($surname_data as $row) {
            $n_surn = $row->n_surn === '' ? $row->n_surname : $row->n_surn;
            $n_surn = I18N::strtoupper(I18N::language()->normalize($n_surn));

            $list[$n_surn][$row->n_surname] ??= 0;
            $list[$n_surn][$row->n_surname] += $row->total;
        }

        uksort($list, I18N::comparator());

        return $list;
    }

    /**
     * Extract initial letters and counts for all surnames.
     *
     * @param array<object{n_surn:string,n_surname:string,total:int}> $surname_data
     *
     * @return array<int>
     */
    protected function surnameInitials(array $surname_data): array
    {
        $initials = [];

        // Ensure our own language comes before others.
        foreach (I18N::language()->alphabet() as $initial) {
            $initials[$initial] = 0;
        }

        foreach ($surname_data as $row) {
            $initial = I18N::language()->initialLetter(I18N::strtoupper($row->n_surn));
            $initial = I18N::language()->normalize($initial);

            $initials[$initial] ??= 0;
            $initials[$initial] += $row->total;
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
     * @param Tree          $tree
     * @param array<string> $surns_to_show if set, only fetch people with this n_surn
     * @param string        $galpha        if set, only fetch given names starting with this letter
     * @param bool          $marnm         if set, include married names
     * @param bool          $fams          if set, only fetch individuals with FAMS records
     *
     * @return Collection<int,Individual>
     */
    protected function individuals(Tree $tree, array $surns_to_show, string $galpha, bool $marnm, bool $fams): Collection
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

        if ($surns_to_show === []) {
            $query->whereNotIn('n_surn', ['', '@N.N.']);
        } else {
            $query->whereIn($this->binaryColumn('n_surn'), $surns_to_show);
        }

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
     * @param array<string> $surnames if set, only fetch people with this n_surname
     * @param string        $galpha   if set, only fetch given names starting with this letter
     * @param bool          $marnm    if set, include married names
     *
     * @return Collection<int,Family>
     */
    protected function families(Tree $tree, array $surnames, string $galpha, bool $marnm): Collection
    {
        $families = new Collection();

        foreach ($this->individuals($tree, $surnames, $galpha, $marnm, true) as $indi) {
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
    private function binaryColumn(string $column, ?string $alias = null): Expression
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
