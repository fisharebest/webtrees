<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

class descendancy_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */
			WT_I18N::translate('Descendants');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Descendants” module */
			WT_I18N::translate('A sidebar showing the descendants of an individual.');
	}

	// Implement WT_Module
	public function modAction($modAction) {
		Zend_Session::writeClose();
		header('Content-Type: text/html; charset=UTF-8');

		switch ($modAction) {
		case 'search':
			$search = WT_Filter::get('search');
			echo $this->search($search);
			break;
		case 'descendants':
			$individual = WT_Individual::getInstance(WT_Filter::get('xref', WT_REGEX_XREF));
			if ($individual) {
				echo $this->loadSpouses($individual, 1);
			}
			break;
		default:
			header('HTTP/1.0 404 Not Found');
			break;
		}
		exit;
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 30;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}

	// Implement WT_Module_Sidebar
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
			jQuery("#sb_desc_name").blur(function(){if (this.value=="") this.value="' . WT_I18N::translate('Search') . '";});
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
			'<input type="search" name="sb_desc_name" id="sb_desc_name" placeholder="' . WT_I18N::translate('Search') . '">' .
			'</form>' .
			'<div id="sb_desc_content">' .
			'<ul>' . $this->getPersonLi($controller->record, 1) . '</ul>' .
			'</div>';
	}

	public function getPersonLi(WT_Individual $person, $generations = 0) {
		$icon = $generations > 0 ? 'icon-minus' : 'icon-plus';
		$lifespan = $person->canShow() ? '(' . $person->getLifeSpan() . ')' : '';
		$spouses = $generations > 0 ? $this->loadSpouses($person, 0) : '';
		return sprintf('<li class="sb_desc_indi_li">
		                  <a class="sb_desc_indi" href="module.php?mod=%s&amp;mod_action=descendants&amp;xref=%s"><i class="plusminus %s"></i>%s %s %s</a>
		                  <a class="icon-button_indi" href="%s"></a>
		                  %s
		                  <div>%s</div>
		                </li>', $this->getName(), $person->getXref(), $icon, $person->getSexImage(), $person->getFullName(), $lifespan, $person->getHtmlUrl(), '', $spouses);
	}

	public function getFamilyLi(WT_Family $family, WT_Individual $person, $generations = 0) {
		$marryear = $family->getMarriageYear();
		$marr = $marryear ? '<i class="icon-rings"></i>' . $marryear : '';
		$fam = '<a href="' . $family->getHtmlUrl() . '" class="icon-button_family"></a>';
		$kids = $this->loadChildren($family, $generations);
		return sprintf('<li class="sb_desc_indi_li">
		                  <a class="sb_desc_indi" href="#"><i class="plusminus icon-minus"></i>%s %s %s</a>
		                  <a class="icon-button_indi" href="%s"></a>
		                  %s
		                  <div>%s</div>
		                </li>', $person->getSexImage(), $person->getFullName(), $marr, $person->getHtmlUrl(), $fam, $kids);
	}

	public function search($query) {
		if (strlen($query) < 2) {
			return '';
		}
		$rows = WT_DB::prepare(
			"SELECT i_id AS xref" .
			" FROM `##individuals`, `##name`" .
			" WHERE (i_id LIKE ? OR n_sort LIKE ?)" .
			" AND i_id=n_id AND i_file=n_file AND i_file=?" .
			" ORDER BY n_sort"
		)
			->execute(array("%{$query}%", "%{$query}%", WT_GED_ID))
			->fetchAll();

		$out = '';
		foreach ($rows as $row) {
			$person = WT_Individual::getInstance($row->xref);
			if ($person->canShowName()) {
				$out .= $this->getPersonLi($person);
			}
		}
		if ($out) {
			return '<ul>' . $out . '</ul>';
		} else {
			return '';
		}
	}

	public function loadSpouses(WT_Individual $person, $generations) {
		$out = '';
		if ($person && $person->canShow()) {
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$out .= $this->getFamilyLi($family, $spouse, $generations - 1);
				}
			}
			if (!$out) {
				$out = '<li class="sb_desc_none">' . WT_I18N::translate('No children') . '</li>';
			}
		}
		if ($out) {
			return '<ul>' . $out . '</ul>';
		} else {
			return '';
		}
	}

	public function loadChildren(WT_Family $family, $generations) {
		$out = '';
		if ($family->canShow()) {
			$children = $family->getChildren();
			if ($children) {
				foreach ($children as $child) {
					$out .= $this->getPersonLi($child, $generations - 1);
				}
			} else {
				$out .= '<li class="sb_desc_none">' . WT_I18N::translate('No children') . '</li>';
			}
		}
		if ($out) {
			return '<ul>' . $out . '</ul>';
		} else {
			return '';
		}
	}
}
