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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for lists of GEDCOM records.
 */
class ListController extends AbstractBaseController
{
    /** @var LocalizationService */
    private $localization_service;

    /**
     * ListController constructor.
     *
     * @param LocalizationService $localization_service
     */
    public function __construct(LocalizationService $localization_service)
    {
        $this->localization_service = $localization_service;
    }

    /**
     * Show a list of all individual or family records.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return Response
     */
    public function familyList(Request $request, Tree $tree, User $user): Response
    {
        return $this->individualList($request, $tree, $user);
    }

    /**
     * Show a list of all individual or family records.
     *
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return Response
     */
    public function individualList(Request $request, Tree $tree, User $user): Response
    {
        // This action can show lists of both families and individuals.
        $route    = $request->get('route');
        $families = $route === 'family-list';

        ob_start();

        // We show three different lists: initials, surnames and individuals

        // All surnames beginning with this letter where "@"=unknown and ","=none
        $alpha = $request->get('alpha', '');

        // All individuals with this surname
        $surname = $request->get('surname', '');

        // All individuals
        $show_all = $request->get('show_all', 'no');

        // Long lists can be broken down by given name
        $show_all_firstnames = $request->get('show_all_firstnames', 'no');
        if ($show_all_firstnames === 'yes') {
            $falpha = '';
        } else {
            $falpha = $request->get('falpha'); // All first names beginning with this letter
        }

        $show_marnm = $request->get('show_marnm');
        switch ($show_marnm) {
            case 'no':
            case 'yes':
                $user->setPreference($route . '-marnm', $show_marnm);
                break;
            default:
                $show_marnm = $user->getPreference($route . '-marnm');
        }

        // Make sure selections are consistent.
        // i.e. can’t specify show_all and surname at the same time.
        if ($show_all === 'yes') {
            if ($show_all_firstnames === 'yes') {
                $alpha   = '';
                $surname = '';
                $legend  = I18N::translate('All');
                $params  = [
                    'ged'      => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = 'indi';
            } elseif ($falpha) {
                $alpha   = '';
                $surname = '';
                $legend  = I18N::translate('All') . ', ' . e($falpha) . '…';
                $params  = [
                    'ged'      => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show    = 'indi';
            } else {
                $alpha   = '';
                $surname = '';
                $legend  = I18N::translate('All');
                $params  = [
                    'ged'      => $tree->name(),
                    'show_all' => 'yes',
                ];
                $show = $request->get('show', 'surn');
            }
        } elseif ($surname) {
            $alpha    = $this->localization_service->initialLetter($surname); // so we can highlight the initial letter
            $show_all = 'no';
            if ($surname === '@N.N.') {
                $legend = I18N::translateContext('Unknown surname', '…');
            } else {
                // The surname parameter is a root/canonical form.
                // Display it as the actual surname
                $legend = implode('/', array_keys($this->surnames($tree, $surname, $alpha, $show_marnm === 'yes', $families)));
            }
            $params = [
                'ged'     => $tree->name(),
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
                'ged'   => $tree->name(),
            ];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha === ',') {
            $show_all = 'no';
            $legend   = I18N::translate('None');
            $params   = [
                'alpha' => $alpha,
                'ged'   => $tree->name(),
            ];
            $show     = 'indi'; // SURN list makes no sense here
        } elseif ($alpha) {
            $show_all = 'no';
            $legend   = e($alpha) . '…';
            $params   = [
                'alpha' => $alpha,
                'ged'   => $tree->name(),
            ];
            $show     = $request->get('show', 'surn');
        } else {
            $show_all = 'no';
            $legend   = '…';
            $params   = [
                'ged' => $tree->name(),
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
            <ul class="d-flex flex-wrap wt-initials-list">

                <?php foreach ($this->surnameAlpha($tree, $show_marnm === 'yes', $families) as $letter => $count): ?>
                    <li class="wt-initials-list-item d-flex">
                        <?php if ($count > 0): ?>
                            <a href="<?= e(route($route, ['alpha' => $letter, 'ged' => $tree->name()])) ?>" class="wt-initial<?= $letter === $alpha ? ' active' : ''?> '" title="<?= I18N::number($count) ?>"><?= $this->surnameInitial((string) $letter) ?></a>
                        <?php else: ?>
                            <span class="wt-initial text-muted"><?= $this->surnameInitial((string) $letter) ?></span>

                        <?php endif ?>
                    </li>
                <?php endforeach ?>

                <?php if (Session::has('initiated')): ?>
                    <!-- Search spiders don't get the "show all" option as the other links give them everything. -->
                    <li class="wt-initials-list-item d-flex">
                        <a class="wt-initial<?= $show_all === 'yes' ? ' active' : '' ?>" href="<?= e(route($route, ['show_all' => 'yes'] + $params)) ?>"><?= I18N::translate('All') ?></a>
                    </li>
                <?php endif ?>
            </ul>

            <!-- Search spiders don't get an option to show/hide the surname sublists, nor does it make sense on the all/unknown/surname views -->
            <?php if (Session::has('initiated') && $show !== 'none'): ?>
                <?php if ($show_marnm === 'yes'): ?>
                    <p>
                        <a href="<?= e(route($route, ['show' => $show, 'show_marnm' => 'no'] + $params)) ?>">
                            <?= I18N::translate('Exclude individuals with “%s” as a married name', $legend) ?>
                        </a>
                    </p>
                <?php else: ?>
                    <p>
                        <a href="<?= e(route($route, ['show' => $show, 'show_marnm' => 'yes'] + $params)) ?>">
                            <?= I18N::translate('Include individuals with “%s” as a married name', $legend)?>
                        </a>
                    </p>
                <?php endif ?>

                <?php if ($alpha !== '@' && $alpha !== ',' && !$surname): ?>
                    <?php if ($show === 'surn'): ?>
                        <p>
                            <a href="<?= e(route($route, ['show' => 'indi', 'show_marnm' => 'no'] + $params)) ?>">
                                <?= I18N::translate('Show the list of individuals') ?>
                            </a>
                        </p>
                    <?php else: ?>
                        <p>
                            <a href="<?= e(route($route, ['show' => 'surn', 'show_marnm' => 'no'] + $params)) ?>">
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
                $surns = $this->surnames($tree, $surname, $alpha, $show_marnm === 'yes', $families);
                if ($show === 'surn') {
                    // Show the surname list
                    switch ($tree->getPreference('SURNAME_LIST_STYLE')) {
                        case 'style1':
                            echo FunctionsPrintLists::surnameList($surns, 3, true, $route, $tree);
                            break;
                        case 'style3':
                            echo FunctionsPrintLists::surnameTagCloud($surns, $route, true, $tree);
                            break;
                        case 'style2':
                        default:
                            echo view('lists/surnames-table', [
                                'surnames' => $surns,
                                'route'    => $route,
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
                        $givn_initials = $this->givenAlpha($tree, $surname, $alpha, $show_marnm === 'yes', $families);
                        // Break long lists by initial letter of given name
                        if ($surname || $show_all === 'yes') {
                            if ($show_all === 'no') {
                                echo '<h2 class="wt-page-title">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
                            }
                            // Don't show the list until we have some filter criteria
                            $show = ($falpha || $show_all_firstnames === 'yes') ? 'indi' : 'none';
                            $list = [];
                            echo '<ul class="d-flex flex-wrap justify-content-center wt-initials-list">';
                            foreach ($givn_initials as $givn_initial => $count) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($count > 0) {
                                    if ($show === 'indi' && $givn_initial === $falpha && $show_all_firstnames === 'no') {
                                        echo '<a class="wt-initial active" href="' . e(route($route, ['falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($count) . '">' . $this->givenNameInitial((string) $givn_initial) . '</a>';
                                    } else {
                                        echo '<a class="wt-initial" href="' . e(route($route, ['falpha' => $givn_initial] + $params)) . '" title="' . I18N::number($count) . '">' . $this->givenNameInitial((string) $givn_initial) . '</a>';
                                    }
                                } else {
                                    echo '<span class="wt-initial text-muted">' . $this->givenNameInitial((string) $givn_initial) . '</span>';
                                }
                                echo '</li>';
                            }
                            // Search spiders don't get the "show all" option as the other links give them everything.
                            if (Session::has('initiated')) {
                                echo '<li class="wt-initials-list-item d-flex">';
                                if ($show_all_firstnames === 'yes') {
                                    echo '<span class="wt-initial warning">' . I18N::translate('All') . '</span>';
                                } else {
                                    echo '<a class="wt-initial" href="' . e(route($route, ['show_all_firstnames' => 'yes'] + $params)) . '" title="' . I18N::number($count) . '">' . I18N::translate('All') . '</a>';
                                }
                                echo '</li>';
                            }
                            echo '</ul>';
                            echo '<p class="center alpha_index">', implode(' | ', $list), '</p>';
                        }
                    }
                    if ($show === 'indi') {
                        if ($route === 'individual-list') {
                            echo view('lists/individuals-table', [
                                'individuals' => $this->individuals($tree, $surname, $alpha, $falpha, $show_marnm === 'yes', false),
                                'sosa'        => false,
                                'tree'        => $tree,
                            ]);
                        } else {
                            echo view('lists/families-table', [
                                'families' => $this->families($tree, $surname, $alpha, $falpha, $show_marnm === 'yes'),
                                'tree'     => $tree,
                            ]);
                        }
                    }
                }
            } ?>
        </div>
        <?php

        $html = ob_get_clean();

        // @TODO convert this to use views
        return $this->viewResponse('individual-list-page', [
            'content' => $html,
            'title'   => $title,
        ]);
    }

    /**
     * Show a list of all media records.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function mediaList(Request $request, Tree $tree): Response
    {
        $formats = GedcomTag::getFileFormTypes();

        $action    = $request->get('action');
        $page      = (int) $request->get('page');
        $max       = (int) $request->get('max', 20);
        $folder    = $request->get('folder', '');
        $filter    = $request->get('filter', '');
        $subdirs   = $request->get('subdirs', '');
        $form_type = $request->get('form_type', '');

        $folders = $this->allFolders($tree);

        if ($action === '1') {
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

        return $this->viewResponse('media-list-page', [
            'count'         => $count,
            'filter'        => $filter,
            'folder'        => $folder,
            'folders'       => $folders,
            'formats'       => $formats,
            'form_type'     => $form_type,
            'max'           => $max,
            'media_objects' => $media_objects,
            'page'          => $page,
            'pages'         => $pages,
            'subdirs'       => $subdirs,
            'title'         => I18N::translate('Media'),
        ]);
    }

    /**
     * Show a list of all note records.
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function noteList(Tree $tree): Response
    {
        $notes = $this->allNotes($tree);

        return $this->viewResponse('note-list-page', [
            'notes' => $notes,
            'title' => I18N::translate('Shared notes'),
        ]);
    }

    /**
     * Show a list of all repository records.
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function repositoryList(Tree $tree): Response
    {
        $repositories = $this->allRepositories($tree);

        return $this->viewResponse('repository-list-page', [
            'repositories' => $repositories,
            'title'        => I18N::translate('Repositories'),
        ]);
    }

    /**
     * Show a list of all source records.
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function sourceList(Tree $tree): Response
    {
        $sources = $this->allSources($tree);

        return $this->viewResponse('source-list-page', [
            'sources' => $sources,
            'title'   => I18N::translate('Sources'),
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
        $folders = Database::prepare(
            "SELECT LEFT(multimedia_file_refn, CHAR_LENGTH(multimedia_file_refn) - CHAR_LENGTH(SUBSTRING_INDEX(multimedia_file_refn, '/', -1))) AS media_path" .
            " FROM  `##media_file`" .
            " WHERE m_file = ?" .
            " AND   multimedia_file_refn NOT LIKE 'http://%'" .
            " AND   multimedia_file_refn NOT LIKE 'https://%'" .
            " GROUP BY 1" .
            " ORDER BY 1"
        )->execute([
            $tree->id(),
        ])->fetchOneColumn();

        // Ensure we have an empty (top level) folder.
        if (!$folders || reset($folders) !== '') {
            array_unshift($folders, '');
        }

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
        // All files in the folder, plus external files
        $sql =
            "SELECT m_id AS xref, m_gedcom AS gedcom" .
            " FROM `##media`" .
            " JOIN `##media_file` USING (m_id, m_file)" .
            " WHERE m_file = ?";
        $args = [
            $tree->id(),
        ];

        // Only show external files when we are looking at the root folder
        if ($folder == '') {
            $sql_external = " OR multimedia_file_refn LIKE 'http://%' OR multimedia_file_refn LIKE 'https://%'";
        } else {
            $sql_external = "";
        }

        // Include / exclude subfolders (but always include external)
        switch ($subfolders) {
            case 'include':
                $sql .= " AND (multimedia_file_refn LIKE CONCAT(?, '%') $sql_external)";
                $args[] = Database::escapeLike($folder);
                break;
            case 'exclude':
                $sql .= " AND (multimedia_file_refn LIKE CONCAT(?, '%') AND multimedia_file_refn NOT LIKE CONCAT(?, '%/%') $sql_external)";
                $args[] = Database::escapeLike($folder);
                $args[] = Database::escapeLike($folder);
                break;
        }

        // Apply search terms
        if ($filter) {
            $sql .= " AND (SUBSTRING_INDEX(multimedia_file_refn, '/', -1) LIKE CONCAT('%', ?, '%') OR descriptive_title LIKE CONCAT('%', ?, '%'))";
            $args[] = Database::escapeLike($filter);
            $args[] = Database::escapeLike($filter);
        }

        if ($form_type) {
            $sql .= " AND source_media_type = ?";
            $args[] = $form_type;
        }

        switch ($sort) {
            case 'file':
                $sql .= " ORDER BY multimedia_file_refn";
                break;
            case 'title':
                $sql .= " ORDER BY descriptive_title";
                break;
        }

        $rows = Database::prepare($sql)->execute($args)->fetchAll();
        $list = [];
        foreach ($rows as $row) {
            $media = Media::getInstance($row->xref, $tree, $row->gedcom);
            if ($media->canShow()) {
                $list[] = $media;
            }
        }

        return $list;
    }

    /**
     * Find all the note records in a tree.
     *
     * @param Tree $tree
     *
     * @return array
     */
    private function allNotes(Tree $tree): array
    {
        $rows = Database::prepare(
            "SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'NOTE' AND o_file = :tree_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        $list = [];
        foreach ($rows as $row) {
            $list[] = Note::getInstance($row->xref, $tree, $row->gedcom);
        }

        return array_filter($list, function (Note $x): bool {
            return $x->canShowName();
        });
    }

    /**
     * Find all the repository record in a tree.
     *
     * @param Tree $tree
     *
     * @return array
     */
    private function allRepositories(Tree $tree): array
    {
        $rows = Database::prepare(
            "SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'REPO' AND o_file = ?"
        )->execute([
            $tree->id(),
        ])->fetchAll();

        $list = [];
        foreach ($rows as $row) {
            $list[] = Repository::getInstance($row->xref, $tree, $row->gedcom);
        }

        return array_filter($list, function (Repository $x): bool {
            return $x->canShowName();
        });
    }

    /**
     * Find all the source records in a tree.
     *
     * @param Tree $tree
     *
     * @return array
     */
    private function allSources(Tree $tree): array
    {
        $rows = Database::prepare(
            "SELECT s_id AS xref, s_gedcom AS gedcom FROM `##sources` WHERE s_file = :tree_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        $list = [];
        foreach ($rows as $row) {
            $list[] = Source::getInstance($row->xref, $tree, $row->gedcom);
        }

        return array_filter($list, function (Source $x): bool {
            return $x->canShow();
        });
    }

    /**
     * Generate SQL to match a given letter, taking care of cases that
     * are not covered by the collation setting.
     * We must consider:
     * potential substrings, such as Czech "CH" and "C"
     * equivalent letters, such as Danish "AA" and "Å"
     * We COULD write something that handles all languages generically,
     * but its performance would most likely be poor.
     * For languages that don't appear in this list, we could write
     * simpler versions of the surnameAlpha() and givenAlpha() functions,
     * but it gives no noticable improvement in performance.
     *
     * @param string $field
     * @param string $letter
     *
     * @return mixed[] Where clause and array of bind variables
     */
    private function getInitialSql($field, $letter): array
    {
        $collate = 'COLLATE ' . I18N::collation();

        switch ($field) {
            case 'n_givn':
                $where1 = " AND n_givn LIKE :n_givn1 " . $collate . " AND n_givn NOT LIKE :n_givn2 " . $collate;
                $where2 = " AND (n_givn LIKE :n_givn1 " . $collate . " OR n_givn LIKE :n_givn2 " . $collate . ")";
                $where3 = " AND n_givn LIKE CONCAT('@', :n_givn1, '%') " . $collate . " ESCAPE '@'";
                break;

            case 'n_surn':
                $where1 = " AND n_surn LIKE :n_surn1 " . $collate . " AND n_surn NOT LIKE :n_surn2 " . $collate;
                $where2 = " AND (n_surn LIKE :n_surn1 " . $collate . " OR n_surn LIKE :n_surn2 " . $collate . ")";
                $where3 = " AND n_surn LIKE CONCAT('@', :n_surn1, '%') " . $collate . " ESCAPE '@'";
                break;

            default:
                throw new InvalidArgumentException('ListController::getInitialSql(' . $field . ')');
        }

        switch (WT_LOCALE) {
            case 'cs':
                switch ($letter) {
                    case 'C':
                        return [$where1, [$field . '1' => 'C%', $field . '2' => 'CH%']];
                }
                break;
            case 'da':
            case 'nb':
            case 'nn':
                switch ($letter) {
                    // AA gets listed under Å
                    case 'A':
                        return [$where1, [$field . '1' => 'A%', $field . '2' => 'AA%']];
                    case 'Å':
                        return [$where2, [$field . '1' => 'Å%', $field . '2' => 'AA%']];
                }
                break;
            case 'hu':
                switch ($letter) {
                    case 'C':
                        return [$where1, [$field . '1' => 'C%', $field . '2' => 'CS%']];
                    case 'D':
                        return [$where1, [$field . '1' => 'D%', $field . '2' => 'DZ%']];
                    case 'DZ':
                        return [$where1, [$field . '1' => 'DZ%', $field . '2' => 'DZS%']];
                    case 'G':
                        return [$where1, [$field . '1' => 'G%', $field . '2' => 'GY%']];
                    case 'L':
                        return [$where1, [$field . '1' => 'L%', $field . '2' => 'LY%']];
                    case 'N':
                        return [$where1, [$field . '1' => 'N%', $field . '2' => 'NY%']];
                    case 'S':
                        return [$where1, [$field . '1' => 'S%', $field . '2' => 'SZ%']];
                    case 'T':
                        return [$where1, [$field . '1' => 'T%', $field . '2' => 'TY%']];
                    case 'Z':
                        return [$where1, [$field . '1' => 'Z%', $field . '2' => 'ZS%']];
                }
                break;
            case 'nl':
                switch ($letter) {
                    case 'I':
                        return [$where1, [$field . '1' => 'I%', $field . '2' => 'IJ%']];
                }
                break;
        }

        // Easy cases: the MySQL collation rules take care of it
        return [$where3, [$field . '1' => $letter]];
    }

    /**
     * Get a list of initial surname letters for indilist.php and famlist.php
     *
     * @param Tree $tree
     * @param bool $marnm  if set, include married names
     * @param bool $fams   if set, only consider individuals with FAMS records
     * @param bool $totals if set, count the number of names beginning with each letter
     *
     * @return int[]
     */
    private function surnameAlpha(Tree $tree, $marnm, $fams, $totals = true): array
    {
        $alphas = [];

        $sql =
            "SELECT COUNT(n_id)" .
            " FROM `##name` " .
            ($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
            " WHERE n_file=" . $tree->id() .
            ($marnm ? "" : " AND n_type!='_MARNM'");

        // Fetch all the letters in our alphabet, whether or not there
        // are any names beginning with that letter. It looks better to
        // show the full alphabet, rather than omitting rare letters such as X
        foreach ($this->localization_service->alphabet() as $letter) {
            $count = 1;
            if ($totals) {
                list($where, $args2) = $this->getInitialSql('n_surn', $letter);
                $count = Database::prepare($sql . $where)->execute($args2)->fetchOne();
            }
            $alphas[$letter] = (int) $count;
        }

        // Now fetch initial letters that are not in our alphabet,
        // including "@" (for "@N.N.") and "" for no surname.
        $sql =
            "SELECT initial, count FROM (SELECT UPPER(LEFT(n_surn, 1)) AS initial, COUNT(n_id) AS count" .
            " FROM `##name` " .
            ($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
            " WHERE n_file = :tree_id AND n_surn <> ''" .
            ($marnm ? "" : " AND n_type != '_MARNM'");

        $args = [
            'tree_id' => $tree->id(),
        ];

        foreach ($this->localization_service->alphabet() as $n => $letter) {
            $sql .= " AND n_surn COLLATE :collate_" . $n . " NOT LIKE :letter_" . $n;
            $args['collate_' . $n] = I18N::collation();
            $args['letter_' . $n]  = $letter . '%';
        }
        $sql .= " GROUP BY UPPER(LEFT(n_surn, 1))) AS subquery ORDER BY initial = '', initial = '@', initial";
        foreach (Database::prepare($sql)->execute($args)->fetchAssoc() as $alpha => $count) {
            $alphas[$alpha] = (int) $count;
        }

        // Names with no surname
        $sql =
            "SELECT COUNT(n_id)" .
            " FROM `##name` " .
            ($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
            " WHERE n_file = :tree_id AND n_surn = ''" .
            ($marnm ? "" : " AND n_type != '_MARNM'");

        $args = [
            'tree_id' => $tree->id(),
        ];

        $count_no_surname = (int) Database::prepare($sql)->execute($args)->fetchOne();
        if ($count_no_surname !== 0) {
            // Special code to indicate "no surname"
            $alphas[','] = $count_no_surname;
        }

        return $alphas;
    }

    /**
     * Get a list of initial given name letters for indilist.php and famlist.php
     *
     * @param Tree   $tree
     * @param string $surn   if set, only consider people with this surname
     * @param string $salpha if set, only consider surnames starting with this letter
     * @param bool   $marnm  if set, include married names
     * @param bool   $fams   if set, only consider individuals with FAMS records
     *
     * @return int[]
     */
    private function givenAlpha(Tree $tree, $surn, $salpha, $marnm, $fams): array
    {
        $alphas = [];

        $sql =
            "SELECT COUNT(DISTINCT n_id)" .
            " FROM `##name`" .
            ($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
            " WHERE n_file=" . $tree->id() . " " .
            ($marnm ? "" : " AND n_type!='_MARNM'");

        $args = [];

        if ($surn) {
            $sql .= " AND n_surn = :surn COLLATE '" . I18N::collation() . "'";
            $args['surn'] = $surn;
        } elseif ($salpha == ',') {
            $sql .= " AND n_surn=''";
        } elseif ($salpha == '@') {
            $sql .= " AND n_surn='@N.N.'";
        } elseif ($salpha) {
            list($where, $args2) = $this->getInitialSql('n_surn', $salpha);
            $sql .= $where;
            $args += $args2;
        } else {
            // All surnames
            $sql .= " AND n_surn NOT IN ('', '@N.N.')";
        }

        // Fetch all the letters in our alphabet, whether or not there
        // are any names beginning with that letter. It looks better to
        // show the full alphabet, rather than omitting rare letters such as X
        foreach ($this->localization_service->alphabet() as $letter) {
            list($where, $args2) = $this->getInitialSql('n_surn', $salpha);

            $count = Database::prepare($sql . $where)->execute($args + $args2)->fetchOne();

            $alphas[$letter] = (int) $count;
        }

        // Now fetch initial letters that are not in our alphabet,
        // including "@" (for "@N.N.") and "" for no surname
        $sql =
            "SELECT initial, total FROM (SELECT UPPER(LEFT(n_givn, 1)) AS initial, COUNT(DISTINCT n_id) AS total" .
            " FROM `##name` " .
            ($fams ? " JOIN `##link` ON (n_id = l_from AND n_file = l_file AND l_type = 'FAMS') " : "") .
            " WHERE n_file = :tree_id" .
            ($marnm ? "" : " AND n_type != '_MARNM'");

        $args = [
            'tree_id' => $tree->id(),
        ];

        if ($surn) {
            $sql .= " AND n_surn COLLATE :collate_1 = :surn";
            $args['collate_1'] = I18N::collation();
            $args['surn']      = $surn;
        } elseif ($salpha === ',') {
            $sql .= " AND n_surn = ''";
        } elseif ($salpha === '@') {
            $sql .= " AND n_surn = '@N.N.'";
        } elseif ($salpha) {
            list($where, $args2) = $this->getInitialSql('n_surn', $salpha);
            $sql .= $where;
            $args += $args2;
        } else {
            // All surnames
            $sql .= " AND n_surn NOT IN ('', '@N.N.')";
        }

        foreach ($this->localization_service->alphabet() as $letter) {
            $sql .= " AND n_givn NOT LIKE '" . $letter . "%' COLLATE " . I18N::collation();
        }

        $sql .= " GROUP BY UPPER(LEFT(n_givn, 1))) AS subquery ORDER BY initial = '@', initial = '', initial";

        foreach (Database::prepare($sql)->execute($args)->fetchAssoc() as $alpha => $count) {
            $alphas[$alpha] = (int) $count;
        }

        return $alphas;
    }

    /**
     * Get a count of actual surnames and variants, based on a "root" surname.
     *
     * @param Tree   $tree
     * @param string $surn   if set, only count people with this surname
     * @param string $salpha if set, only consider surnames starting with this letter
     * @param bool   $marnm  if set, include married names
     * @param bool   $fams   if set, only consider individuals with FAMS records
     *
     * @return int[][]
     */
    private function surnames(Tree $tree, $surn, $salpha, $marnm, $fams): array
    {
        $sql =
            "SELECT UPPER(n_surn COLLATE :collate) AS n_surn, n_surname COLLATE utf8_bin AS n_surname, COUNT(*) AS total" .
            " FROM `##name` " .
            ($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
            " WHERE n_file = :tree_id" .
            ($marnm ? "" : " AND n_type != '_MARNM'");

        $args = [
            'tree_id' => $tree->id(),
            'collate' => I18N::collation(),
        ];

        if ($surn) {
            $sql .= " AND n_surn COLLATE :collate_1 = :surn";
            $args['collate_1'] = I18N::collation();
            $args['surn']      = $surn;
        } elseif ($salpha === ',') {
            $sql .= " AND n_surn = ''";
        } elseif ($salpha === '@') {
            $sql .= " AND n_surn = '@N.N.'";
        } elseif ($salpha) {
            list($where, $args2) = $this->getInitialSql('n_surn', $salpha);

            $sql .= $where;
            $args += $args2;
        } else {
            // All surnames
            $sql .= " AND n_surn NOT IN ('', '@N.N.')";
        }
        $sql .= " GROUP BY 1,2";

        $list = [];

        foreach (Database::prepare($sql)->execute($args)->fetchAll() as $row) {
            $list[$row->n_surn][$row->n_surname] = (int) $row->total;
        }

        return $list;
    }

    /**
     * Fetch a list of individuals with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param Tree   $tree
     * @param string $surn   if set, only fetch people with this surname
     * @param string $salpha if set, only fetch surnames starting with this letter
     * @param string $galpha if set, only fetch given names starting with this letter
     * @param bool   $marnm  if set, include married names
     * @param bool   $fams   if set, only fetch individuals with FAMS records
     *
     * @return Individual[]
     */
    private function individuals(Tree $tree, $surn, $salpha, $galpha, $marnm, $fams): array
    {
        $sql =
            "SELECT i_id AS xref, i_gedcom AS gedcom, n_full " .
            "FROM `##individuals` " .
            "JOIN `##name` ON n_id = i_id AND n_file = i_file " .
            ($fams ? "JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
            "WHERE n_file = :tree_id " .
            ($marnm ? "" : "AND n_type != '_MARNM'");

        $args = [
            'tree_id' => $tree->id(),
        ];

        if ($surn) {
            $sql .= " AND n_surn COLLATE :collate_1 = :surn";
            $args['collate_1'] = I18N::collation();
            $args['surn']      = $surn;
        } elseif ($salpha === ',') {
            $sql .= " AND n_surn = ''";
        } elseif ($salpha === '@') {
            $sql .= " AND n_surn = '@N.N.'";
        } elseif ($salpha) {
            list($where, $args2) = $this->getInitialSql('n_surn', $salpha);
            $sql .= $where;
            $args += $args2;
        } else {
            // All surnames
            $sql .= " AND n_surn NOT IN ('', '@N.N.')";
        }
        if ($galpha) {
            list($where, $args2) = $this->getInitialSql('n_givn', $galpha);
            $sql .= $where;
            $args += $args2;
        }

        $sql .= " ORDER BY CASE n_surn WHEN '@N.N.' THEN 1 ELSE 0 END, n_surn COLLATE :collate_2, CASE n_givn WHEN '@P.N.' THEN 1 ELSE 0 END, n_givn COLLATE :collate_3";
        $args['collate_2'] = I18N::collation();
        $args['collate_3'] = I18N::collation();

        $list = [];
        $rows = Database::prepare($sql)->execute($args)->fetchAll();
        foreach ($rows as $row) {
            $person = Individual::getInstance($row->xref, $tree, $row->gedcom);
            // The name from the database may be private - check the filtered list...
            foreach ($person->getAllNames() as $n => $name) {
                if ($name['fullNN'] == $row->n_full) {
                    $person->setPrimaryName($n);
                    // We need to clone $person, as we may have multiple references to the
                    // same person in this list, and the "primary name" would otherwise
                    // be shared amongst all of them.
                    $list[] = clone $person;
                    break;
                }
            }
        }

        return $list;
    }

    /**
     * Fetch a list of families with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param Tree   $tree
     * @param string $surn   if set, only fetch people with this surname
     * @param string $salpha if set, only fetch surnames starting with this letter
     * @param string $galpha if set, only fetch given names starting with this letter
     * @param bool   $marnm  if set, include married names
     *
     * @return Family[]
     */
    private function families(Tree $tree, $surn, $salpha, $galpha, $marnm): array
    {
        $list = [];
        foreach ($this->individuals($tree, $surn, $salpha, $galpha, $marnm, true) as $indi) {
            foreach ($indi->getSpouseFamilies() as $family) {
                $list[$family->xref()] = $family;
            }
        }
        usort($list, '\Fisharebest\Webtrees\GedcomRecord::compare');

        return $list;
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
