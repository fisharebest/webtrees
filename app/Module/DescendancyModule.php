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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

/**
 * Class DescendancyModule
 */
class DescendancyModule extends AbstractModule implements ModuleSidebarInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */
			I18N::translate('Descendants');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Descendants” module */
			I18N::translate('A sidebar showing the descendants of an individual.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		global $WT_TREE;

		header('Content-Type: text/html; charset=UTF-8');

		switch ($mod_action) {
		case 'search':
			$search = Filter::get('search');
			echo $this->search($search, $WT_TREE);
			break;
		case 'descendants':
			$individual = Individual::getInstance(Filter::get('xref', WT_REGEX_XREF), $WT_TREE);
			if ($individual) {
				echo $this->loadSpouses($individual, 1);
			}
			break;
		default:
			http_response_code(404);
			break;
		}
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 30;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent() {
		return true;
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		return '';
	}

	/**
	 * Load this sidebar synchronously.
	 *
	 * @return string
	 */
	public function getSidebarContent() {
		global $controller;

		$controller->addInlineJavascript('
			function dsearchQ() {
				var query = jQuery("#sb_desc_name").val();
				if (query.length>1) {
					jQuery("#sb_desc_content").load("module.php?mod=' . $this->getName() . '&mod_action=search&search="+query);
				}
			}

			jQuery("#sb_desc_name").focus(function(){this.select();});
			jQuery("#sb_desc_name").blur(function(){if (this.value=="") this.value="' . I18N::translate('Search') . '";});
			var dtimerid = null;
			jQuery("#sb_desc_name").keyup(function(e) {
				if (dtimerid) window.clearTimeout(dtimerid);
				dtimerid = window.setTimeout("dsearchQ()", 500);
			});

			jQuery("#sb_desc_content").on("click", ".sb_desc_indi", function() {
				var self = jQuery(this),
					state = self.children(".plusminus"),
					target = self.siblings("div");
				if(state.hasClass("icon-plus")) {
					if (jQuery.trim(target.html())) {
						target.show("fast"); // already got content so just show it
					} else {
						target
							.hide()
							.load(self.attr("href"), function(response, status, xhr) {
								if(status == "success" && response !== "") {
									target.show("fast");
								}
							})
					}
				} else {
					target.hide("fast");
				}
				state.toggleClass("icon-minus icon-plus");
				return false;
			});
		');

		return
			'<form method="post" action="module.php?mod=' . $this->getName() . '&amp;mod_action=search" onsubmit="return false;">' .
			'<input type="search" name="sb_desc_name" id="sb_desc_name" placeholder="' . I18N::translate('Search') . '">' .
			'</form>' .
			'<div id="sb_desc_content">' .
			'<ul>' . $this->getPersonLi($controller->record, 1) . '</ul>' .
			'</div>';
	}

	/**
	 * Format an individual in a list.
	 *
	 * @param Individual $person
	 * @param int        $generations
	 *
	 * @return string
	 */
	public function getPersonLi(Individual $person, $generations = 0) {
		$icon     = $generations > 0 ? 'icon-minus' : 'icon-plus';
		$lifespan = $person->canShow() ? '(' . $person->getLifeSpan() . ')' : '';
		$spouses  = $generations > 0 ? $this->loadSpouses($person, 0) : '';

		return
			'<li class="sb_desc_indi_li">' .
			'<a class="sb_desc_indi" href="module.php?mod=' . $this->getName() . '&amp;mod_action=descendants&amp;xref=' . $person->getXref() . '">' .
			'<i class="plusminus ' . $icon . '"></i>' .
			$person->getSexImage() . $person->getFullName() . $lifespan .
			'</a>' .
			'<a class="icon-button_indi" href="' . $person->getHtmlUrl() . '"></a>' .
			'<div>' . $spouses . '</div>' .
			'</li>';
	}

	/**
	 * Format a family in a list.
	 *
	 * @param Family     $family
	 * @param Individual $person
	 * @param int        $generations
	 *
	 * @return string
	 */
	public function getFamilyLi(Family $family, Individual $person, $generations = 0) {
		$spouse = $family->getSpouse($person);
		if ($spouse) {
			$spouse_name = $spouse->getSexImage() . $spouse->getFullName();
			$spouse_link = '<a class="icon-button_indi" href="' . $spouse->getHtmlUrl() . '"></a>';
		} else {
			$spouse_name = '';
			$spouse_link = '';
		}

		$marryear = $family->getMarriageYear();
		$marr     = $marryear ? '<i class="icon-rings"></i>' . $marryear : '';

		return
			'<li class="sb_desc_indi_li">' .
			'<a class="sb_desc_indi" href="#"><i class="plusminus icon-minus"></i>' . $spouse_name . $marr . '</a>' .
			$spouse_link .
			'<a href="' . $family->getHtmlUrl() . '" class="icon-button_family"></a>' .
		 '<div>' . $this->loadChildren($family, $generations) . '</div>' .
			'</li>';
	}

	/**
	 * Respond to an autocomplete search request.
	 *
	 * @param string $query Search for this term
	 * @param Tree   $tree  Search in this tree
	 *
	 * @return string
	 */
	public function search($query, Tree $tree) {
		if (strlen($query) < 2) {
			return '';
		}

		$rows = Database::prepare(
			"SELECT i_id AS xref" .
			" FROM `##individuals`" .
			" JOIN `##name` ON i_id = n_id AND i_file = n_file" .
			" WHERE n_sort LIKE CONCAT('%', :query, '%') AND i_file = :tree_id" .
			" ORDER BY n_sort"
		)->execute(array(
			'query'   => $query,
			'tree_id' => $tree->getTreeId(),
		))->fetchAll();

		$out = '';
		foreach ($rows as $row) {
			$person = Individual::getInstance($row->xref, $tree);
			if ($person && $person->canShowName()) {
				$out .= $this->getPersonLi($person);
			}
		}
		if ($out) {
			return '<ul>' . $out . '</ul>';
		} else {
			return '';
		}
	}

	/**
	 * Display spouses.
	 *
	 * @param Individual $person
	 * @param int        $generations
	 *
	 * @return string
	 */
	public function loadSpouses(Individual $person, $generations) {
		$out = '';
		if ($person && $person->canShow()) {
			foreach ($person->getSpouseFamilies() as $family) {
				$out .= $this->getFamilyLi($family, $person, $generations - 1);
			}
		}
		if ($out) {
			return '<ul>' . $out . '</ul>';
		} else {
			return '';
		}
	}

	/**
	 * Display descendants.
	 *
	 * @param Family $family
	 * @param int    $generations
	 *
	 * @return string
	 */
	public function loadChildren(Family $family, $generations) {
		$out = '';
		if ($family->canShow()) {
			$children = $family->getChildren();
			if ($children) {
				foreach ($children as $child) {
					$out .= $this->getPersonLi($child, $generations - 1);
				}
			} else {
				$out .= '<li class="sb_desc_none">' . I18N::translate('No children') . '</li>';
			}
		}
		if ($out) {
			return '<ul>' . $out . '</ul>';
		} else {
			return '';
		}
	}
}
