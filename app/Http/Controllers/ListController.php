<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\IndividualListService;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function addcslashes;
use function array_combine;
use function array_keys;
use function array_slice;
use function array_unshift;
use function assert;
use function count;
use function dirname;
use function e;
use function implode;
use function max;
use function min;
use function ob_get_clean;
use function ob_start;
use function route;
use function view;

/**
 * Controller for lists of GEDCOM records.
 */
class ListController extends AbstractBaseController
{
    /** @var IndividualListService */
    private $individual_list_service;

    /** @var LocalizationService */
    private $localization_service;


    /**
     * ListController constructor.
     *
     * @param IndividualListService $individual_list_service
     * @param LocalizationService   $localization_service
     */
    public function __construct(IndividualListService $individual_list_service, LocalizationService $localization_service)
    {
        $this->individual_list_service = $individual_list_service;
        $this->localization_service    = $localization_service;
    }

    /**
     * Show a list of all individual or family records.
     *
     * @param ServerRequestInterface   $request
     * @param ModuleListInterface|null $moduleListInterface
     *
     * @return ResponseInterface
     */
    public function familyList(ServerRequestInterface $request, ?ModuleListInterface $moduleListInterface): ResponseInterface
    {
        return $this->individualOrFamilyList($request, true, $moduleListInterface);
    }

    /**
     * Show a list of all individual or family records.
     *
     * @param ServerRequestInterface   $request
     * @param ModuleListInterface|null $moduleListInterface
     *
     * @return ResponseInterface
     */
    public function individualList(ServerRequestInterface $request, ?ModuleListInterface $moduleListInterface): ResponseInterface
    {
        return $this->individualOrFamilyList($request, false, $moduleListInterface);
    }

    /**
     * @param ServerRequestInterface   $request
     * @param bool                     $families
     * @param ModuleListInterface|null $moduleListInterface
     *
     * @return ResponseInterface
     */
    public function individualOrFamilyList(ServerRequestInterface $request, bool $families, ?ModuleListInterface $moduleListInterface): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');

        // This action can show lists of both families and individuals.
        //route is assumed to be 'module'
        $module = $request->getAttribute('module');
        $action = $request->getAttribute('action');

        ob_start();

        // We show three different lists: initials, surnames and individuals

        // All surnames beginning with this letter where "@"=unknown and ","=none
        $alpha = $request->getQueryParams()['alpha'] ?? '';

        // All individuals with this surname
        $surname = $request->getQueryParams()['surname'] ??  '';

        // All individuals
        $show_all = $request->getQueryParams()['show_all'] ?? 'no';

        // Long lists can be broken down by given name
        $show_all_firstnames = $request->getQueryParams()['show_all_firstnames'] ?? 'no';
        if ($show_all_firstnames === 'yes') {
            $falpha = '';
        } else {
            // All first names beginning with this letter
            $falpha = $request->getQueryParams()['falpha'] ?? '';
        }

