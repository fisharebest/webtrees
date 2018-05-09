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
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for lists of GEDCOM records.
 */
class ListController extends AbstractBaseController {
	/**
	 * Show a list of all individual or family records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function familyList(Request $request): Response {
		return $this->individualList($request);
	}

	/**
	 * Show a list of all individual or family records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function individualList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		// This action can show lists of both families and individuals.
		$route    = $request->get('route');
		$families = $route === 'family-list';

		ob_start();

		// We show three different lists: initials, surnames and individuals

		// All surnames beginning with this letter where "@"=unknown and ","=none
		$alpha    = $request->get('alpha', '');

		// All individuals with this surname
		$surname  = $request->get('surname', '');

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
					'ged'      => $tree->getName(),
					'show_all' => 'yes',
				];
				$show    = 'indi';
			} elseif ($falpha) {
				$alpha   = '';
				$surname = '';
				$legend  = I18N::translate('All') . ', ' . e($falpha) . '…';
				$params  = [
					'ged'      => $tree->getName(),
					'show_all' => 'yes',
				];
				$show    = 'indi';
			} else {
				$alpha   = '';
				$surname = '';
				$legend  = I18N::translate('All');
				$params  = [
					'ged'      => $tree->getName(),
					'show_all' => 'yes',
				];
				$show    = $request->get('show', 'surn');
			}
		} elseif ($surname) {
			$alpha    = $this->initialLetter($surname); // so we can highlight the initial letter
			$show_all = 'no';
			if ($surname === '@N.N.') {
				$legend = I18N::translateContext('Unknown surname', '…');
			} else {
				// The surname parameter is a root/canonical form.
				// Display it as the actual surname
				$legend = implode('/', array_keys($this->surnames($surname, $alpha, $show_marnm === 'yes', $families)));
			}
			$params = [
				'ged'     => $tree->getName(),
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
				'ged'   => $tree->getName(),
			];
			$show     = 'indi'; // SURN list makes no sense here
		} elseif ($alpha === ',') {
			$show_all = 'no';
			$legend   = I18N::translate('None');
			$params   = [
				'alpha' => $alpha,
				'ged'   => $tree->getName(),
			];
			$show     = 'indi'; // SURN list makes no sense here
		} elseif ($alpha) {
			$show_all = 'no';
			$legend   = e($alpha) . '…';
			$params   = [
				'alpha' => $alpha,
				'ged'   => $tree->getName(),
			];
			$show     = $request->get('show', 'surn');
		} else {
			$show_all = 'no';
			$legend   = '…';
			$params   = [
				'ged' => $tree->getName(),
			];
			$show     = 'none'; // Don't show lists until something is chosen
		}
		$legend = '<span dir="auto">' . $legend . '</span>';

		if ($families) {
			$title = I18N::translate('Families') . ' — ' . $legend;
		} else {
			$title = I18N::translate('Individuals') . ' — ' . $legend;
		}

		?>
		<div class="d-flex flex-column wt-page-options wt-page-options-individual-list d-print-none">
			<ul class="d-flex flex-wrap wt-initials-list">

				<?php
				foreach ($this->surnameAlpha($show_marnm === 'yes', $families) as $letter => $count) {
					echo '<li class="wt-initials-list-item">';
					if ($count > 0) {
						echo '<a href="' . e(route($route, [
								'alpha' => $letter,
								'ged'   => $tree->getName(),
							])) . '" class="wt-initial' . ($letter === $alpha ? ' active' : '') . '" title="' . I18N::number($count) . '">' . $this->surnameInitial((string) $letter) . '</a>';
					} else {
						echo '<span class="wt-initial text-muted">' . $this->surnameInitial((string) $letter) . '</span>';
					}
					echo '</li>';
				}

				// Search spiders don't get the "show all" option as the other links give them everything.
				if (Session::has('initiated')) {
					echo '<li class="wt-initials-list-item">';
					echo '<a class="wt-initial' . ($show_all === 'yes' ? ' active' : '') . '" href="' . e(route($route, ['show_all' => 'yes'] + $params)) . '">';
					echo I18N::translate('All');
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';

				// Search spiders don't get an option to show/hide the surname sublists,
				// nor does it make sense on the all/unknown/surname views
				if (Session::has('initiated') && $show !== 'none') {
					if ($show_marnm === 'yes') {
						echo '<p><a href="', e(route($route, [
								'show'       => $show,
								'show_marnm' => 'no',
							] + $params)), '">', I18N::translate('Exclude individuals with “%s” as a married name', $legend), '</a></p>';
					} else {
						echo '<p><a href="', e(route($route, [
								'show'       => $show,
								'show_marnm' => 'yes',
							] + $params)), '">', I18N::translate('Include individuals with “%s” as a married name', $legend), '</a></p>';
					}

					if ($alpha !== '@' && $alpha !== ',' && !$surname) {
						if ($show === 'surn') {
							echo '<p><a href="', e(route($route, [
									'show'       => 'indi',
									'show_marnm' => 'no',
								] + $params)), '">', I18N::translate('Show the list of individuals'), '</a></p>';
						} else {
							echo '<p><a href="', e(route($route, [
									'show'       => 'surn',
									'show_marnm' => 'no',
								] + $params)), '">', I18N::translate('Show the list of surnames'), '</a></p>';
						}
					}
				}

				?>
		</div>
		<div class="wt-page-content">
			<?php

			if ($show === 'indi' || $show === 'surn') {
				$surns = $this->surnames($surname, $alpha, $show_marnm === 'yes', $families);
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
							echo view('tables/surnames', [
								'surnames' => $surns,
								'route'    => $route,
							]);
							break;
					}
				} else {
					// Show the list
					$count = 0;
					foreach ($surns as $surnames) {
						foreach ($surnames as $list) {
							$count += count($list);
						}
					}
					// Don't sublists short lists.
					if ($count < $tree->getPreference('SUBLIST_TRIGGER_I')) {
						$falpha              = '';
					} else {
						$givn_initials = $this->givenAlpha($surname, $alpha, $show_marnm === 'yes', $families);
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
								echo '<li class="wt-initials-list-item">';
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
								echo '<li class="wt-initials-list-item">';
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
							echo FunctionsPrintLists::individualTable($this->individuals($surname, $alpha, $falpha, $show_marnm === 'yes', false));
						} else {
							echo FunctionsPrintLists::familyTable($this->families($surname, $alpha, $falpha, $show_marnm === 'yes'));
						}
					}
				}
			}
			?>
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
	 *
	 * @return Response
	 */
	public function mediaList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$formats = GedcomTag::getFileFormTypes();

