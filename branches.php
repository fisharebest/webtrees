<?php
// List branches by surname
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'branches.php');
require './includes/session.php';

//-- const
$fact='MARR';
define('WT_ICON_RINGS', '<img src="'.$WT_IMAGES['rings'].'" alt="'.WT_Gedcom_Tag::getLabel('MARR').'" title="'.WT_Gedcom_Tag::getLabel('MARR').'" />');
define('WT_ICON_BRANCHES', '<img src="'.$WT_IMAGES['patriarch'].'" alt="" align="middle" />');

//-- args
$surn = safe_GET('surname', '[^<>&%{};]*');
$soundex_std = safe_GET_bool('soundex_std');
$soundex_dm = safe_GET_bool('soundex_dm');
$ged = safe_GET('ged');
if (empty($ged)) {
	$ged = $GEDCOM;
}

$user_ancestors=array();
if (WT_USER_GEDCOM_ID) {
	load_ancestors_array(WT_Person::getInstance(WT_USER_GEDCOM_ID), 1);
}

$controller=new WT_Controller_Base();
if ($surn) {
	$controller->setPageTitle(/* I18N: %s is a surname */ WT_I18N::translate('Branches of the %s family', $surn));
} else {
	$controller->setPageTitle(WT_I18N::translate('Branches'));
}
$controller->pageHeader();

if ($ENABLE_AUTOCOMPLETE) {
	require WT_ROOT.'/js/autocomplete.js.htm';
}

