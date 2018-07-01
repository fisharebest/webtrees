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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Datatables;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Ramsey\Uuid\Uuid;

/**
 * Class FunctionsPrintLists - create sortable lists using datatables.net
 */
class FunctionsPrintLists {
	/**
	 * Print a table of sources
	 *
	 * @param Source[] $sources
	 *
	 * @return string
	 */
	public static function sourceTable($sources) {
		// Count the number of linked records. These numbers include private records.
		// It is not good to bypass privacy, but many servers do not have the resources
		// to process privacy for every record in the tree
		$count_individuals = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##individuals` JOIN `##link` ON l_from = i_id AND l_file = i_file AND l_type = 'SOUR' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$count_families = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##families` JOIN `##link` ON l_from = f_id AND l_file = f_file AND l_type = 'SOUR' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$count_media = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##media` JOIN `##link` ON l_from = m_id AND l_file = m_file AND l_type = 'SOUR' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$count_notes = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##other` JOIN `##link` ON l_from = o_id AND l_file = o_file AND o_type = 'NOTE' AND l_type = 'SOUR' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$html = '';
		$html .= '<table ' . Datatables::sourceTableAttributes() . '><thead><tr>';
		$html .= '<th>' . I18N::translate('Title') . '</th>';
		$html .= '<th>' . I18N::translate('Author') . '</th>';
		$html .= '<th>' . I18N::translate('Individuals') . '</th>';
		$html .= '<th>' . I18N::translate('Families') . '</th>';
		$html .= '<th>' . I18N::translate('Media objects') . '</th>';
		$html .= '<th>' . I18N::translate('Shared notes') . '</th>';
		$html .= '<th>' . I18N::translate('Last change') . '</th>';
		$html .= '</tr></thead>';
		$html .= '<tbody>';

		foreach ($sources as $source) {
			if (!$source->canShow()) {
				continue;
			}
			if ($source->isPendingDeletion()) {
				$class = ' class="old"';
			} elseif ($source->isPendingAddition()) {
				$class = ' class="new"';
			} else {
				$class = '';
			}
			$html .= '<tr' . $class . '>';
			// Source name(s)
			$html .= '<td data-sort="' . e($source->getSortName()) . '">';
			foreach ($source->getAllNames() as $n => $name) {
				if ($n) {
					$html .= '<br>';
				}
				if ($n == $source->getPrimaryName()) {
					$html .= '<a class="name2" href="' . e($source->url()) . '">' . $name['full'] . '</a>';
				} else {
					$html .= '<a href="' . e($source->url()) . '">' . $name['full'] . '</a>';
				}
			}
			$html .= '</td>';
			// Author
			$auth = $source->getFirstFact('AUTH');
			if ($auth) {
				$author = $auth->getValue();
			} else {
				$author = '';
			}
			$html .= '<td data-sort="' . e($author) . '">' . $author . '</td>';
			$key = $source->getXref() . '@' . $source->getTree()->getTreeId();
			// Count of linked individuals
			$num = array_key_exists($key, $count_individuals) ? $count_individuals[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Count of linked families
			$num = array_key_exists($key, $count_families) ? $count_families[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Count of linked media objects
			$num = array_key_exists($key, $count_media) ? $count_media[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Count of linked notes
			$num = array_key_exists($key, $count_notes) ? $count_notes[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Last change
			$html .= '<td data-sort="' . $source->lastChangeTimestamp(true) . '">' . $source->lastChangeTimestamp() . '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';

		return $html;
	}

	/**
	 * Print a table of shared notes
	 *
	 * @param Note[] $notes
	 *
	 * @return string
	 */
	public static function noteTable($notes) {
		// Count the number of linked records. These numbers include private records.
		// It is not good to bypass privacy, but many servers do not have the resources
		// to process privacy for every record in the tree
		$count_individuals = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##individuals` JOIN `##link` ON l_from = i_id AND l_file = i_file AND l_type = 'NOTE' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$count_families = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##families` JOIN `##link` ON l_from = f_id AND l_file = f_file AND l_type = 'NOTE' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$count_media = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##media` JOIN `##link` ON l_from = m_id AND l_file = m_file AND l_type = 'NOTE' GROUP BY l_to, l_file"
		)->fetchAssoc();
		$count_sources = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##sources` JOIN `##link` ON l_from = s_id AND l_file = s_file AND l_type = 'NOTE' GROUP BY l_to, l_file"
		)->fetchAssoc();

		$html = '';
		$html .= '<table ' . Datatables::noteTableAttributes() . '><thead><tr>';
		$html .= '<th>' . I18N::translate('Title') . '</th>';
		$html .= '<th>' . I18N::translate('Individuals') . '</th>';
		$html .= '<th>' . I18N::translate('Families') . '</th>';
		$html .= '<th>' . I18N::translate('Media objects') . '</th>';
		$html .= '<th>' . I18N::translate('Sources') . '</th>';
		$html .= '<th>' . I18N::translate('Last change') . '</th>';
		$html .= '</tr></thead>';
		$html .= '<tbody>';

		foreach ($notes as $note) {
			if (!$note->canShow()) {
				continue;
			}
			if ($note->isPendingDeletion()) {
				$class = ' class="old"';
			} elseif ($note->isPendingAddition()) {
				$class = ' class="new"';
			} else {
				$class = '';
			}
			$html .= '<tr' . $class . '>';
			// Count of linked notes
			$html .= '<td data-sort="' . e($note->getSortName()) . '"><a class="name2" href="' . e($note->url()) . '">' . $note->getFullName() . '</a></td>';
			$key = $note->getXref() . '@' . $note->getTree()->getTreeId();
			// Count of linked individuals
			$num = array_key_exists($key, $count_individuals) ? $count_individuals[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Count of linked families
			$num = array_key_exists($key, $count_families) ? $count_families[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Count of linked media objects
			$num = array_key_exists($key, $count_media) ? $count_media[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Count of linked sources
			$num = array_key_exists($key, $count_sources) ? $count_sources[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Last change
			$html .= '<td data-sort="' . $note->lastChangeTimestamp(true) . '">' . $note->lastChangeTimestamp() . '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';

		return $html;
	}

	/**
	 * Print a table of repositories
	 *
	 * @param Repository[] $repositories
	 *
	 * @return string
	 */
	public static function repositoryTable($repositories) {
		// Count the number of linked records. These numbers include private records.
		// It is not good to bypass privacy, but many servers do not have the resources
		// to process privacy for every record in the tree
		$count_sources = Database::prepare(
			"SELECT CONCAT(l_to, '@', l_file), COUNT(*) FROM `##sources` JOIN `##link` ON l_from = s_id AND l_file = s_file AND l_type = 'REPO' GROUP BY l_to, l_file"
		)->fetchAssoc();

		$html = '';
		$html .= '<table ' . Datatables::repositoryTableAttributes() . '><thead><tr>';
		$html .= '<th>' . I18N::translate('Repository name') . '</th>';
		$html .= '<th>' . I18N::translate('Sources') . '</th>';
		$html .= '<th>' . I18N::translate('Last change') . '</th>';
		$html .= '</tr></thead>';
		$html .= '<tbody>';

		foreach ($repositories as $repository) {
			if (!$repository->canShow()) {
				continue;
			}
			if ($repository->isPendingDeletion()) {
				$class = ' class="old"';
			} elseif ($repository->isPendingAddition()) {
				$class = ' class="new"';
			} else {
				$class = '';
			}
			$html .= '<tr' . $class . '>';
			// Repository name(s)
			$html .= '<td data-sort="' . e($repository->getSortName()) . '">';
			foreach ($repository->getAllNames() as $n => $name) {
				if ($n) {
					$html .= '<br>';
				}
				if ($n == $repository->getPrimaryName()) {
					$html .= '<a class="name2" href="' . e($repository->url()) . '">' . $name['full'] . '</a>';
				} else {
					$html .= '<a href="' . e($repository->url()) . '">' . $name['full'] . '</a>';
				}
			}
			$html .= '</td>';
			$key = $repository->getXref() . '@' . $repository->getTree()->getTreeId();
			// Count of linked sources
			$num = array_key_exists($key, $count_sources) ? $count_sources[$key] : 0;
			$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
			// Last change
			$html .= '<td data-sort="' . $repository->lastChangeTimestamp(true) . '">' . $repository->lastChangeTimestamp() . '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';

		return $html;
	}

	/**
	 * Print a table of media objects
	 *
	 * @param Media[] $media_objects
	 *
	 * @return string
	 */
	public static function mediaTable($media_objects) {
		global $WT_TREE, $controller;

		$html     = '';
		$table_id = 'table-obje-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
		$controller
			->addInlineJavascript('
				$("#' . $table_id . '").dataTable({
					dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
					' . I18N::datatablesI18N() . ',
					autoWidth:false,
					processing: true,
					columns: [
						/* Thumbnail   */ { sortable: false },
						/* Title       */ { type: "text" },
						/* Individuals */ { type: "num" },
						/* Families    */ { type: "num" },
						/* Sources     */ { type: "num" },
						/* Last change */ { visible: ' . ($WT_TREE->getPreference('SHOW_LAST_CHANGE') ? 'true' : 'false') . ' },
					],
					displayLength: 20,
					pagingType: "full_numbers"
				});
			');

		$html .= '<div class="media-list">';
		$html .= '<table id="' . $table_id . '"><thead><tr>';
		$html .= '<th>' . I18N::translate('Media') . '</th>';
		$html .= '<th>' . I18N::translate('Title') . '</th>';
		$html .= '<th>' . I18N::translate('Individuals') . '</th>';
		$html .= '<th>' . I18N::translate('Families') . '</th>';
		$html .= '<th>' . I18N::translate('Sources') . '</th>';
		$html .= '<th>' . I18N::translate('Last change') . '</th>';
		$html .= '</tr></thead>';
		$html .= '<tbody>';

		foreach ($media_objects as $media_object) {
			if ($media_object->canShow()) {
				$name = $media_object->getFullName();
				if ($media_object->isPendingDeletion()) {
					$class = ' class="old"';
				} elseif ($media_object->isPendingAddition()) {
					$class = ' class="new"';
				} else {
					$class = '';
				}
				$html .= '<tr' . $class . '>';
				// Media object thumbnail
				$html .= '<td>';
				foreach ($media_object as $media_file) {
					$html .= $media_file->displayImage(100, 100, 'contain', []);
				}
				$html .= '</td>';
				// Media object name(s)
				$html .= '<td data-sort="' . e($media_object->getSortName()) . '">';
				$html .= '<a href="' . e($media_object->url()) . '" class="list_item name2">' . $name . '</a>';
				$html .= '</td>';

				// Count of linked individuals
				$num = count($media_object->linkedIndividuals('OBJE'));
				$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
				// Count of linked families
				$num = count($media_object->linkedFamilies('OBJE'));
				$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
				// Count of linked sources
				$num = count($media_object->linkedSources('OBJE'));
				$html .= '<td class="center" data-sort="' . $num . '">' . I18N::number($num) . '</td>';
				// Last change
				$html .= '<td data-sort="' . $media_object->lastChangeTimestamp(true) . '">' . $media_object->lastChangeTimestamp() . '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</tbody></table></div>';

		return $html;
	}

	/**
	 * Print a tagcloud of surnames.
	 *
	 * @param string[][] $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
	 * @param string $route individual-list or family-listlist
	 * @param bool $totals show totals after each name
	 * @param Tree $tree generate links to this tree
	 *
	 * @return string
	 */
	public static function surnameTagCloud($surnames, $route, $totals, Tree $tree) {
		$minimum = PHP_INT_MAX;
		$maximum = 1;
		foreach ($surnames as $surn => $surns) {
			foreach ($surns as $spfxsurn => $indis) {
				$maximum = max($maximum, count($indis));
				$minimum = min($minimum, count($indis));
			}
		}

		$html = '';
		foreach ($surnames as $surn => $surns) {
			foreach ($surns as $spfxsurn => $indis) {
				if ($maximum === $minimum) {
					// All surnames occur the same number of times
					$size = 150.0;
				} else {
					$size = 75.0 + 125.0 * (count($indis) - $minimum) / ($maximum - $minimum);
				}
				$url = route($route, ['surname' => $surn, 'ged' => $tree->getName()]);
				$html .= '<a style="font-size:' . $size . '%" href="' . e($url) . '">';
				if ($totals) {
					$html .= I18N::translate('%1$s (%2$s)', '<span dir="auto">' . $spfxsurn . '</span>', I18N::number(count($indis)));
				} else {
					$html .= $spfxsurn;
				}
				$html .= '</a> ';
			}
		}

		return '<div class="tag_cloud">' . $html . '</div>';
	}

	/**
	 * Print a list of surnames.
	 *
	 * @param string[][] $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
	 * @param int $style 1=bullet list, 2=semicolon-separated list, 3=tabulated list with up to 4 columns
	 * @param bool $totals show totals after each name
	 * @param string $route individual-list or family-list
	 * @param Tree $tree Link back to the individual list in this tree
	 *
	 * @return string
	 */
	public static function surnameList($surnames, $style, $totals, $route, Tree $tree) {
		$html = [];
		foreach ($surnames as $surn => $surns) {
			// Each surname links back to the indilist
			if ($surn) {
				$url = route($route, ['surname' => $surn, 'ged' => $tree->getName()]);
			} else {
				$url = route($route, ['alpha' => ',', 'ged' => $tree->getName()]);
			}
			// If all the surnames are just case variants, then merge them into one
			// Comment out this block if you want SMITH listed separately from Smith
			$first_spfxsurn = null;
			foreach ($surns as $spfxsurn => $indis) {
				if ($first_spfxsurn) {
					if (I18N::strtoupper($spfxsurn) == I18N::strtoupper($first_spfxsurn)) {
						$surns[$first_spfxsurn] = array_merge($surns[$first_spfxsurn], $surns[$spfxsurn]);
						unset($surns[$spfxsurn]);
					}
				} else {
					$first_spfxsurn = $spfxsurn;
				}
			}
			$subhtml = '<a href="' . e($url) . '" dir="auto">' . e(implode(I18N::$list_separator, array_keys($surns))) . '</a>';

			if ($totals) {
				$subtotal = 0;
				foreach ($surns as $indis) {
					$subtotal += count($indis);
				}
				$subhtml .= '&nbsp;(' . I18N::number($subtotal) . ')';
			}
			$html[] = $subhtml;
		}
		switch ($style) {
			case 1:
				return '<ul><li>' . implode('</li><li>', $html) . '</li></ul>';
			case 2:
				return implode(I18N::$list_separator, $html);
			case 3:
				$i     = 0;
				$count = count($html);
				if ($count > 36) {
					$col = 4;
				} elseif ($count > 18) {
					$col = 3;
				} elseif ($count > 6) {
					$col = 2;
				} else {
					$col = 1;
				}
				$newcol = ceil($count / $col);
				$html2  = '<table class="list_table"><tr>';
				$html2 .= '<td class="list_value" style="padding: 14px;">';

				foreach ($html as $surns) {
					$html2 .= $surns . '<br>';
					$i++;
					if ($i == $newcol && $i < $count) {
						$html2 .= '</td><td class="list_value" style="padding: 14px;">';
						$newcol = $i + ceil($count / $col);
					}
				}
				$html2 .= '</td></tr></table>';

				return $html2;
		}
	}
}
