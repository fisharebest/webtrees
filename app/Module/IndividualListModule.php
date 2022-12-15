<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Localization\Locale\LocaleInterface;
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
use function array_keys;
use function assert;
use function e;
use function implode;
use function in_array;
use function ob_get_clean;
use function ob_start;
use function route;
use function view;

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

                $parameters['surname'] = $parameters['surname'] ?? $individual->getAllNames()[$primary_name]['surn'] ?? null;
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

        $params = [
            'alpha'               => Validator::queryParams($request)->string('alpha', ''),
            'falpha'              => Validator::queryParams($request)->string('falpha', ''),
            'show'                => Validator::queryParams($request)->string('show', 'surn'),
            'show_all'            => Validator::queryParams($request)->string('show_all', 'no'),
            'show_all_firstnames' => Validator::queryParams($request)->string('show_all_firstnames', 'no'),
            'show_marnm'          => Validator::queryParams($request)->string('show_marnm', ''),
            'surname'             => Validator::queryParams($request)->string('surname', ''),
        ];

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
        ob_start();

        // We show three different lists: initials, surnames and individuals

        // All surnames beginning with this letter where "@"=unknown and ","=none
        $alpha = $params['alpha'];

        // All individuals with this surname
        $surname = $params['surname'];

        // All individuals
        $show_all = $params['show_all'];

        // Long lists can be broken down by given name
        $show_all_firstnames = $params['show_all_firstnames'];
        if ($show_all_firstnames === 'yes') {
            $falpha = '';
        } else {
            // All first names beginning with this letter
            $falpha = $params['falpha'];
        }

        $show_marnm = $params['show_marnm'];
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
        if ($show_all === 'yes') {
            $alpha   = '';
            $surname = '';

            if ($show_all_firstnames === 'yes') {
                $legend  = I18N::translate('All');
                $params  = [
                    'tree'     => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = 'indi';
            } elseif ($falpha !== '') {
                $legend  = I18N::translate('All') . ', ' . e($falpha) . '…';
                $params  = [
                    'tree'     => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = 'indi';
            } else {
                $legend  = I18N::translate('All');
                $show    = $params['show'];
                $params  = [
                    'tree'     => $tree->name(),
                    'show_all' => 'yes',
                ];
            }
        } elseif ($surname !== '') {
            $alpha    = I18N::language()->initialLetter($surname); // so we can highlight the initial letter
            $show_all = 'no';
            if ($surname === Individual::NOMEN_NESCIO) {
                $legend = I18N::translateContext('Unknown surname', '…');
            } else {
                // The surname parameter is a root/canonical form.
                // Display it as the actual surname
                $legend = implode('/', array_keys($this->surnames($tree, $surname, $alpha, $show_marnm === 'yes', $families, I18N::locale())));
            }
            $params = [
                'tree'    => $tree->name(),
                'surname' => $surname,
                'falpha'  => $falpha,
            ];
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
            $show = 'indi'; // SURN list makes no sense here
        } elseif ($alpha === '@') {
            $show_all = 'no';
            $legend   = I18N::translateContext('Unknown surname', '…');
            $params   = [
                'alpha' => $alpha,
                'tree'  => $tree->name(),
            ];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha === ',') {
            $show_all = 'no';
            $legend   = I18N::translate('No surname');
            $params   = [
                'alpha' => $alpha,
                'tree'  => $tree->name(),
            ];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha !== '') {
            $show_all = 'no';
            $legend   = e($alpha) . '…';
            $show     = $params['show'];
            $params   = [
                'alpha' => $alpha,
                'tree'  => $tree->name(),
            ];
        } else {
            $show_all = 'no';
            $legend   = '…';
            $params   = [
                'tree' => $tree->name(),
            ];
            $show     = 'none'; // Don't show lists until something is chosen
        }
        $legend = '<bdi>' . $legend . '</bdi>';

        if ($families) {
            $title = I18N::translate('Families') . ' — ' . $legend;
        } else {
            $title = I18N::translate('Individuals') . ' — ' . $legend;
        } ?>
        <div class="d-flex flex-column wt-page-options wt-page-options-individual-list d-print-none">
            <ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-surname">

                <?php foreach ($this->surnameAlpha($tree, $show_marnm === 'yes', $families, I18N::locale()) as $letter => $count) : ?>
                    <li class="wt-initials-list-item d-flex">
                        <?php if ($count > 0) : ?>
                            <a href="<?= e($this->listUrl($tree, ['alpha' => $letter, 'tree' => $tree->name()])) ?>" class="wt-initial px-1<?= $letter === $alpha ? ' active' : '' ?> '" title="<?= I18N::number($count) ?>"><?= $this->surnameInitial((string) $letter) ?></a>
                        <?php else : ?>
                            <span class="wt-initial px-1 text-muted"><?= $this->surnameInitial((string) $letter) ?></span>

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
                $surns = $this->surnames($tree, $surname, $alpha, $show_marnm === 'yes', $families, I18N::locale());
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
                    $count = 0;
                    foreach ($surns as $surnames) {
                        foreach ($surnames as $total) {
                            $count += $total;
                        }
                    }
                    // Don't sublist short lists.
                    if ($count < $tree->getPreference('SUBLIST_TRIGGER_I')) {
                        $falpha = '';
                    } else {
                        $givn_initials = $this->givenAlpha($tree, $surname, $alpha, $show_marnm === 'yes', $families, I18N::locale());
                        // Break long lists by initial letter of given name
                        if ($surname !== '' || $show_all === 'yes') {
                            if ($show_all === 'no') {
                                echo '<h2 class="wt-page-title">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
                            }
                            // Don't show the list until we have some filter criteria
                            $show = $falpha !== '' || $show_all_firstnames === 'yes' ? 'indi' : 'none';
                            $list = [];
                            echo '<ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-given-names">';
                            foreach ($givn_initials as $givn_initial => $given_count) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($given_count > 0) {
                                    if ($show === 'indi' && $givn_initial === $falpha && $show_all_firstnames === 'no') {
                                        echo '<a class="wt-initial px-1 active" href="' . e($this->listUrl($tree, ['falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($given_count) . '">' . $this->givenNameInitial((string) $givn_initial) . '</a>';
                                    } else {
                                        echo '<a class="wt-initial px-1" href="' . e($this->listUrl($tree, ['falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($given_count) . '">' . $this->givenNameInitial((string) $givn_initial) . '</a>';
                                    }
                                } else {
                                    echo '<span class="wt-initial px-1 text-muted">' . $this->givenNameInitial((string) $givn_initial) . '</span>';
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
                            echo '<p class="text-center alpha_index">', implode(' | ', $list), '</p>';
                        }
                    }
                    if ($show === 'indi') {
                        if ($families) {
                            echo view('lists/families-table', [
                                'families' => $this->families($tree, $surname, $alpha, $falpha, $show_marnm === 'yes', I18N::locale()),
                                'tree'     => $tree,
                            ]);
                        } else {
                            echo view('lists/individuals-table', [
                                'individuals' => $this->individuals($tree, $surname, $alpha, $falpha, $show_marnm === 'yes', false, I18N::locale()),
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
    protected function givenNameInitial(string $initial): string
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
    protected function surnameInitial(string $initial): string
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
     * Get a list of initial surname letters.
     *
     * @param Tree            $tree
     * @param bool            $marnm if set, include married names
     * @param bool            $fams  if set, only consider individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return array<int>
     */
    public function surnameAlpha(Tree $tree, bool $marnm, bool $fams, LocaleInterface $locale): array
    {
        $n_surn = $this->fieldWithCollation('n_surn');
        $alphas = [];

        $query = DB::table('name')->where('n_file', '=', $tree->id());

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        // Fetch all the letters in our alphabet, whether or not there
        // are any names beginning with that letter. It looks better to
        // show the full alphabet, rather than omitting rare letters such as X.
        foreach (I18N::language()->alphabet() as $letter) {
            $query2 = clone $query;

            $this->whereInitial($query2, 'n_surn', $letter, $locale);

            $alphas[$letter] = $query2->count();
        }

        // Now fetch initial letters that are not in our alphabet,
        // including "@" (for "@N.N.") and "" for no surname.
        foreach (I18N::language()->alphabet() as $letter) {
            $query->where($n_surn, 'NOT LIKE', $letter . '%');
        }

        $substring_function = DB::connection()->getDriverName() === 'sqlite' ? 'SUBSTR' : 'SUBSTRING';

        $rows = $query
            ->groupBy(['initial'])
            ->orderBy('initial')
            ->pluck(new Expression('COUNT(*) AS aggregate'), new Expression($substring_function . '(n_surn, 1, 1) AS initial'));

        $specials = ['@', ''];

        foreach ($rows as $alpha => $count) {
            if (!in_array($alpha, $specials, true)) {
                $alphas[$alpha] = (int) $count;
            }
        }

        // Empty surnames have a special code ',' - as we search for SURN,GIVN
        foreach ($specials as $special) {
            if ($rows->has($special)) {
                $alphas[$special ?: ','] = (int) $rows[$special];
            }
        }

        return $alphas;
    }

    /**
     * Get a list of initial given name letters for indilist.php and famlist.php
     *
     * @param Tree            $tree
     * @param string          $surn   if set, only consider people with this surname
     * @param string          $salpha if set, only consider surnames starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param bool            $fams   if set, only consider individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return array<int>
     */
    public function givenAlpha(Tree $tree, string $surn, string $salpha, bool $marnm, bool $fams, LocaleInterface $locale): array
    {
        $alphas = [];

        $query = DB::table('name')
            ->where('n_file', '=', $tree->id());

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surn !== '') {
            $n_surn = $this->fieldWithCollation('n_surn');
            $query->where($n_surn, '=', $surn);
        } elseif ($salpha === ',') {
            $query->where('n_surn', '=', '');
        } elseif ($salpha === '@') {
            $query->where('n_surn', '=', Individual::NOMEN_NESCIO);
        } elseif ($salpha !== '') {
            $this->whereInitial($query, 'n_surn', $salpha, $locale);
        } else {
            // All surnames
            $query->whereNotIn('n_surn', ['', Individual::NOMEN_NESCIO]);
        }

        // Fetch all the letters in our alphabet, whether or not there
        // are any names beginning with that letter. It looks better to
        // show the full alphabet, rather than omitting rare letters such as X
        foreach (I18N::language()->alphabet() as $letter) {
            $query2 = clone $query;

            $this->whereInitial($query2, 'n_givn', $letter, $locale);

            $alphas[$letter] = $query2->distinct()->count('n_id');
        }

        $substring_function = DB::connection()->getDriverName() === 'sqlite' ? 'SUBSTR' : 'SUBSTRING';

        $rows = $query
            ->groupBy(['initial'])
            ->orderBy('initial')
            ->pluck(new Expression('COUNT(*) AS aggregate'), new Expression('' . $substring_function . '(n_givn, 1, 1) AS initial'));

        foreach ($rows as $alpha => $count) {
            if ($alpha !== '@') {
                $alphas[$alpha] = (int) $count;
            }
        }

        if ($rows->has('@')) {
            $alphas['@'] = (int) $rows['@'];
        }

        return $alphas;
    }

    /**
     * Get a count of actual surnames and variants, based on a "root" surname.
     *
     * @param Tree            $tree
     * @param string          $surn   if set, only count people with this surname
     * @param string          $salpha if set, only consider surnames starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param bool            $fams   if set, only consider individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return array<array<int>>
     */
    protected function surnames(
        Tree $tree,
        string $surn,
        string $salpha,
        bool $marnm,
        bool $fams,
        LocaleInterface $locale
    ): array {
        $query = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->select([
                new Expression('n_surn /*! COLLATE utf8_bin */ AS n_surn'),
                new Expression('n_surname /*! COLLATE utf8_bin */ AS n_surname'),
                new Expression('COUNT(*) AS total'),
            ]);

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surn !== '') {
            $query->where('n_surn', '=', $surn);
        } elseif ($salpha === ',') {
            $query->where('n_surn', '=', '');
        } elseif ($salpha === '@') {
            $query->where('n_surn', '=', Individual::NOMEN_NESCIO);
        } elseif ($salpha !== '') {
            $this->whereInitial($query, 'n_surn', $salpha, $locale);
        } else {
            // All surnames
            $query->whereNotIn('n_surn', ['', Individual::NOMEN_NESCIO]);
        }
        $query->groupBy([
            new Expression('n_surn /*! COLLATE utf8_bin */'),
            new Expression('n_surname /*! COLLATE utf8_bin */'),
        ]);

        $list = [];

        foreach ($query->get() as $row) {
            $row->n_surn = strtr(I18N::strtoupper($row->n_surn), I18N::language()->equivalentLetters());
            $row->total += $list[$row->n_surn][$row->n_surname] ?? 0;

            $list[$row->n_surn][$row->n_surname] = (int) $row->total;
        }

        uksort($list, I18N::comparator());

        return $list;
    }

    /**
     * Fetch a list of individuals with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param Tree            $tree
     * @param string          $surn   if set, only fetch people with this surname
     * @param string          $salpha if set, only fetch surnames starting with this letter
     * @param string          $galpha if set, only fetch given names starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param bool            $fams   if set, only fetch individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return Collection<Individual>
     */
    protected function individuals(
        Tree $tree,
        string $surn,
        string $salpha,
        string $galpha,
        bool $marnm,
        bool $fams,
        LocaleInterface $locale
    ): Collection {
        // Use specific collation for name fields.
        $n_givn = $this->fieldWithCollation('n_givn');
        $n_surn = $this->fieldWithCollation('n_surn');

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

        if ($surn) {
            $query->where($n_surn, '=', $surn);
        } elseif ($salpha === ',') {
            $query->where($n_surn, '=', '');
        } elseif ($salpha === '@') {
            $query->where($n_surn, '=', Individual::NOMEN_NESCIO);
        } elseif ($salpha) {
            $this->whereInitial($query, 'n_surn', $salpha, $locale);
        } else {
            // All surnames
            $query->whereNotIn($n_surn, ['', Individual::NOMEN_NESCIO]);
        }
        if ($galpha) {
            $this->whereInitial($query, 'n_givn', $galpha, $locale);
        }

        $query
            ->orderBy(new Expression("CASE n_surn WHEN '" . Individual::NOMEN_NESCIO . "' THEN 1 ELSE 0 END"))
            ->orderBy($n_surn)
            ->orderBy(new Expression("CASE n_givn WHEN '" . Individual::NOMEN_NESCIO . "' THEN 1 ELSE 0 END"))
            ->orderBy($n_givn);

        $individuals = new Collection();
        $rows = $query->get();

        foreach ($rows as $row) {
            $individual = Registry::individualFactory()->make($row->xref, $tree, $row->gedcom);
            assert($individual instanceof Individual);

            // The name from the database may be private - check the filtered list...
            foreach ($individual->getAllNames() as $n => $name) {
                if ($name['givn'] === $row->n_givn && $name['surn'] === $row->n_surn) {
                    $individual->setPrimaryName($n);
                    // We need to clone $individual, as we may have multiple references to the
                    // same individual in this list, and the "primary name" would otherwise
                    // be shared amongst all of them.
                    $individuals->push(clone $individual);
                    break;
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
     * @param Tree            $tree
     * @param string          $surn   if set, only fetch people with this surname
     * @param string          $salpha if set, only fetch surnames starting with this letter
     * @param string          $galpha if set, only fetch given names starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param LocaleInterface $locale
     *
     * @return Collection<Family>
     */
    protected function families(Tree $tree, string $surn, string $salpha, string $galpha, bool $marnm, LocaleInterface $locale): Collection
    {
        $families = new Collection();

        foreach ($this->individuals($tree, $surn, $salpha, $galpha, $marnm, true, $locale) as $indi) {
            foreach ($indi->spouseFamilies() as $family) {
                $families->push($family);
            }
        }

        return $families->unique();
    }

    /**
     * Use MySQL-specific comments so we can run these queries on other RDBMS.
     *
     * @param string $field
     *
     * @return Expression
     */
    protected function fieldWithCollation(string $field): Expression
    {
        return new Expression($field . ' /*! COLLATE ' . I18N::collation() . ' */');
    }

    /**
     * Modify a query to restrict a field to a given initial letter.
     * Take account of digraphs, equialent letters, etc.
     *
     * @param Builder         $query
     * @param string          $field
     * @param string          $letter
     * @param LocaleInterface $locale
     *
     * @return void
     */
    protected function whereInitial(
        Builder $query,
        string $field,
        string $letter,
        LocaleInterface $locale
    ): void {
        // Use MySQL-specific comments so we can run these queries on other RDBMS.
        $field_with_collation = $this->fieldWithCollation($field);

        switch ($locale->languageTag()) {
            case 'cs':
                $this->whereInitialCzech($query, $field_with_collation, $letter);
                break;

            case 'da':
            case 'nb':
            case 'nn':
                $this->whereInitialNorwegian($query, $field_with_collation, $letter);
                break;

            case 'sv':
            case 'fi':
                $this->whereInitialSwedish($query, $field_with_collation, $letter);
                break;

            case 'hu':
                $this->whereInitialHungarian($query, $field_with_collation, $letter);
                break;

            case 'nl':
                $this->whereInitialDutch($query, $field_with_collation, $letter);
                break;

            default:
                $query->where($field_with_collation, 'LIKE', '\\' . $letter . '%');
        }
    }

    /**
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    protected function whereInitialCzech(Builder $query, Expression $field, string $letter): void
    {
        if ($letter === 'C') {
            $query->where($field, 'LIKE', 'C%')->where($field, 'NOT LIKE', 'CH%');
        } else {
            $query->where($field, 'LIKE', '\\' . $letter . '%');
        }
    }

    /**
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    protected function whereInitialDutch(Builder $query, Expression $field, string $letter): void
    {
        if ($letter === 'I') {
            $query->where($field, 'LIKE', 'I%')->where($field, 'NOT LIKE', 'IJ%');
        } else {
            $query->where($field, 'LIKE', '\\' . $letter . '%');
        }
    }

    /**
     * Hungarian has many digraphs and trigraphs, so exclude these from prefixes.
     *
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    protected function whereInitialHungarian(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'C':
                $query->where($field, 'LIKE', 'C%')->where($field, 'NOT LIKE', 'CS%');
                break;

            case 'D':
                $query->where($field, 'LIKE', 'D%')->where($field, 'NOT LIKE', 'DZ%');
                break;

            case 'DZ':
                $query->where($field, 'LIKE', 'DZ%')->where($field, 'NOT LIKE', 'DZS%');
                break;

            case 'G':
                $query->where($field, 'LIKE', 'G%')->where($field, 'NOT LIKE', 'GY%');
                break;

            case 'L':
                $query->where($field, 'LIKE', 'L%')->where($field, 'NOT LIKE', 'LY%');
                break;

            case 'N':
                $query->where($field, 'LIKE', 'N%')->where($field, 'NOT LIKE', 'NY%');
                break;

            case 'S':
                $query->where($field, 'LIKE', 'S%')->where($field, 'NOT LIKE', 'SZ%');
                break;

            case 'T':
                $query->where($field, 'LIKE', 'T%')->where($field, 'NOT LIKE', 'TY%');
                break;

            case 'Z':
                $query->where($field, 'LIKE', 'Z%')->where($field, 'NOT LIKE', 'ZS%');
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }

    /**
     * In Norwegian and Danish, AA gets listed under Å, NOT A
     *
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    protected function whereInitialNorwegian(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'A':
                $query->where($field, 'LIKE', 'A%')->where($field, 'NOT LIKE', 'AA%');
                break;

            case 'Å':
                $query->where(static function (Builder $query) use ($field): void {
                    $query
                        ->where($field, 'LIKE', 'Å%')
                        ->orWhere($field, 'LIKE', 'AA%');
                });
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }

    /**
     * In Swedish and Finnish, AA gets listed under A, NOT Å (even though Swedish collation says they should).
     *
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    protected function whereInitialSwedish(Builder $query, Expression $field, string $letter): void
    {
        if ($letter === 'Å') {
            $query->where($field, 'LIKE', 'Å%')->where($field, 'NOT LIKE', 'AA%');
        } else {
            $query->where($field, 'LIKE', '\\' . $letter . '%');
        }
    }
}
