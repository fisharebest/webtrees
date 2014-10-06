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

class families_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ WT_I18N::translate('Family list');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Families” module */ WT_I18N::translate('A sidebar showing an alphabetic list of all the families in the family tree.');
	}

	// Implement WT_Module
	public function modAction($modAction) {
		switch ($modAction) {
		case 'ajax':
			Zend_Session::writeClose();
			header('Content-Type: text/html; charset=UTF-8');
			echo $this->getSidebarAjaxContent();
			break;
		default:
			header('HTTP/1.0 404 Not Found');
			break;
		}
		exit;
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 50;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		$alpha   = WT_Filter::get('alpha'); // All surnames beginning with this letter where "@"=unknown and ","=none
		$surname = WT_Filter::get('surname'); // All indis with this surname.
		$search  = WT_Filter::get('search');

		if ($search) {
			return $this->search($search);
		} elseif ($alpha=='@' || $alpha==',' || $surname) {
			return $this->getSurnameFams($alpha, $surname);
		} elseif ($alpha) {
			return $this->getAlphaSurnames($alpha, $surname);
		} else {
			return '';
		}
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $WT_IMAGES, $UNKNOWN_NN, $controller;

		// Fetch a list of the initial letters of all surnames in the database
		$initials = WT_Query_Name::surnameAlpha(true, false, WT_GED_ID, false);

		$controller->addInlineJavascript('
			var famloadedNames = new Array();

			function fsearchQ() {
				var query = jQuery("#sb_fam_name").val();
				if (query.length>1) {
					jQuery("#sb_fam_content").load("module.php?mod=' . $this->getName() . '&mod_action=ajax&sb_action=families&search="+query);
				}
			}

			var famtimerid = null;
			jQuery("#sb_fam_name").keyup(function(e) {
				if (famtimerid) window.clearTimeout(famtimerid);
				famtimerid = window.setTimeout("fsearchQ()", 500);
			});
			jQuery("#sb_content_families").on("click", ".sb_fam_letter", function() {
				jQuery("#sb_fam_content").load(this.href);
				return false;
			});
			jQuery("#sb_content_families").on("click", ".sb_fam_surname", function() {
				var surname = jQuery(this).attr("title");
				var alpha = jQuery(this).attr("alt");

				if (!famloadedNames[surname]) {
					jQuery.ajax({
					  url: "module.php?mod='.$this->getName().'&mod_action=ajax&sb_action=families&alpha="+alpha+"&surname="+surname,
					  cache: false,
					  success: function(html) {
					    jQuery("#sb_fam_"+surname+" div").html(html);
					    jQuery("#sb_fam_"+surname+" div").show();
					    jQuery("#sb_fam_"+surname).css("list-style-image", "url('.$WT_IMAGES['minus'].')");
					    famloadedNames[surname]=2;
					  }
					});
				}
				else if (famloadedNames[surname]==1) {
					famloadedNames[surname]=2;
					jQuery("#sb_fam_"+surname+" div").show();
					jQuery("#sb_fam_"+surname).css("list-style-image", "url('.$WT_IMAGES['minus'].')");
				}
				else {
					famloadedNames[surname]=1;
					jQuery("#sb_fam_"+surname+" div").hide();
					jQuery("#sb_fam_"+surname).css("list-style-image", "url('.$WT_IMAGES['plus'].')");
				}
				return false;
			});
		');
		$out=
			'<form method="post" action="module.php?mod='.$this->getName().'&amp;mod_action=ajax" onsubmit="return false;">'.
			'<input type="search" name="sb_fam_name" id="sb_fam_name" placeholder="'.WT_I18N::translate('Search').'">'.
			'<p>';
		foreach ($initials as $letter=>$count) {
			switch ($letter) {
				case '@':
					$html=$UNKNOWN_NN;
					break;
				case ',':
					$html=WT_I18N::translate('None');
					break;
				case ' ':
					$html='&nbsp;';
					break;
				default:
					$html=$letter;
					break;
			}
			$html='<a href="module.php?mod='.$this->getName().'&amp;mod_action=ajax&amp;sb_action=families&amp;alpha='.urlencode($letter).'" class="sb_fam_letter">'.$html.'</a>';
			$out .= $html." ";
		}

		$out .= '</p>';
		$out .= '<div id="sb_fam_content">';
		$out .= '</div></form>';
		return $out;
	}

	public function getAlphaSurnames($alpha, $surname1='') {
		$surnames = WT_Query_Name::surnames('', $alpha, true, true, WT_GED_ID);
		$out = '<ul>';
		foreach (array_keys($surnames) as $surname) {
			$out .= '<li id="sb_fam_' . $surname . '" class="sb_fam_surname_li"><a href="' . $surname . '" title="' . $surname . '" alt="' . $alpha . '" class="sb_fam_surname">' . $surname . '</a>';
			if (!empty($surname1) && $surname1==$surname) {
				$out .= '<div class="name_tree_div_visible">';
				$out .= $this->getSurnameFams($alpha, $surname1);
				$out .= '</div>';
			} else {
				$out .= '<div class="name_tree_div"></div>';
			}
			$out .= '</li>';
		}
		$out .= '</ul>';
		return $out;
	}

	public function getSurnameFams($alpha, $surname) {
		$families=WT_Query_Name::families($surname, $alpha, '', true, WT_GED_ID);
		$out = '<ul>';
		foreach ($families as $family) {
			if ($family->canShowName()) {
				$out .= '<li><a href="'.$family->getHtmlUrl().'">'.$family->getFullName().' ';
				if ($family->canShow()) {
					$marriage_year=$family->getMarriageYear();
					if ($marriage_year) {
						$out.=' ('.$marriage_year.')';
					}
				}
				$out .= '</a></li>';
			}
		}
		$out .= '</ul>';
		return $out;
	}

	public function search($query) {
		if (strlen($query)<2) {
			return '';
		}

		//-- search for INDI names
		$rows=WT_DB::prepare(
			"SELECT i_id AS xref".
			" FROM `##individuals`, `##name`".
			" WHERE (i_id LIKE ? OR n_sort LIKE ?)".
			" AND i_id=n_id AND i_file=n_file AND i_file=?".
			" ORDER BY n_sort"
		)
		->execute(array("%{$query}%", "%{$query}%", WT_GED_ID))
		->fetchAll();
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row->xref;
		}

		$vars=array();
		if (empty($ids)) {
			//-- no match : search for FAM id
			$where = "f_id LIKE ?";
			$vars[]="%{$query}%";
		} else {
			//-- search for spouses
			$qs=implode(',', array_fill(0, count($ids), '?'));
			$where = "(f_husb IN ($qs) OR f_wife IN ($qs))";
			$vars=array_merge($vars, $ids, $ids);
		}

		$vars[]=WT_GED_ID;
		$rows=WT_DB::prepare("SELECT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom FROM `##families` WHERE {$where} AND f_file=?")
		->execute($vars)
		->fetchAll();

		$out = '<ul>';
		foreach ($rows as $row) {
			$family=WT_Family::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($family->canShowName()) {
				$out .= '<li><a href="'.$family->getHtmlUrl().'">'.$family->getFullName().' ';
				if ($family->canShow()) {
					$marriage_year=$family->getMarriageYear();
					if ($marriage_year) {
						$out.=' ('.$marriage_year.')';
					}
				}
				$out .= '</a></li>';
			}
		}
		$out .= '</ul>';
		return $out;
	}
}
