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

require_once(WT_ROOT."includes/classes/class_module.php");

class individuals_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Individuals');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Adds a sidebar which allows for easy navigation of individuals in a list format.');
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
		$alpha   =safe_GET('alpha'); // All surnames beginning with this letter where "@"=unknown and ","=none
		$surname =safe_GET('surname', '[^<>&%{};]*'); // All indis with this surname.  NB - allow ' and "
		$search   =safe_GET('search');

		$last = array('alpha'=>$alpha, 'surname'=>$surname, 'search'=>$search);
		$_SESSION['sb_individuals_last'] = $last;

		if (!empty($search)) return $this->search($search);
		else if (empty($surname)) return $this->getAlphaSurnames($alpha, $surname);
		else return $this->getSurnameIndis($alpha, $surname);
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $SHOW_MARRIED_NAMES;
		global $WT_IMAGE_DIR, $WT_IMAGES;

		// Fetch a list of the initial letters of all surnames in the database
		$initials=get_indilist_salpha($SHOW_MARRIED_NAMES, false, WT_GED_ID);

		$out = '<script type="text/javascript">
		<!--
		var loadedNames = new Array();
		
		function searchQ() {
			var query = jQuery("#sb_indi_name").attr("value");
			if (query.length>1) {
				jQuery("#sb_indi_content").load("sidebar.php?sb_action=individuals&search="+query);
			}
		}
		
		jQuery(document).ready(function(){
			jQuery("#sb_indi_name").focus(function(){this.select();});
			jQuery("#sb_indi_name").blur(function(){if (this.value=="") this.value="'.i18n::translate('Search').'";});
			var timerid = null;
			jQuery("#sb_indi_name").keyup(function(e) {
				if (timerid) window.clearTimeout(timerid);
				timerid = window.setTimeout("searchQ()", 500);
			});
			jQuery(".sb_indi_letter").live("click", function() {
				jQuery("#sb_indi_content").load(this.href);
				return false;
			});
			jQuery(".sb_indi_surname").live("click", function() {
				var surname = jQuery(this).attr("title");
				var alpha = jQuery(this).attr("alt");
				
				if (!loadedNames[surname]) {
					jQuery.ajax({
					  url: "sidebar.php?sb_action=individuals&alpha="+alpha+"&surname="+surname,
					  cache: false,
					  success: function(html){
					    jQuery("#sb_indi_"+surname+" div").html(html);
					    jQuery("#sb_indi_"+surname+" div").show();
					    jQuery("#sb_indi_"+surname).css("list-style-image", "url('.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].')");
					    loadedNames[surname]=2;
					  }
					});
				}
				else if (loadedNames[surname]==1) {
					loadedNames[surname]=2;
					jQuery("#sb_indi_"+surname+" div").show();
					jQuery("#sb_indi_"+surname).css("list-style-image", "url('.$WT_IMAGE_DIR."/".$WT_IMAGES['minus']['other'].')");
				}
				else {
					loadedNames[surname]=1;
					jQuery("#sb_indi_"+surname+" div").hide();
					jQuery("#sb_indi_"+surname).css("list-style-image", "url('.$WT_IMAGE_DIR."/".$WT_IMAGES['plus']['other'].')");
				}
				return false;
			});
		});
		//-->
		</script>
		<form method="post" action="sidebar.php" onsubmit="return false;">
		<input type="text" name="sb_indi_name" id="sb_indi_name" value="'.i18n::translate('Search').'" />
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
			$html='<a href="sidebar.php?sb_action=individuals&amp;alpha='.urlencode($letter).'" class="sb_indi_letter">'.PrintReady($html).'</a>';
			$out .= $html." ";
		}

		$out .= '</p>';
		$out .= '<div id="sb_indi_content">';
		
		if (isset($_SESSION['sb_individuals_last'])) {
			$last = $_SESSION['sb_individuals_last'];
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
		$surns=get_indilist_surns('', $alpha, $SHOW_MARRIED_NAMES, false, WT_GED_ID);
		$out = '<ul>';
		foreach($surns as $surname=>$surns) {
			$out .= '<li id="sb_indi_'.$surname.'" class="sb_indi_surname_li"><a href="'.$surname.'" title="'.$surname.'" alt="'.$alpha.'" class="sb_indi_surname">'.$surname.'</a>';
			if (!empty($surname1) && $surname1==$surname) {
				$out .= '<div class="name_tree_div_visible">';
				$out .= $this->getSurnameIndis($alpha, $surname1);
				$out .= '</div>';
			}
			else
				$out .= '<div class="name_tree_div"></div>'; 
			$out .= '</li>';
		}
		$out .= '</ul>';
		return $out;
	}

	public function getSurnameIndis($alpha, $surname) {
		global $SHOW_MARRIED_NAMES;
		$indis=get_indilist_indis($surname, $alpha, '', $SHOW_MARRIED_NAMES, false, WT_GED_ID);
		$out = '<ul>';
		$private_count = 0;
		foreach($indis as $person) {
			if ($person->canDisplayName()) {
				$out .= '<li><a href="'.encode_url($person->getLinkUrl()).'">'.$person->getSexImage().' '.$person->getListName().' ';
				if ($person->canDisplayDetails()) {
					$bd = $person->getBirthDeathYears(false,'');
					if (!empty($bd)) $out .= PrintReady(' ('.$bd.')');
				}
				$out .= '</a></li>';
			}
			else $private_count++;
		}
		if ($private_count>0) $out .= '<li>'.i18n::translate('Private').' ('.$private_count.')</li>';
		$out .= '</ul>';
		return $out;
	}

	public function search($query) {
		global $TBLPREFIX;
		if (strlen($query)<2) return '';
		$sql=
		"SELECT ? AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
		" FROM {$TBLPREFIX}individuals, {$TBLPREFIX}name".
		" WHERE (i_id LIKE ? OR n_sort LIKE ?)".
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
				$out .= '<li><a href="'.encode_url($person->getLinkUrl()).'">'.$person->getSexImage().' '.$person->getListName().' ';
				if ($person->canDisplayDetails()) {
					$bd = $person->getBirthDeathYears(false,'');
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
