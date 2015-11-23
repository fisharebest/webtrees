<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Query\QueryName;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 * Class IndividualSidebarModule
 */
class IndividualSidebarModule extends AbstractModule implements ModuleSidebarInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Individual list');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of “Individuals” module */ I18N::translate('A sidebar showing an alphabetic list of all the individuals in the family tree.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'ajax':
			header('Content-Type: text/html; charset=UTF-8');
			echo $this->getSidebarAjaxContent();
			break;
		default:
			http_response_code(404);
			break;
		}
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 40;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent() {
		return !Auth::isSearchEngine();
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		global $WT_TREE;

		$alpha   = Filter::get('alpha'); // All surnames beginning with this letter where "@"=unknown and ","=none
		$surname = Filter::get('surname'); // All indis with this surname.
		$search  = Filter::get('search');

		if ($search) {
			return $this->search($WT_TREE, $search);
		} elseif ($alpha == '@' || $alpha == ',' || $surname) {
			return $this->getSurnameIndis($WT_TREE, $alpha, $surname);
		} elseif ($alpha) {
			return $this->getAlphaSurnames($WT_TREE, $alpha);
		} else {
			return '';
		}
	}

	/**
	 * Load this sidebar synchronously.
	 *
	 * @return string
	 */
	public function getSidebarContent() {
		global $controller, $WT_TREE;

		// Fetch a list of the initial letters of all surnames in the database
		$initials = QueryName::surnameAlpha($WT_TREE, true, false, false);

		$controller->addInlineJavascript('
			var loadedNames = new Array();

			function isearchQ() {
				var query = jQuery("#sb_indi_name").val();
				if (query.length>1) {
					jQuery("#sb_indi_content").load("module.php?mod=' . $this->getName() . '&mod_action=ajax&sb_action=individuals&search="+query);
				}
			}

			var timerid = null;
			jQuery("#sb_indi_name").keyup(function(e) {
				if (timerid) window.clearTimeout(timerid);
				timerid = window.setTimeout("isearchQ()", 500);
			});
			jQuery("#sb_content_individuals").on("click", ".sb_indi_letter", function() {
				jQuery("#sb_indi_content").load(this.href);
				return false;
			});
			jQuery("#sb_content_individuals").on("click", ".sb_indi_surname", function() {
				var element = jQuery(this);
				var surname = element.data("surname");
				var alpha   = element.data("alpha");

				if (!loadedNames[surname]) {
					jQuery.ajax({
					  url: "module.php?mod=' . $this->getName() . '&mod_action=ajax&sb_action=individuals&alpha=" + encodeURIComponent(alpha) + "&surname=" + encodeURIComponent(surname),
					  cache: false,
					  success: function(html) {
					    jQuery("div.name_tree_div", element.closest("li"))
					    .html(html)
					    .show("fast")
					    .css("list-style-image", "url(' . Theme::theme()->parameter('image-minus') . ')");
					    loadedNames[surname]=2;
					  }
					});
				} else if (loadedNames[surname]==1) {
					loadedNames[surname]=2;
					jQuery("div.name_tree_div", jQuery(this).closest("li"))
					.show()
					.css("list-style-image", "url(' . Theme::theme()->parameter('image-minus') . ')");
				} else {
					loadedNames[surname]=1;
					jQuery("div.name_tree_div", jQuery(this).closest("li"))
					.hide("fast")
					.css("list-style-image", "url(' . Theme::theme()->parameter('image-plus') . ')");
				}
				return false;
			});
		');

		$out = '<form method="post" action="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax" onsubmit="return false;"><input type="search" name="sb_indi_name" id="sb_indi_name" placeholder="' . I18N::translate('Search') . '"><p>';
		foreach ($initials as $letter => $count) {
			switch ($letter) {
			case '@':
				$html = I18N::translateContext('Unknown surname', '…');
				break;
			case ',':
				$html = I18N::translate('None');
				break;
			case ' ':
				$html = '&nbsp;';
				break;
			default:
				$html = $letter;
				break;
			}
			$html = '<a href="module.php?mod=' . $this->getName() . '&amp;mod_action=ajax&amp;sb_action=individuals&amp;alpha=' . urlencode($letter) . '" class="sb_indi_letter">' . $html . '</a>';
			$out .= $html . " ";
		}

		$out .= '</p>';
		$out .= '<div id="sb_indi_content">';
		$out .= '</div></form>';

		return $out;
	}

	/**
	 * Get the initial letters of surnames.
	 *
	 * @param Tree   $tree
	 * @param string $alpha
	 *
	 * @return string
	 */
	private function getAlphaSurnames(Tree $tree, $alpha) {
		$surnames = QueryName::surnames($tree, '', $alpha, true, false);
		$out      = '<ul>';
		foreach (array_keys($surnames) as $surname) {
			$out .= '<li class="sb_indi_surname_li"><a href="#" data-surname="' . Filter::escapeHtml($surname) . '" data-alpha="' . Filter::escapeHtml($alpha) . '" class="sb_indi_surname">' . Filter::escapeHtml($surname) . '</a>';
			$out .= '<div class="name_tree_div"></div>';
			$out .= '</li>';
		}
		$out .= '</ul>';

		return $out;
	}

	/**
	 * Format a list of individuals.
	 *
	 * @param Tree   $tree
	 * @param string $alpha
	 * @param string $surname
	 *
	 * @return string
	 */
	private function getSurnameIndis(Tree $tree, $alpha, $surname) {
		$indis = QueryName::individuals($tree, $surname, $alpha, '', true, false);
		$out   = '<ul>';
		foreach ($indis as $person) {
			if ($person->canShowName()) {
				$out .= '<li><a href="' . $person->getHtmlUrl() . '">' . $person->getSexImage() . ' ' . $person->getFullName() . ' ';
				if ($person->canShow()) {
					$bd = $person->getLifeSpan();
					if (!empty($bd)) {
						$out .= ' (' . $bd . ')';
					}
				}
				$out .= '</a></li>';
			}
		}
		$out .= '</ul>';

		return $out;
	}

	/**
	 * Search for individuals in a tree.
	 *
	 * @param Tree   $tree  Search this tree
	 * @param string $query Search for this text
	 *
	 * @return string
	 */
	private function search(Tree $tree, $query) {
		if (strlen($query) < 2) {
			return '';
		}
		$rows = Database::prepare(
			"SELECT i_id AS xref, i_gedcom AS gedcom" .
			" FROM `##individuals`, `##name`" .
			" WHERE (i_id LIKE CONCAT('%', :query_1, '%') OR n_sort LIKE CONCAT('%', :query_2, '%'))" .
			" AND i_id = n_id AND i_file = n_file AND i_file = :tree_id" .
			" ORDER BY n_sort COLLATE :collation" .
			" LIMIT 50"
		)->execute(array(
			'query_1'   => $query,
			'query_2'   => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		))->fetchAll();

		$out = '<ul>';
		foreach ($rows as $row) {
			$person = Individual::getInstance($row->xref, $tree, $row->gedcom);
			if ($person->canShowName()) {
				$out .= '<li><a href="' . $person->getHtmlUrl() . '">' . $person->getSexImage() . ' ' . $person->getFullName() . ' ';
				if ($person->canShow()) {
					$bd = $person->getLifeSpan();
					if (!empty($bd)) {
						$out .= ' (' . $bd . ')';
					}
				}
				$out .= '</a></li>';
			}
		}
		$out .= '</ul>';

		return $out;
	}
}
