<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class descendancy_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ WT_I18N::translate('Descendants');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Descendants" module */ WT_I18N::translate('A sidebar showing the descendants of an individual.');
	}

	// Implement WT_Module
	public function modAction($modAction) {
		switch ($modAction) {
		case 'ajax':
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
		return 30;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		$search=safe_GET('search');
		$pid   =safe_GET('pid', WT_REGEX_XREF);
		$famid =safe_GET('famid', WT_REGEX_XREF);

		if ($search) {
			return $this->search($search);
		} elseif ($pid) {
			return $this->loadSpouses($pid, 1);
		} elseif ($famid) {
			return $this->loadChildren($famid, 1);
		} else {
			return '';
		}
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $WT_IMAGES, $controller;

		$controller->addInlineJavaScript('
			var dloadedNames = new Array();

			function dsearchQ() {
				var query = jQuery("#sb_desc_name").attr("value");
				if (query.length>1) {
					jQuery("#sb_desc_content").load("module.php?mod='.$this->getName().'&mod_action=ajax&sb_action=descendancy&search="+query);
				}
			}

			jQuery("#sb_desc_name").focus(function(){this.select();});
			jQuery("#sb_desc_name").blur(function(){if (this.value=="") this.value="'.WT_I18N::translate('Search').'";});
			var dtimerid = null;
			jQuery("#sb_desc_name").keyup(function(e) {
				if (dtimerid) window.clearTimeout(dtimerid);
				dtimerid = window.setTimeout("dsearchQ()", 500);
			});

			jQuery(".sb_desc_indi").live("click", function() {
				var pid=this.title;
				if (!dloadedNames[pid]) {
					jQuery("#sb_desc_"+pid+" div").load(this.href);
					jQuery("#sb_desc_"+pid+" div").show();
					jQuery("#sb_desc_"+pid+" .plusminus").attr("src", "'.$WT_IMAGES['minus'].'");
					dloadedNames[pid]=2;
				}
				else if (dloadedNames[pid]==1) {
					dloadedNames[pid]=2;
					jQuery("#sb_desc_"+pid+" div").show();
					jQuery("#sb_desc_"+pid+" .plusminus").attr("src", "'.$WT_IMAGES['minus'].'");
				}
				else {
					dloadedNames[pid]=1;
					jQuery("#sb_desc_"+pid+" div").hide();
					jQuery("#sb_desc_"+pid+" .plusminus").attr("src", "'.$WT_IMAGES['plus'].'");
				}
				return false;
			});
		');

		return
			'<form method="post" action="module.php?mod='.$this->getName().'&amp;mod_action=ajax" onsubmit="return false;">'.
			'<input type="text" name="sb_desc_name" id="sb_desc_name" placeholder="'.WT_I18N::translate('Search').'">'.
			'</form>'.
			'<div id="sb_desc_content">'.
			'<ul>'.$this->getPersonLi($controller->record, 1).'</ul>'.
			'</div>';
	}

	public function getPersonLi(WT_Person $person, $generations=0) {
		global $WT_IMAGES;

		$out = '<li id="sb_desc_'.$person->getXref().'" class="sb_desc_indi_li"><a href="module.php?mod='.$this->getName().'&amp;mod_action=ajax&amp;sb_action=descendancy&amp;pid='.$person->getXref().'" title="'.$person->getXref().'" class="sb_desc_indi">';
		if ($generations>0) {
			$out .= '<img src="'.$WT_IMAGES['minus'].'" class="plusminus" alt="">';
		} else {
			$out .= '<img src="'.$WT_IMAGES['plus'].'" class="plusminus" alt="">';
		}
		$out .= $person->getSexImage().' '.$person->getFullName().' ';
		if ($person->canDisplayDetails()) {
			$out .= ' ('.$person->getLifeSpan().')';
		}
		$out .= '</a> <a href="'.$person->getHtmlUrl().'"><img src="'.$WT_IMAGES['button_indi'].'" alt="indi"></a>';
		if ($generations>0) {
			$out .= '<div class="desc_tree_div_visible">';
			$out .= $this->loadSpouses($person->getXref());
			$out .= '</div>';
			$base_controller=new WT_Controller_Base();
			$base_controller->addInlineJavaScript('dloadedNames["'.$person->getXref().'"]=2;');
		} else {
			$out .= '<div class="desc_tree_div">';
			$out .= '</div>';
		}
		$out .= '</li>';
		return $out;
	}

	public function getFamilyLi(WT_Family $family, WT_Person $person, $generations=0) {
		global $WT_IMAGES;

		$out = '<li id="sb_desc_'.$family->getXref().'" class="sb_desc_indi_li"><a href="module.php?mod='.$this->getName().'&amp;mod_action=ajax&amp;sb_action=descendancy&amp;famid='.$family->getXref().'" title="'.$family->getXref().'" class="sb_desc_indi">';
		$out .= '<img src="'.$WT_IMAGES['minus'].'" class="plusminus" alt="">';
		$out .= $person->getSexImage().$person->getFullName();

		$marryear = $family->getMarriageYear();
		if (!empty($marryear)) {
			$out .= ' ('.WT_Gedcom_Tag::getLabel('MARR').' '.$marryear.')';
		}
		$out .= '</a> <a href="'.$person->getHtmlUrl().'"><img src="'.$WT_IMAGES['button_indi'].'" alt="indi"></a>';
		$out .= '<a href="'.$family->getHtmlUrl().'"><img src="'.$WT_IMAGES['button_family'].'" alt="family"></a>';
		$out .= '<div class="desc_tree_div_visible">';
		$out .= $this->loadChildren($family->getXref(), $generations);
		$out .= '</div>';
		$base_controller=new WT_Controller_Base();
		$base_controller->addInlineJavaScript('dloadedNames["'.$family->getXref().'"]=2;');
		$out .= '</li>';
		return $out;
	}

	public function search($query) {
		if (strlen($query)<2) return '';
		$rows=WT_DB::prepare(
			"SELECT ? AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec".
			" FROM `##individuals`, `##name`".
			" WHERE (i_id LIKE ? OR n_sort LIKE ?)".
			" AND i_id=n_id AND i_file=n_file AND i_file=?".
			" ORDER BY n_sort LIMIT ".WT_AUTOCOMPLETE_LIMIT
		)
		->execute(array('INDI', "%{$query}%", "%{$query}%", WT_GED_ID))
		->fetchAll(PDO::FETCH_ASSOC);

		$out = '';
		foreach ($rows as $row) {
			$person=WT_Person::getInstance($row);
			if ($person->canDisplayName()) {
				$out .= $this->getPersonLi($person);
			}
		}
		if ($out) {
			return '<ul>'.$out.'</ul>';
		} else {
			return '';
		}
	}

	public function loadSpouses($pid, $generations=0) {
		$out = '';
		$person = WT_Person::getInstance($pid);
		if ($person->canDisplayDetails()) {
			foreach($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$out .= $this->getFamilyLi($family, $spouse, $generations-1);
				} else {
					foreach($family->getChildren() as $child) {
						$out .= $this->getPersonLi($child, $generations-1);
					}
				}
			}
		}
		if ($out) {
			return '<ul>'.$out.'</ul>';
		} else {
			return '';
		}
	}

	public function loadChildren($famid, $generations=0) {
		$out = '';
		$family = WT_Family::getInstance($famid);
		if ($family->canDisplayDetails()) {
			$children = $family->getChildren();
			if (count($children)>0) {
				foreach($children as $child) {
					$out .= $this->getPersonLi($child, $generations-1);
				}
			} else {
				$out .= WT_I18N::translate('No children');
			}
		}
		if ($out) {
			return '<ul>'.$out.'</ul>';
		} else {
			return '';
		}
	}
}