echo '<div id="branches-page">
	<form name="surnlist" id="surnlist" action="?">
		<table class="facts_table width50">
			<tr>
				<td class="descriptionbox">', WT_Gedcom_Tag::getLabel('SURN'), help_link('surname'), '</td>
				<td class="optionbox">
					<input type="text" name="surname" id="SURN" value="',$surn, '" />
					<input type="hidden" name="ged" id="ged" value="', $ged, '" />
					<input type="submit" value="', WT_I18N::translate('View'), '" />
					<p>', WT_I18N::translate('Phonetic search'), '</p>
					<p>
						<input type="checkbox" name="soundex_std" id="soundex_std" value="1"';
							if ($soundex_std) echo ' checked="checked"';
						echo ' />
						<label for="soundex_std">', WT_I18N::translate('Russell'), '</label>
						<input type="checkbox" name="soundex_dm" id="soundex_dm" value="1"';
							if ($soundex_dm) echo ' checked="checked"';
						echo ' />
						<label for="soundex_dm">', WT_I18N::translate('Daitch-Mokotoff'), '</label>
					</p>
				</td>
			</tr>
		</table>
	</form>';
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.treeview.js')
		->addInlineJavaScript('
			jQuery(document).ready(function() {						
				jQuery("#branch-list").treeview({
					collapsed: true,
					animated: "slow",
					control:"#treecontrol"
				});
				jQuery("#branch-list").css("visibility", "visible");
				jQuery(".loading-image").css("display", "none");
			});
		');
	//-- results
	if ($surn) {
		$surn_script = utf8_script($surn);
		echo '<h2>', $controller->getPageTitle(), '</h2>
			<div id="treecontrol">
				<a href="#">', WT_I18N::translate('Collapse all'), '</a> | <a href="#">', WT_I18N::translate('Expand all'), '</a>
			</div>
			<div class="loading-image">&nbsp;</div>';
		$indis = indis_array($surn, $soundex_std, $soundex_dm);
		usort($indis, array('WT_Person', 'CompareBirtDate'));
		echo '<ul id="branch-list">';
		foreach ($indis as $person) {
			$famc = $person->getPrimaryChildFamily();
			// Don't show INDIs with parents in the list, as they will be shown twice.
			if ($famc) {
				foreach ($famc->getSpouses() as $parent) {
					if (in_array($parent, $indis, true)) {
						continue 2;
					}
				}
			}
			print_fams($person);
		}
		echo '</ul>';
		echo '</fieldset>';
	}
echo '</div>'; // close branches-page

function print_fams($person, $famid=null) {
	global $UNKNOWN_NN, $surn, $surn_script, $user_ancestors;
	// select person name according to searched surname
	$person_name = "";
	foreach ($person->getAllNames() as $n=>$name) {
		list($surn1) = explode(",", $name['sort']);
		if (stripos($surn1, $surn)===false
			&& stripos($surn, $surn1)===false
			&& soundex_std($surn1)!==soundex_std($surn)
			&& soundex_dm($surn1)!==soundex_dm($surn)
			) {
			continue;
		}
		if (utf8_script($surn1)!==$surn_script) {
			continue;
		}
		$person_name = $name['full'];
		break;
	}
	if (empty($person_name)) {
		echo '<li title="', strip_tags($person->getFullName()), '">', $person->getSexImage(), '…</li>';
		return;
	}
	$person_script = utf8_script($person_name);
	// current indi
	echo '<li>';
	$class = '';
	$sosa = array_search($person->getXref(), $user_ancestors);
	if ($sosa) {
		$class = 'search_hit';
		$sosa = '<a target="_blank" dir="ltr" class="details1 '.$person->getBoxStyle().'" title="'.WT_I18N::translate('Sosa').'" href="relationship.php?pid2='.WT_USER_ROOT_ID.'&amp;pid1='.$person->getXref().'">&nbsp;'.$sosa.'&nbsp;</a>'.sosa_gen($sosa);
	}
	$current = $person->getSexImage().'<a target="_blank" class="'.$class.'" href="'.$person->getHtmlUrl().'">'.PrintReady($person_name).'</a> '.$person->getLifeSpan().' '.$sosa;
	if ($famid && $person->getChildFamilyPedigree($famid)) {
		$famcrec = get_sub_record(1, '1 FAMC @'.$famid.'@', $person->getGedcomRecord());
		$pedi = get_gedcom_value('PEDI', 2, $famcrec, '', false);
		if ($pedi) {
			$label = WT_Gedcom_Code_Pedi::getValue($pedi, $person);
		}
		$current .= '<p class="branches red">'.$label.'</p>';
	}
	// spouses and children
	if (count($person->getSpouseFamilies())<1) {
		echo $current;
	}
	foreach ($person->getSpouseFamilies() as $family) {
		$txt = $current;
		$spouse = $family->getSpouse($person);
		if ($spouse) {
			$class = '';
			$sosa2 = array_search($spouse->getXref(), $user_ancestors);
			if ($sosa2) {
				$class = 'search_hit';
				$sosa2 = '<a target="_blank" dir="ltr" class="details1 '.$spouse->getBoxStyle().'" title="'.WT_I18N::translate('Sosa').'" href="relationship.php?pid2='.WT_USER_ROOT_ID.'&amp;pid1='.$spouse->getXref().'">&nbsp;'.$sosa2.'&nbsp;</a>'.sosa_gen($sosa2);
			}
			if ($family->getMarriageYear()) {
				$txt .= ' <p class="branches details1">';
				$txt .= '<a href="'.$family->getHtmlUrl().'" title="'.strip_tags($family->getMarriageDate()->Display()).'">'.WT_ICON_RINGS.$family->getMarriageYear().'</a></p>&nbsp;';
			}
			else if ($family->getMarriage()) {
				$txt .= ' <p class="branches details1">';
				$txt .= '<a href="'.$family->getHtmlUrl().'" title="'.WT_I18N::translate('yes').'">'.WT_ICON_RINGS.'</a></p>&nbsp;';
			}
		$txt .=
			$spouse->getSexImage().
			'<a class="'.$class.'" href="'.$spouse->getHtmlUrl().'">'.$spouse->getFullName().' </a>'.$spouse->getLifeSpan().' '.$sosa2;
		}
		echo $txt;
		echo '<ul>';
		foreach ($family->getChildren() as $c=>$child) {
			print_fams($child, $family->getXref());
		}
		echo '</ul>';
	}
	echo '</li>';
}

function load_ancestors_array($person, $sosa=1) {
	global $user_ancestors;
	if ($person) {
		$user_ancestors[$sosa]=$person->getXref();
		foreach ($person->getChildFamilies() as $family) {
			foreach ($family->getSpouses() as $parent) {
				load_ancestors_array($parent, $sosa*2+($parent->getSex()=='F'));
			}
		}
	}
}

function indis_array($surn, $soundex_std, $soundex_dm) {
	$sql=
		"SELECT DISTINCT n_id".
		" FROM `##name`".
		" WHERE n_file=?".
		" AND n_type!=?".
		" AND (n_surn=? OR n_surname=?";
	$args=array(WT_GED_ID, '_MARNM', $surn, $surn);
	if ($soundex_std) {
		$sql .= " OR n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
		$args[]=soundex_std($surn);
	}
	if ($soundex_dm) {
		$sql .= " OR n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
		$args[]=soundex_dm($surn);
	}
	$sql .= ')';
	$rows=
		WT_DB::prepare($sql)
		->execute($args)
		->fetchAll();
	$data=array();
	foreach ($rows as $row) {
		$data[]=WT_Person::getInstance($row->n_id);
	}
	return $data;
}

function sosa_gen($sosa) {
	$gen = (int)log($sosa, 2)+1;
	return '<sup title="'.WT_I18N::translate('Generation').'">'.$gen.'</sup>';
}
