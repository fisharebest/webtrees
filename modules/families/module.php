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

class families_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Families');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Adds a sidebar which allows for easy navigation of famlies in a list format.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 40;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		$alpha   =safe_GET('alpha'); // All surnames beginning with this letter where "@"=unknown and ","=none
		$surname =safe_GET('surname', '[^<>&%{};]*'); // All indis with this surname.  NB - allow ' and "
		$search   =safe_GET('search');

		$last = array('alpha'=>$alpha, 'surname'=>$surname, 'search'=>$search);
		$_SESSION['sb_families_last'] = $last;
		if (!empty($search)) return $this->search($search);
		else if (empty($surname)) return $this->getAlphaSurnames($alpha, $surname);
		else return $this->getSurnameFams($alpha, $surname);
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $SHOW_MARRIED_NAMES;
		global $WT_IMAGE_DIR, $WT_IMAGES;

		// Fetch a list of the initial letters of all surnames in the database
		$initials=get_indilist_salpha($SHOW_MARRIED_NAMES, false, WT_GED_ID);

		$out = '<script type="text/javascript">
		<!--
		var famloadedNames = new Array();
		
		function searchQ() {
			var query = jQuery("#sb_fam_name").attr("value");
			if (query.length>1) {
				jQuery("#sb_fam_content").load("sidebar.php?sb_action=families&search="+query);
			}
		}
		
		jQuery(document).ready(function(){
			jQuery("#sb_fam_name").focus(function(){this.select();});
			jQuery("#sb_fam_name").blur(function(){if (this.value=="") this.value="'.i18n::translate('Search').'";});
			var famtimerid = null;
			jQuery("#sb_fam_name").keyup(function(e) {
				if (famtimerid) window.clearTimeout(famtimerid);
				famtimerid = window.setTimeout("searchQ()", 500);
			});
			jQuery(".sb_fam_letter").live("click", function() {
				jQuery("#sb_fam_content").load(this.href);
				return false;
			});
			jQuery(".sb_fam_surname").live("click", function() {
				var surname = jQuery(this).attr("title");
				var alpha = jQuery(this).attr("alt");
				
				if (!famloadedNames[surname]) {
					jQuery.ajax({
					  url: "sidebar.php?sb_action=families&alpha="+alpha+"&surname="+surname,
					  cache: false,
					  success: function(html){
					    jQuery("#sb_fam_"+surname+" div").html(html);
					    jQuery("#sb_fam_"+surname+" div").show();
					    jQuery("#sb_fam_"+surname).css("list-style-image", "url('.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].')");
					    famloadedNames[surname]=2;
					  }
					});
				}
				else if (famloadedNames[surname]==1) {
					famloadedNames[surname]=2;
					jQuery("#sb_fam_"+surname+" div").show();
					jQuery("#sb_fam_"+surname).css("list-style-image", "url('.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].')");
				}
				else {
					famloadedNames[surname]=1;
					jQuery("#sb_fam_"+surname+" div").hide();
					jQuery("#sb_fam_"+surname).css("list-style-image", "url('.$WT_IMAGE_DIR."/".$WT_IMAGES['plus']['other'].')");
				}
				return false;
			});
		});
		//-->
		</script>
		<form method="post" action="sidebar.php" onsubmit="return false;">
		<input type="text" name="sb_fam_name" id="sb_fam_name" value="'.i18n::translate('Search').'" />
		<p>';
		foreach ($initials as $letter=>$count) {
			switch ($letter) {
				case '@':
					$html=i18n::translate('(unknown)');
					break;
				case ',':
					$html=i18n::translate('None');
					break;
				default:
					$html=$letter;
					break;
			}
			$html='<a href="sidebar.php?sb_action=families&amp;alpha='.urlencode($letter).'" class="sb_fam_letter">'.PrintReady($html).'</a>';
			$out .= $html." ";
		}

		$out .= '</p>';
		$out .= '<div id="sb_fam_content">';

		if (isset($_SESSION['sb_families_last'])) {
			$last = $_SESSION['sb_families_last'];
			$alpha = $last['alpha'];
			$search = $last['search'];
			$surname = $last['surname'];
			if (!empty($search)) $out.= $this->search($search);
			else if (!empty($alpha)) $out.= $this->getAlphaSurnames($alpha, $surname);
		}
		
		$out .= '</div></form>';
		return $out;
	}

	public function getAlphaSurnames($alpha, $surname1='') {
		global $SHOW_MARRIED_NAMES;
		$surns=get_famlist_surns('', $alpha, $SHOW_MARRIED_NAMES, WT_GED_ID);
		$out = '<ul>';
		foreach($surns as $surname=>$surns) {
			$out .= '<li id="sb_fam_'.$surname.'" class="sb_fam_surname_li"><a href="'.$surname.'" title="'.$surname.'" alt="'.$alpha.'" class="sb_fam_surname">'.$surname.'</a>';
			if (!empty($surname1) && $surname1==$surname) {
				$out .= '<div class="name_tree_div_visible">';
				$out .= $this->getSurnameFams($alpha, $surname1);
				$out .= '</div>';
			}
			else
				$out .= '<div class="name_tree_div"></div>'; 
			$out .= '</li>';
		}
		$out .= '</ul>';
		return $out;
	}

	public function getSurnameFams($alpha, $surname) {
		global $SHOW_MARRIED_NAMES;
		$families=get_famlist_fams($surname, $alpha, '', $SHOW_MARRIED_NAMES, WT_GED_ID);
		$out = '<ul>';
		$private_count = 0;
		foreach($families as $family) {
			if ($family->canDisplayName()) {
				$out .= '<li><a href="'.encode_url($family->getLinkUrl()).'">'.$family->getFullName().' ';
				if ($family->canDisplayDetails()) {
					$bd = $family->getMarriageYear();
					if (!empty($bd)) $out .= PrintReady(' ('.$bd.')');
				}
				$out .= '</a></li>';
			}
			else $private_count++;
		}
		if ($private_count>0) $out .= '<li>'.PrintReady(i18n::translate('Private').' ('.$private_count.')').'</li>';
		$out .= '</ul>';
		return $out;
	}

	public function search($query) {
		global $TBLPREFIX;
		if (strlen($query)<2) return '';

		//-- search for INDI names
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
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row['xref'];
		}

		$vars=array('FAM');
		if (empty($ids)) {
			//-- no match : search for FAM id
			$where = "f_id ".WT_DB::$LIKE." ?";
			$vars[]="%{$FILTER}%";
		} else {
			//-- search for spouses
			$qs=implode(',', array_fill(0, count($ids), '?'));
			$where = "(f_husb IN ($qs) OR f_wife IN ($qs))";
			$vars=array_merge($vars, $ids, $ids);
		}

		$sql="SELECT ? AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families WHERE {$where} AND f_file=?";
		$vars[]=WT_GED_ID;
		$rows=
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute($vars)
		->fetchAll(PDO::FETCH_ASSOC);

		$out = '<ul>';
		$private_count = 0;
		foreach ($rows as $row) {
			$family=Family::getInstance($row);
			if ($family->canDisplayName()) {
				$out .= '<li><a href="'.encode_url($family->getLinkUrl()).'">'.$family->getFullName().' ';
				if ($family->canDisplayDetails()) {
					$bd = $family->getMarriageYear();
					if (!empty($bd)) $out .= PrintReady(' ('.$bd.')');
				}
				$out .= '</a></li>';
			}
			else $private_count++;
		}
		if ($private_count>0) $out .= '<li>'.PrintReady(i18n::translate('Private').' ('.$private_count.')').'</li>';
		$out .= '</ul>';
		return $out;
	}
}
