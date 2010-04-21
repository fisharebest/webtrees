<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 John Finlay
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Modules
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class descendancy_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend WT_Module
	public function getTitle() {
		return i18n::translate('Descendancy');
	}

	// Extend WT_Module
	public function getDescription() {
		return i18n::translate('Adds a sidebar which allows for easy navigation of indiviuals in a descendants tree-view format.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 20;
	}
	
	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		$search   =safe_GET('search');
		$pid   =safe_GET('pid', WT_REGEX_XREF);
		$famid   =safe_GET('famid', WT_REGEX_XREF);

		$last = array('search'=>$search);
		$_SESSION['sb_descendancy_last'] = $last;

		if (!empty($search)) return $this->search($search);
		else if (!empty($pid)) return $this->loadSpouses($pid, 1);
		else if (!empty($famid)) return $this->loadChildren($famid, 1);
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $WT_IMAGE_DIR, $WT_IMAGES;

		$out = '<script type="text/javascript">
		<!--
		var dloadedNames = new Array();
		
		function dsearchQ() {
			var query = jQuery("#sb_desc_name").attr("value");
			if (query.length>1) {
				jQuery("#sb_desc_content").load("sidebar.php?sb_action=descendancy&search="+query);
			}
		}
		
		jQuery(document).ready(function(){
			jQuery("#sb_desc_name").focus(function(){this.select();});
			jQuery("#sb_desc_name").blur(function(){if (this.value=="") this.value="'.i18n::translate('Search').'";});
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
					jQuery("#sb_desc_"+pid+" .plusminus").attr("src", "'.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].'");
					dloadedNames[pid]=2;
				}
				else if (dloadedNames[pid]==1) {
					dloadedNames[pid]=2;
					jQuery("#sb_desc_"+pid+" div").show();
					jQuery("#sb_desc_"+pid+" .plusminus").attr("src", "'.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].'");
				}
				else {
					dloadedNames[pid]=1;
					jQuery("#sb_desc_"+pid+" div").hide();
					jQuery("#sb_desc_"+pid+" .plusminus").attr("src", "'.$WT_IMAGE_DIR."/".$WT_IMAGES['plus']['other'].'");
				}
				return false;
			});
		});
		//-->
		</script>
		<form method="post" action="sidebar.php" onsubmit="return false;">
		<input type="text" name="sb_desc_name" id="sb_desc_name" value="'.i18n::translate('Search').'" />';
		$out .= '</form><div id="sb_desc_content">';

		if ($this->controller) {
			$root = null;
			if ($this->controller->pid) {
				$root = Person::getInstance($this->controller->pid);
			}
			else if ($this->controller->famid) {
				$fam = Family::getInstance($this->controller->famid);
				if ($fam) $root = $fam->getHusband();
				if (!$root) $root = $fam->getWife(); 
			}
			if ($root!=null) {
				$out .= '<ul>';
				$out .= $this->getPersonLi($root, 1);
				$out .= '</ul>';
			}
		}
		$out .= '</div>';
		return $out;
	}
	
	public function getPersonLi(&$person, $generations=0) {
		global $WT_IMAGE_DIR, $WT_IMAGES;
		$out = '';
		$out .= '<li id="sb_desc_'.$person->getXref().'" class="sb_desc_indi_li"><a href="sidebar.php?sb_action=descendancy&amp;pid='.$person->getXref().'" title="'.$person->getXref().'" class="sb_desc_indi">';
		if ($generations>0) $out .= '<img src="'.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].'" border="0" class="plusminus" />';
		else $out .= '<img src="'.$WT_IMAGE_DIR."/".$WT_IMAGES['plus']['other'].'" border="0" class="plusminus" />';
		$out .= $person->getSexImage().' '.$person->getListName().' ';
		if ($person->canDisplayDetails()) {
			$bd = $person->getBirthDeathYears(false,'');
			if (!empty($bd)) $out .= PrintReady(' ('.$bd.')');
		}
		$out .= '</a> <a href="'.encode_url($person->getLinkUrl()).'"><img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['indi']['button'].'" border="0" alt="indi" /></a>';
		if ($generations>0) {
			$out .= '<div class="desc_tree_div_visible">';
			$out .= $this->loadSpouses($person->getXref());
			$out .= '</div><script type="text/javascript">dloadedNames["'.$person->getXref().'"]=2;</script>';
		}else {
			$out .= '<div class="desc_tree_div">';
			$out .= '</div>';
		}
		$out .= '</li>';
		return $out;
	}
	
	public function getFamilyLi(&$family, &$person, $generations=0) {
		global $WT_IMAGE_DIR, $WT_IMAGES;
		$out = '';
		$out .= '<li id="sb_desc_'.$family->getXref().'" class="sb_desc_indi_li"><a href="sidebar.php?sb_action=descendancy&amp;famid='.$family->getXref().'" title="'.$family->getXref().'" class="sb_desc_indi">';
		$out .= '<img src="'.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].'" border="0" class="plusminus" />';
		$out .= $person->getSexImage().$person->getListName();
		
		$marryear = $family->getMarriageYear();
		if (!empty($marryear)) {
			$out .= ' ('.translate_fact('MARR').' '.$marryear.')';
		}
		$out .= '</a> <a href="'.encode_url($person->getLinkUrl()).'"><img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['indi']['button'].'" border="0" alt="indi" /></a>';
		$out .= '<a href="'.encode_url($family->getLinkUrl()).'"><img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['family']['button'].'" border="0" alt="family" /></a>';
		$out .= '<div class="desc_tree_div_visible">';
		$out .= $this->loadChildren($family->getXref(), $generations);
		$out .= '</div><script type="text/javascript">dloadedNames["'.$family->getXref().'"]=2;</script>';
		$out .= '</li>';
		return $out;
	}

	public function search($query) {
		global $TBLPREFIX, $WT_IMAGES, $WT_IMAGE_DIR;
		if (strlen($query)<2) return '';
		$sql=
		"SELECT ? AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
		" FROM {$TBLPREFIX}individuals, {$TBLPREFIX}name".
		" WHERE (i_id ".WT_DB::$LIKE." ? OR n_sort ".WT_DB::$LIKE." ?)".
		" AND i_id=n_id AND i_file=n_file AND i_file=?".
		" ORDER BY n_sort";
		$rows=
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array('INDI', "%{$query}%", "%{$query}%", WT_GED_ID))
		->fetchAll(PDO::FETCH_ASSOC);

		$out = '<ul>';
		$private_count = 0;
		foreach ($rows as $row) {
			$person=Person::getInstance($row);
			if ($person->canDisplayName()) {
				$out .= $this->getPersonLi($person);
			}
			else $private_count++;
		}
		if ($private_count>0) $out .= '<li>'.i18n::translate('Private').' ('.$private_count.')</li>';
		$out .= '</ul>';
		return $out;
	}
	
	public function loadSpouses($pid, $generations=0) {
		$out = '<ul>';
		$person = Person::getInstance($pid);
		if ($person->canDisplayDetails()) {
			$families = $person->getSpouseFamilies();
			foreach($families as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$out .= $this->getFamilyLi($family, $spouse, $generations-1);
				}
			}
		}
		$out .= "</ul>";
		return $out;
	}
	
	public function loadChildren($famid, $generations=0) {
		$out = '<ul>';
		$family = Family::getInstance($famid);
		if ($family->canDisplayDetails()) {
			$children = $family->getChildren();
			if (count($children)>0) {
				$private = 0;
				foreach($children as $child) {
					if ($child->canDisplayName()) $out .= $this->getPersonLi($child, $generations-1);
					else $private++;
				}
				if ($private>0) $out .= '<li class="sb_desc_indi_li">'.i18n::translate('Private').' ('.$private.')</li>';
			}
			else {
				$out .= "No children";
			}
		}
		$out .= "</ul>";
		return $out;
	}
}