        $show_marnm = $request->getQueryParams()['show_marnm'] ?? '';
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
            if ($show_all_firstnames === 'yes') {
                $alpha   = '';
                $surname = '';
                $legend  = I18N::translate('All');
                $params  = [
                    'tree'      => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = 'indi';
            } elseif ($falpha !== '') {
                $alpha   = '';
                $surname = '';
                $legend  = I18N::translate('All') . ', ' . e($falpha) . '…';
                $params  = [
                    'tree'      => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = 'indi';
            } else {
                $alpha   = '';
                $surname = '';
                $legend  = I18N::translate('All');
                $params  = [
                    'tree'      => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = $request->getQueryParams()['show'] ?? 'surn';
            }
        } elseif ($surname !== '') {
            $alpha    = $this->localization_service->initialLetter($surname, I18N::locale()); // so we can highlight the initial letter
            $show_all = 'no';
            if ($surname === '@N.N.') {
                $legend = I18N::translateContext('Unknown surname', '…');
            } else {
                // The surname parameter is a root/canonical form.
                // Display it as the actual surname
                $legend = implode('/', array_keys($this->individual_list_service->surnames($surname, $alpha, $show_marnm === 'yes', $families, I18N::locale())));
            }
            $params = [
                'tree'     => $tree->name(),
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
                'tree'   => $tree->name(),
            ];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha === ',') {
            $show_all = 'no';
            $legend   = I18N::translate('None');
            $params   = [
                'alpha' => $alpha,
                'tree'   => $tree->name(),
            ];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha !== '') {
            $show_all = 'no';
            $legend   = e($alpha) . '…';
            $params   = [
                'alpha' => $alpha,
                'tree'   => $tree->name(),
            ];
            $show     = $request->getQueryParams()['show'] ?? 'surn';
        } else {
            $show_all = 'no';
            $legend   = '…';
            $params   = [
                'tree' => $tree->name(),
            ];
            $show     = 'none'; // Don't show lists until something is chosen
        }
        $legend = '<span dir="auto">' . $legend . '</span>';

        if ($families) {
            $title = I18N::translate('Families') . ' — ' . $legend;
        } else {
            $title = I18N::translate('Individuals') . ' — ' . $legend;
        } ?>
        <div class="d-flex flex-column wt-page-options wt-page-options-individual-list d-print-none">
            <ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-surname">

                <?php foreach ($this->individual_list_service->surnameAlpha($show_marnm === 'yes', $families, I18N::locale()) as $letter => $count) : ?>
                    <li class="wt-initials-list-item d-flex">
                        <?php if ($count > 0) : ?>
                            <a href="<?= e(route('module', ['module' => $module, 'action' => $action, 'alpha' => $letter, 'tree' => $tree->name()])) ?>" class="wt-initial px-1<?= $letter === $alpha ? ' active' : '' ?> '" title="<?= I18N::number($count) ?>"><?= $this->surnameInitial((string) $letter) ?></a>
                        <?php else : ?>
                            <span class="wt-initial px-1 text-muted"><?= $this->surnameInitial((string) $letter) ?></span>

                        <?php endif ?>
                    </li>
                <?php endforeach ?>

                <?php if (Session::has('initiated')) : ?>
                    <!-- Search spiders don't get the "show all" option as the other links give them everything. -->
                    <li class="wt-initials-list-item d-flex">
                        <a class="wt-initial px-1<?= $show_all === 'yes' ? ' active' : '' ?>" href="<?= e(route('module', ['module' => $module, 'action' => $action, 'show_all' => 'yes'] + $params)) ?>"><?= I18N::translate('All') ?></a>
                    </li>
                <?php endif ?>
            </ul>

            <!-- Search spiders don't get an option to show/hide the surname sublists, nor does it make sense on the all/unknown/surname views -->
            <?php if ($show !== 'none' && Session::has('initiated')) : ?>
                <?php if ($show_marnm === 'yes') : ?>
                    <p>
                        <a href="<?= e(route('module', ['module' => $module, 'action' => $action, 'show' => $show, 'show_marnm' => 'no'] + $params)) ?>">
                            <?= I18N::translate('Exclude individuals with “%s” as a married name', $legend) ?>
                        </a>
                    </p>
                <?php else : ?>
                    <p>
                        <a href="<?= e(route('module', ['module' => $module, 'action' => $action, 'show' => $show, 'show_marnm' => 'yes'] + $params)) ?>">
                            <?= I18N::translate('Include individuals with “%s” as a married name', $legend) ?>
                        </a>
                    </p>
                <?php endif ?>

                <?php if ($alpha !== '@' && $alpha !== ',' && $surname === '') : ?>
                    <?php if ($show === 'surn') : ?>
                        <p>
                            <a href="<?= e(route('module', ['module' => $module, 'action' => $action, 'show' => 'indi', 'show_marnm' => 'no'] + $params)) ?>">
                                <?= I18N::translate('Show the list of individuals') ?>
                            </a>
                        </p>
                    <?php else : ?>
                        <p>
                            <a href="<?= e(route('module', ['module' => $module, 'action' => $action, 'show' => 'surn', 'show_marnm' => 'no'] + $params)) ?>">
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
                $surns = $this->individual_list_service->surnames($surname, $alpha, $show_marnm === 'yes', $families, I18N::locale());
                if ($show === 'surn') {
                    // Show the surname list
                    switch ($tree->getPreference('SURNAME_LIST_STYLE')) {
                        case 'style1':
                            echo FunctionsPrintLists::surnameList($surns, 3, true, $moduleListInterface, $tree);
                            break;
                        case 'style3':
                            echo FunctionsPrintLists::surnameTagCloud($surns, $moduleListInterface, true, $tree);
                            break;
                        case 'style2':
                        default:
                            echo view('lists/surnames-table', [
                                'surnames' => $surns,
                                'families' => $families,
                                'module'   => $moduleListInterface,
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
                    // Don't sublists short lists.
                    if ($count < $tree->getPreference('SUBLIST_TRIGGER_I')) {
                        $falpha = '';
                    } else {
                        $givn_initials = $this->individual_list_service->givenAlpha($surname, $alpha, $show_marnm === 'yes', $families, I18N::locale());
                        // Break long lists by initial letter of given name
                        if ($surname !== '' || $show_all === 'yes') {
                            if ($show_all === 'no') {
                                echo '<h2 class="wt-page-title">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
                            }
                            // Don't show the list until we have some filter criteria
                            $show = ($falpha !== '' || $show_all_firstnames === 'yes') ? 'indi' : 'none';
                            $list = [];
                            echo '<ul class="d-flex flex-wrap list-unstyled justify-content-center wt-initials-list wt-initials-list-given-names">';
                            foreach ($givn_initials as $givn_initial => $given_count) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($given_count > 0) {
                                    if ($show === 'indi' && $givn_initial === $falpha && $show_all_firstnames === 'no') {
                                        echo '<a class="wt-initial px-1 active" href="' . e(route('module', ['module' => $module, 'action' => $action, 'falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($given_count) . '">' . $this->givenNameInitial((string) $givn_initial) . '</a>';
                                    } else {
                                        echo '<a class="wt-initial px-1" href="' . e(route('module', ['module' => $module, 'action' => $action, 'falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($given_count) . '">' . $this->givenNameInitial((string) $givn_initial) . '</a>';
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
                                    echo '<span class="wt-initial px-1 warning">' . I18N::translate('All') . '</span>';
                                } else {
                                    echo '<a class="wt-initial px-1" href="' . e(route('module', ['module' => $module, 'action' => $action, 'show_all_firstnames' => 'yes'] + $params)) . '" title="' . I18N::number($count) . '">' . I18N::translate('All') . '</a>';
                                }
                                echo '</li>';
                            }
                            echo '</ul>';
                            echo '<p class="text-center alpha_index">', implode(' | ', $list), '</p>';
                        }
                    }
                    if ($show === 'indi') {
                        if (!$families) {
                            echo view('lists/individuals-table', [
                                'individuals' => $this->individual_list_service->individuals($surname, $alpha, $falpha, $show_marnm === 'yes', false, I18N::locale()),
                                'sosa'        => false,
                                'tree'        => $tree,
                            ]);
                        } else {
                            echo view('lists/families-table', [
                                'families' => $this->individual_list_service->families($surname, $alpha, $falpha, $show_marnm === 'yes', I18N::locale()),
                                'tree'     => $tree,
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
     * Show a list of all media records.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mediaList(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $module    = $request->getAttribute('module');
        $action    = $request->getAttribute('action');
        $params    = $request->getQueryParams();
        $formats   = GedcomTag::getFileFormTypes();
        $action2   = $params['action2'] ?? '';
        $page      = (int) ($params['page'] ?? 1);
        $max       = (int) ($params['max'] ?? 20);
        $folder    = $params['folder'] ?? '';
        $filter    = $params['filter'] ?? '';
        $subdirs   = $params['subdirs'] ?? '';
        $form_type = $params['form_type'] ?? '';

        $folders = $this->allFolders($tree);

        if ($action2 === '1') {
            $media_objects = $this->allMedia(
                $tree,
                $folder,
                $subdirs === '1' ? 'include' : 'exclude',
                'title',
                $filter,
                $form_type
            );
        } else {
            $media_objects = [];
        }

        // Pagination
        $count = count($media_objects);
        $pages = (int) (($count + $max - 1) / $max);
        $page  = max(min($page, $pages), 1);

        $media_objects = array_slice($media_objects, ($page - 1) * $max, $max);

        return $this->viewResponse('modules/media-list/page', [
            'count'           => $count,
            'filter'          => $filter,
            'folder'          => $folder,
            'folders'         => $folders,
            'formats'         => $formats,
            'form_type'       => $form_type,
            'max'             => $max,
            'media_objects'   => new Collection($media_objects),
            'page'            => $page,
            'pages'           => $pages,
            'subdirs'         => $subdirs,
            'title'           => I18N::translate('Media'),
            'tree'            => $tree,
            'module'          => $module,
            'action'          => $action,
            'data_filesystem' => $data_filesystem,
        ]);
    }

    /**
     * Show a list of all note records.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function noteList(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $notes = $this->allNotes($tree);

        return $this->viewResponse('modules/note-list/page', [
            'notes' => $notes,
            'title' => I18N::translate('Shared notes'),
            'tree'  => $tree,
        ]);
    }

    /**
     * Show a list of all repository records.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function repositoryList(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $repositories = $this->allRepositories($tree);

        return $this->viewResponse('modules/repository-list/page', [
            'repositories' => $repositories,
            'title'        => I18N::translate('Repositories'),
            'tree'         => $tree,
        ]);
    }

    /**
     * Show a list of all source records.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function sourceList(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $sources = $this->allSources($tree);

        return $this->viewResponse('modules/source-list/page', [
            'sources' => $sources,
            'title'   => I18N::translate('Sources'),
            'tree'    => $tree,
        ]);
    }

    /**
     * Show a list of all submitter records.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function submitterList(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $submitters = $this->allSubmitters($tree);

        return $this->viewResponse('modules/submitter-list/page', [
            'submitters'   => $submitters,
            'title'        => I18N::translate('Submitters'),
            'tree'         => $tree,
        ]);
    }

    /**
     * Generate a list of all the folders in a current tree.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    private function allFolders(Tree $tree): array
    {
        $folders = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('multimedia_file_refn', 'NOT LIKE', 'http:%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https:%')
            ->where('multimedia_file_refn', 'LIKE', '%/%')
            ->pluck('multimedia_file_refn', 'multimedia_file_refn')
            ->map(static function (string $path): string {
                return dirname($path);
            })
            ->uniqueStrict()
            ->sort()
            ->all();

        // Ensure we have an empty (top level) folder.
        array_unshift($folders, '');

        return array_combine($folders, $folders);
    }

    /**
     * Generate a list of all the media objects matching the criteria in a current tree.
     *
     * @param Tree   $tree       find media in this tree
     * @param string $folder     folder to search
     * @param string $subfolders either "include" or "exclude"
     * @param string $sort       either "file" or "title"
     * @param string $filter     optional search string
     * @param string $form_type  option OBJE/FILE/FORM/TYPE
     *
     * @return Media[]
     */
    private function allMedia(Tree $tree, string $folder, string $subfolders, string $sort, string $filter, string $form_type): array
    {
        $query = DB::table('media')
            ->join('media_file', static function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->where('media.m_file', '=', $tree->id());

        if ($folder === '') {
            // Include external URLs in the root folder.
            if ($subfolders === 'exclude') {
                $query->where(static function (Builder $query): void {
                    $query
                        ->where('multimedia_file_refn', 'NOT LIKE', '%/%')
                        ->orWhere('multimedia_file_refn', 'LIKE', 'http:%')
                        ->orWhere('multimedia_file_refn', 'LIKE', 'https:%');
                });
            }
        } else {
            // Exclude external URLs from the root folder.
            $query
                ->where('multimedia_file_refn', 'LIKE', $folder . '/%')
                ->where('multimedia_file_refn', 'NOT LIKE', 'http:%')
                ->where('multimedia_file_refn', 'NOT LIKE', 'https:%');

            if ($subfolders === 'exclude') {
                $query->where('multimedia_file_refn', 'NOT LIKE', $folder . '/%/%');
            }
        }

        // Apply search terms
        if ($filter !== '') {
            $query->where(static function (Builder $query) use ($filter): void {
                $like = '%' . addcslashes($filter, '\\%_') . '%';
                $query
                    ->where('multimedia_file_refn', 'LIKE', $like)
                    ->orWhere('descriptive_title', 'LIKE', $like);
            });
        }

        if ($form_type) {
            $query->where('source_media_type', '=', $form_type);
        }

        switch ($sort) {
            case 'file':
                $query->orderBy('multimedia_file_refn');
                break;
            case 'title':
                $query->orderBy('descriptive_title');
                break;
        }

        return $query
            ->get()
            ->map(Factory::media()->mapper($tree))
            ->filter(GedcomRecord::accessFilter())
            ->all();
    }

    /**
     * Find all the note records in a tree.
     *
     * @param Tree $tree
     *
     * @return Collection<Note>
     */
    private function allNotes(Tree $tree): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->get()
            ->map(Factory::note()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find all the repository record in a tree.
     *
     * @param Tree $tree
     *
     * @return Collection<Repository>
     */
    private function allRepositories(Tree $tree): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->get()
            ->map(Factory::repository()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find all the source records in a tree.
     *
     * @param Tree $tree
     *
     * @return Collection<Source>
     */
    private function allSources(Tree $tree): Collection
    {
        return DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->get()
            ->map(Factory::source()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find all the submitter record in a tree.
     *
     * @param Tree $tree
     *
     * @return Collection<Submitter>
     */
    private function allSubmitters(Tree $tree): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Submitter::RECORD_TYPE)
            ->get()
            ->map(Factory::submitter()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Some initial letters have a special meaning
     *
     * @param string $initial
     *
     * @return string
     */
    private function givenNameInitial(string $initial): string
    {
        switch ($initial) {
            case '@':
                return I18N::translateContext('Unknown given name', '…');
            default:
                return e($initial);
        }
    }

    /**
     * Some initial letters have a special meaning
     *
     * @param string $initial
     *
     * @return string
     */
    private function surnameInitial(string $initial): string
    {
        switch ($initial) {
            case '@':
                return I18N::translateContext('Unknown surname', '…');
            case ',':
                return I18N::translate('None');
            default:
                return e($initial);
        }
    }
}