		$action    = $request->get('action');
		$page      = (int) $request->get('page');
		$max       = (int) $request->get('max', 20);
		$folder    = $request->get('folder', '');
		$filter    = $request->get('filter', '');
		$subdirs   = $request->get('subdirs', '1');
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
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function noteList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$notes = $this->allNotes($tree);

		return $this->viewResponse('note-list-page', [
			'notes' => $notes,
			'title' => I18N::translate('Shared notes'),
		]);
	}

	/**
	 * Show a list of all repository records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function repositoryList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$repositories = $this->allrepositories($tree);

		return $this->viewResponse('repository-list-page', [
			'repositories' => $repositories,
			'title'        => I18N::translate('Repositories'),
		]);
	}

	/**
	 * Show a list of all source records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function sourceList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

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
	private function allFolders(Tree $tree) {
		$folders = Database::prepare(
			"SELECT SQL_CACHE LEFT(multimedia_file_refn, CHAR_LENGTH(multimedia_file_refn) - CHAR_LENGTH(SUBSTRING_INDEX(multimedia_file_refn, '/', -1))) AS media_path" .
			" FROM  `##media_file`" .
			" WHERE m_file = ?" .
			" AND   multimedia_file_refn NOT LIKE 'http://%'" .
			" AND   multimedia_file_refn NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute([
			$tree->getTreeId(),
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
	private function allMedia(Tree $tree, string $folder, string $subfolders, string $sort, string $filter, string $form_type): array {
		// All files in the folder, plus external files
		$sql  =
			"SELECT m_id AS xref, m_gedcom AS gedcom" .
			" FROM `##media`" .
			" JOIN `##media_file` USING (m_id, m_file)" .
			" WHERE m_file = ?";
		$args = [
			$tree->getTreeId(),
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
				$sql    .= " AND (multimedia_file_refn LIKE CONCAT(?, '%') $sql_external)";
				$args[] = Database::escapeLike($folder);
				break;
			case 'exclude':
				$sql    .= " AND (multimedia_file_refn LIKE CONCAT(?, '%') AND multimedia_file_refn NOT LIKE CONCAT(?, '%/%') $sql_external)";
				$args[] = Database::escapeLike($folder);
				$args[] = Database::escapeLike($folder);
				break;
		}

		// Apply search terms
		if ($filter) {
			$sql    .= " AND (SUBSTRING_INDEX(multimedia_file_refn, '/', -1) LIKE CONCAT('%', ?, '%') OR descriptive_title LIKE CONCAT('%', ?, '%'))";
			$args[] = Database::escapeLike($filter);
			$args[] = Database::escapeLike($filter);
		}

		if ($form_type) {
			$sql    .= " AND source_media_type = ?";
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
	private function allNotes(Tree $tree): array {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'NOTE' AND o_file = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$list = [];
		foreach ($rows as $row) {
			$list[] = Note::getInstance($row->xref, $tree, $row->gedcom);
		}

		return array_filter($list, function (Note $x) {
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
	private function allRepositories(Tree $tree): array {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'REPO' AND o_file = ?"
		)->execute([
			$tree->getTreeId(),
		])->fetchAll();

		$list = [];
		foreach ($rows as $row) {
			$list[] = Repository::getInstance($row->xref, $tree, $row->gedcom);
		}

		return array_filter($list, function (Repository $x) {
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
	private function allSources(Tree $tree): array {
		$rows = Database::prepare(
			"SELECT s_id AS xref, s_gedcom AS gedcom FROM `##sources` WHERE s_file = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$list = [];
		foreach ($rows as $row) {
			$list[] = Source::getInstance($row->xref, $tree, $row->gedcom);
		}

		return array_filter($list, function (Source $x) {
			return $x->canShow();
		});
	}

	/**
	 * Get a list of initial letters, for lists of names.
	 *
	 * @param string $locale Return the alphabet for this locale
	 *
	 * @return string[]
	 */
	private function getAlphabetForLocale($locale) {
		switch ($locale) {
			case 'ar':
				return [
					'ا',
					'ب',
					'ت',
					'ث',
					'ج',
					'ح',
					'خ',
					'د',
					'ذ',
					'ر',
					'ز',
					'س',
					'ش',
					'ص',
					'ض',
					'ط',
					'ظ',
					'ع',
					'غ',
					'ف',
					'ق',
					'ك',
					'ل',
					'م',
					'ن',
					'ه',
					'و',
					'ي',
					'آ',
					'ة',
					'ى',
					'ی',
				];
			case 'cs':
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'CH',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
				];
			case 'da':
			case 'nb':
			case 'nn':
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'Æ',
					'Ø',
					'Å',
				];
			case 'el':
				return [
					'Α',
					'Β',
					'Γ',
					'Δ',
					'Ε',
					'Ζ',
					'Η',
					'Θ',
					'Ι',
					'Κ',
					'Λ',
					'Μ',
					'Ν',
					'Ξ',
					'Ο',
					'Π',
					'Ρ',
					'Σ',
					'Τ',
					'Υ',
					'Φ',
					'Χ',
					'Ψ',
					'Ω',
				];
			case 'es':
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'Ñ',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
				];
			case 'et':
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'Š',
					'Z',
					'Ž',
					'T',
					'U',
					'V',
					'W',
					'Õ',
					'Ä',
					'Ö',
					'Ü',
					'X',
					'Y',
				];
			case 'fi':
			case 'sv':
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'Å',
					'Ä',
					'Ö',
				];
			case 'he':
				return [
					'א',
					'ב',
					'ג',
					'ד',
					'ה',
					'ו',
					'ז',
					'ח',
					'ט',
					'י',
					'כ',
					'ל',
					'מ',
					'נ',
					'ס',
					'ע',
					'פ',
					'צ',
					'ק',
					'ר',
					'ש',
					'ת',
				];
			case 'hu':
				return [
					'A',
					'B',
					'C',
					'CS',
					'D',
					'DZ',
					'DZS',
					'E',
					'F',
					'G',
					'GY',
					'H',
					'I',
					'J',
					'K',
					'L',
					'LY',
					'M',
					'N',
					'NY',
					'O',
					'Ö',
					'P',
					'Q',
					'R',
					'S',
					'SZ',
					'T',
					'TY',
					'U',
					'Ü',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'ZS',
				];
			case 'lt':
				return [
					'A',
					'Ą',
					'B',
					'C',
					'Č',
					'D',
					'E',
					'Ę',
					'Ė',
					'F',
					'G',
					'H',
					'I',
					'Y',
					'Į',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'R',
					'S',
					'Š',
					'T',
					'U',
					'Ų',
					'Ū',
					'V',
					'Z',
					'Ž',
				];
			case 'nl':
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'IJ',
				];
			case 'pl':
				return [
					'A',
					'B',
					'C',
					'Ć',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'Ł',
					'M',
					'N',
					'O',
					'Ó',
					'P',
					'Q',
					'R',
					'S',
					'Ś',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'Ź',
					'Ż',
				];
			case 'ro':
				return [
					'A',
					'Ă',
					'Â',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'Î',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'Ş',
					'T',
					'Ţ',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
				];
			case 'ru':
				return [
					'А',
					'Б',
					'В',
					'Г',
					'Д',
					'Е',
					'Ё',
					'Ж',
					'З',
					'И',
					'Й',
					'К',
					'Л',
					'М',
					'Н',
					'О',
					'П',
					'Р',
					'С',
					'Т',
					'У',
					'Ф',
					'Х',
					'Ц',
					'Ч',
					'Ш',
					'Щ',
					'Ъ',
					'Ы',
					'Ь',
					'Э',
					'Ю',
					'Я',
				];
			case 'sk':
				return [
					'A',
					'Á',
					'Ä',
					'B',
					'C',
					'Č',
					'D',
					'Ď',
					'E',
					'É',
					'F',
					'G',
					'H',
					'I',
					'Í',
					'J',
					'K',
					'L',
					'Ľ',
					'Ĺ',
					'M',
					'N',
					'Ň',
					'O',
					'Ó',
					'Ô',
					'P',
					'Q',
					'R',
					'Ŕ',
					'S',
					'Š',
					'T',
					'Ť',
					'U',
					'Ú',
					'V',
					'W',
					'X',
					'Y',
					'Ý',
					'Z',
					'Ž',
				];
			case 'sl':
				return [
					'A',
					'B',
					'C',
					'Č',
					'Ć',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'Š',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'Ž',
				];
			case 'sr':
				return [
					'A',
					'B',
					'C',
					'Č',
					'Ć',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'Š',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
					'Ž',
				];
			case 'tr':
				return [
					'A',
					'B',
					'C',
					'Ç',
					'D',
					'E',
					'F',
					'G',
					'Ğ',
					'H',
					'I',
					'İ',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'Ö',
					'P',
					'R',
					'S',
					'Ş',
					'T',
					'U',
					'Ü',
					'V',
					'Y',
					'Z',
				];
			default:
				return [
					'A',
					'B',
					'C',
					'D',
					'E',
					'F',
					'G',
					'H',
					'I',
					'J',
					'K',
					'L',
					'M',
					'N',
					'O',
					'P',
					'Q',
					'R',
					'S',
					'T',
					'U',
					'V',
					'W',
					'X',
					'Y',
					'Z',
				];
		}
	}

	/**
	 * Get the initial letter of a name, taking care of multi-letter sequences and equivalences.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function initialLetter($name) {
		$name = I18N::strtoupper($name);
		switch (WT_LOCALE) {
			case 'cs':
				if (substr($name, 0, 2) == 'CH') {
					return 'CH';
				}
				break;
			case 'da':
			case 'nb':
			case 'nn':
				if (substr($name, 0, 2) == 'AA') {
					return 'Å';
				}
				break;
			case 'hu':
				if (substr($name, 0, 2) == 'CS') {
					return 'CS';
				} elseif (substr($name, 0, 3) == 'DZS') {
					return 'DZS';
				} elseif (substr($name, 0, 2) == 'DZ') {
					return 'DZ';
				} elseif (substr($name, 0, 2) == 'GY') {
					return 'GY';
				} elseif (substr($name, 0, 2) == 'LY') {
					return 'LY';
				} elseif (substr($name, 0, 2) == 'NY') {
					return 'NY';
				} elseif (substr($name, 0, 2) == 'SZ') {
					return 'SZ';
				} elseif (substr($name, 0, 2) == 'TY') {
					return 'TY';
				} elseif (substr($name, 0, 2) == 'ZS') {
					return 'ZS';
				}
				break;
			case 'nl':
				if (substr($name, 0, 2) == 'IJ') {
					return 'IJ';
				}
				break;
		}

		// No special rules - just take the first character
		return mb_substr($name, 0, 1);
	}

	/**
	 * Generate SQL to match a given letter, taking care of cases that
	 * are not covered by the collation setting.
	 *
	 * We must consider:
	 * potential substrings, such as Czech "CH" and "C"
	 * equivalent letters, such as Danish "AA" and "Å"
	 *
	 * We COULD write something that handles all languages generically,
	 * but its performance would most likely be poor.
	 *
	 * For languages that don't appear in this list, we could write
	 * simpler versions of the surnameAlpha() and givenAlpha() functions,
	 * but it gives no noticable improvement in performance.
	 *
	 * @param string $field
	 * @param string $letter
	 *
	 * @return string
	 */
	private function getInitialSql($field, $letter) {
		switch (WT_LOCALE) {
			case 'cs':
				switch ($letter) {
					case 'C':
						return $field . " LIKE 'C%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'CH%' COLLATE " . I18N::collation();
				}
				break;
			case 'da':
			case 'nb':
			case 'nn':
				switch ($letter) {
					// AA gets listed under Å
					case 'A':
						return $field . " LIKE 'A%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'AA%' COLLATE " . I18N::collation();
					case 'Å':
						return "(" . $field . " LIKE 'Å%' COLLATE " . I18N::collation() . " OR " . $field . " LIKE 'AA%' COLLATE " . I18N::collation() . ")";
				}
				break;
			case 'hu':
				switch ($letter) {
					case 'C':
						return $field . " LIKE 'C%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'CS%' COLLATE " . I18N::collation();
					case 'D':
						return $field . " LIKE 'D%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'DZ%' COLLATE " . I18N::collation();
					case 'DZ':
						return $field . " LIKE 'DZ%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'DZS%' COLLATE " . I18N::collation();
					case 'G':
						return $field . " LIKE 'G%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'GY%' COLLATE " . I18N::collation();
					case 'L':
						return $field . " LIKE 'L%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'LY%' COLLATE " . I18N::collation();
					case 'N':
						return $field . " LIKE 'N%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'NY%' COLLATE " . I18N::collation();
					case 'S':
						return $field . " LIKE 'S%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'SZ%' COLLATE " . I18N::collation();
					case 'T':
						return $field . " LIKE 'T%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'TY%' COLLATE " . I18N::collation();
					case 'Z':
						return $field . " LIKE 'Z%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'ZS%' COLLATE " . I18N::collation();
				}
				break;
			case 'nl':
				switch ($letter) {
					case 'I':
						return $field . " LIKE 'I%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'IJ%' COLLATE " . I18N::collation();
				}
				break;
		}

		// Easy cases: the MySQL collation rules take care of it
		return "$field LIKE CONCAT('@'," . Database::quote($letter) . ",'%') COLLATE " . I18N::collation() . " ESCAPE '@'";
	}

	/**
	 * Get a list of initial surname letters for indilist.php and famlist.php
	 *
	 * @param bool $marnm  if set, include married names
	 * @param bool $fams   if set, only consider individuals with FAMS records
	 * @param bool $totals if set, count the number of names beginning with each letter
	 *
	 * @return int[]
	 */
	public function surnameAlpha($marnm, $fams, $totals = true) {
		$alphas = [];

		$sql =
			"SELECT SQL_CACHE COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file=" . $this->tree()->getTreeId() .
			($marnm ? "" : " AND n_type!='_MARNM'");

		// Fetch all the letters in our alphabet, whether or not there
		// are any names beginning with that letter. It looks better to
		// show the full alphabet, rather than omitting rare letters such as X
		foreach ($this->getAlphabetForLocale(WT_LOCALE) as $letter) {
			$count = 1;
			if ($totals) {
				$count = Database::prepare($sql . " AND " . $this->getInitialSql('n_surn', $letter))->fetchOne();
			}
			$alphas[$letter] = $count;
		}

		// Now fetch initial letters that are not in our alphabet,
		// including "@" (for "@N.N.") and "" for no surname.
		$sql =
			"SELECT SQL_CACHE initial, count FROM (SELECT UPPER(LEFT(n_surn, 1)) AS initial, COUNT(n_id) AS count" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			" WHERE n_file = :tree_id AND n_surn <> ''" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = [
			'tree_id' => $this->tree()->getTreeId(),
		];

		foreach ($this->getAlphabetForLocale(WT_LOCALE) as $n => $letter) {
			$sql                   .= " AND n_surn COLLATE :collate_" . $n . " NOT LIKE :letter_" . $n;
			$args['collate_' . $n] = I18N::collation();
			$args['letter_' . $n]  = $letter . '%';
		}
		$sql .= " GROUP BY UPPER(LEFT(n_surn, 1))) AS subquery ORDER BY initial = '', initial = '@', initial";
		foreach (Database::prepare($sql)->execute($args)->fetchAssoc() as $alpha => $count) {
			$alphas[$alpha] = $count;
		}

		// Names with no surname
		$sql =
			"SELECT SQL_CACHE COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			" WHERE n_file = :tree_id AND n_surn = ''" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = [
			'tree_id' => $this->tree()->getTreeId(),
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
	 * @param string $surn   if set, only consider people with this surname
	 * @param string $salpha if set, only consider surnames starting with this letter
	 * @param bool   $marnm  if set, include married names
	 * @param bool   $fams   if set, only consider individuals with FAMS records
	 *
	 * @return int[]
	 */
	public function givenAlpha($surn, $salpha, $marnm, $fams) {
		$alphas = [];

		$sql =
			"SELECT SQL_CACHE COUNT(DISTINCT n_id)" .
			" FROM `##name`" .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file=" . $this->tree()->getTreeId() . " " .
			($marnm ? "" : " AND n_type!='_MARNM'");

		if ($surn) {
			$sql .= " AND n_surn=" . Database::quote($surn) . " COLLATE '" . I18N::collation() . "'";
		} elseif ($salpha == ',') {
			$sql .= " AND n_surn=''";
		} elseif ($salpha == '@') {
			$sql .= " AND n_surn='@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . $this->getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}

		// Fetch all the letters in our alphabet, whether or not there
		// are any names beginning with that letter. It looks better to
		// show the full alphabet, rather than omitting rare letters such as X
		foreach ($this->getAlphabetForLocale(WT_LOCALE) as $letter) {
			$count           = Database::prepare($sql . " AND " . $this->getInitialSql('n_givn', $letter))->fetchOne();
			$alphas[$letter] = $count;
		}

		// Now fetch initial letters that are not in our alphabet,
		// including "@" (for "@N.N.") and "" for no surname
		$sql =
			"SELECT SQL_CACHE initial, total FROM (SELECT UPPER(LEFT(n_givn, 1)) AS initial, COUNT(DISTINCT n_id) AS total" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id = l_from AND n_file = l_file AND l_type = 'FAMS') " : "") .
			" WHERE n_file = :tree_id" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = [
			'tree_id' => $this->tree()->getTreeId(),
		];

		if ($surn) {
			$sql               .= " AND n_surn COLLATE :collate_1 = :surn";
			$args['collate_1'] = I18N::collation();
			$args['surn']      = $surn;
		} elseif ($salpha === ',') {
			$sql .= " AND n_surn = ''";
		} elseif ($salpha === '@') {
			$sql .= " AND n_surn = '@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . $this->getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}

		foreach ($this->getAlphabetForLocale(WT_LOCALE) as $letter) {
			$sql .= " AND n_givn NOT LIKE '" . $letter . "%' COLLATE " . I18N::collation();
		}
		$sql .= " GROUP BY UPPER(LEFT(n_givn, 1))) AS subquery ORDER BY initial = '@', initial = '', initial";

		foreach (Database::prepare($sql)->execute($args)->fetchAssoc() as $alpha => $count) {
			$alphas[$alpha] = $count;
		}

		return $alphas;
	}

	/**
	 * Get a list of actual surnames and variants, based on a "root" surname.
	 *
	 * @param string $surn   if set, only fetch people with this surname
	 * @param string $salpha if set, only consider surnames starting with this letter
	 * @param bool   $marnm  if set, include married names
	 * @param bool   $fams   if set, only consider individuals with FAMS records
	 *
	 * @return array
	 */
	public function surnames($surn, $salpha, $marnm, $fams) {
		$sql =
			"SELECT SQL_CACHE n2.n_surn, n1.n_surname, n1.n_id" .
			" FROM `##name` n1 " .
			($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			" JOIN (SELECT n_surn COLLATE :collate_0 AS n_surn, n_file FROM `##name`" .
			" WHERE n_file = :tree_id" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = [
			'tree_id'   => $this->tree()->getTreeId(),
			'collate_0' => I18N::collation(),
		];

		if ($surn) {
			$sql               .= " AND n_surn COLLATE :collate_1 = :surn";
			$args['collate_1'] = I18N::collation();
			$args['surn']      = $surn;
		} elseif ($salpha === ',') {
			$sql .= " AND n_surn = ''";
		} elseif ($salpha === '@') {
			$sql .= " AND n_surn = '@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . $this->getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}
		$sql               .= " GROUP BY n_surn COLLATE :collate_2, n_file) AS n2 ON (n1.n_surn = n2.n_surn COLLATE :collate_3 AND n1.n_file = n2.n_file)";
		$args['collate_2'] = I18N::collation();
		$args['collate_3'] = I18N::collation();

		$list = [];
		foreach (Database::prepare($sql)->execute($args)->fetchAll() as $row) {
			$list[I18N::strtoupper($row->n_surn)][$row->n_surname][$row->n_id] = true;
		}

		return $list;
	}

	/**
	 * Fetch a list of individuals with specified names
	 *
	 * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
	 * To search for names with no surnames, use $salpha=","
	 *
	 * @param string $surn   if set, only fetch people with this surname
	 * @param string $salpha if set, only fetch surnames starting with this letter
	 * @param string $galpha if set, only fetch given names starting with this letter
	 * @param bool   $marnm  if set, include married names
	 * @param bool   $fams   if set, only fetch individuals with FAMS records
	 *
	 * @return Individual[]
	 */
	public function individuals($surn, $salpha, $galpha, $marnm, $fams) {
		$sql =
			"SELECT i_id AS xref, i_gedcom AS gedcom, n_full " .
			"FROM `##individuals` " .
			"JOIN `##name` ON n_id = i_id AND n_file = i_file " .
			($fams ? "JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			"WHERE n_file = :tree_id " .
			($marnm ? "" : "AND n_type != '_MARNM'");

		$args = [
			'tree_id' => $this->tree()->getTreeId(),
		];

		if ($surn) {
			$sql               .= " AND n_surn COLLATE :collate_1 = :surn";
			$args['collate_1'] = I18N::collation();
			$args['surn']      = $surn;
		} elseif ($salpha === ',') {
			$sql .= " AND n_surn = ''";
		} elseif ($salpha === '@') {
			$sql .= " AND n_surn = '@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . $this->getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}
		if ($galpha) {
			$sql .= " AND " . $this->getInitialSql('n_givn', $galpha);
		}

		$sql               .= " ORDER BY CASE n_surn WHEN '@N.N.' THEN 1 ELSE 0 END, n_surn COLLATE :collate_2, CASE n_givn WHEN '@P.N.' THEN 1 ELSE 0 END, n_givn COLLATE :collate_3";
		$args['collate_2'] = I18N::collation();
		$args['collate_3'] = I18N::collation();

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$person = Individual::getInstance($row->xref, $this->tree(), $row->gedcom);
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
	 *
	 * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
	 * To search for names with no surnames, use $salpha=","
	 *
	 * @param string $surn   if set, only fetch people with this surname
	 * @param string $salpha if set, only fetch surnames starting with this letter
	 * @param string $galpha if set, only fetch given names starting with this letter
	 * @param bool   $marnm  if set, include married names
	 *
	 * @return Family[]
	 */
	public function families($surn, $salpha, $galpha, $marnm) {
		$list = [];
		foreach ($this->individuals($surn, $salpha, $galpha, $marnm, true) as $indi) {
			foreach ($indi->getSpouseFamilies() as $family) {
				$list[$family->getXref()] = $family;
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
	public function givenNameInitial(string $initial): string {
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
	public function surnameInitial(string $initial): string {
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
