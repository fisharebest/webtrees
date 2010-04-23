<?php
/**
* List branches by surname
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
* @subpackage Lists
* @version $Id$
*/

define('WT_SCRIPT_NAME', 'branches.php');
require './includes/session.php';

//-- const
$fact='MARR';
define('WT_ICON_RINGS', '<img src="images/small/rings.gif" alt="'.i18n::translate('MARR').'" title="'.i18n::translate('MARR').'" />');
define('WT_ICON_BRANCHES', "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["patriarch"]["small"]."\" alt=\"\" align=\"middle\" />");

//-- args
$surn = safe_GET('surn', '[^<>&%{};]*');
$surn = utf8_strtoupper($surn);
$soundex_std = safe_GET_bool('soundex_std');
$soundex_dm = safe_GET_bool('soundex_dm');
$ged = safe_GET('ged');
if (empty($ged)) {
	$ged = $GEDCOM;
}

//-- rootid
$rootid = "";
if (WT_USER_ID) {
	$rootid = WT_USER_ROOT_ID;
	if (empty($_SESSION['user_ancestors'])	|| $_SESSION['user_ancestors'][1]!==$rootid) {
		unset($_SESSION['user_ancestors']);
		load_ancestors_array($rootid);
	}
}

//-- random surname
if ($surn=='*') {
	$surn = array_rand(get_indilist_surns("", "", false, true, WT_GED_ID));
}

//-- form
print_header(i18n::translate('Branches')." - ".$surn);
if ($ENABLE_AUTOCOMPLETE) {
	require WT_ROOT.'/js/autocomplete.js.htm';
}
?>
<form name="surnlist" id="surnlist" action="?">
	<table class="center facts_table width50">
		<tr>
			<td class="descriptionbox <?php echo $TEXT_DIRECTION; ?>">
				<?php echo i18n::translate('SURN'), help_link('surname'); ?></td>
			<td class="optionbox <?php echo $TEXT_DIRECTION; ?>">
				<input type="text" name="surn" id="SURN" value="<?php echo $surn?>" />
				<input type="hidden" name="ged" id="ged" value="<?php echo $ged?>" />
				<input type="submit" value="<?php echo i18n::translate('View'); ?>" />
				<input type="submit" value="<?php echo i18n::translate('Random surname'); ?>" onclick="document.surnlist.surn.value='*';" />
				<p class="details1">
					<?php echo i18n::translate('Search the way you think the name is written (Soundex)'), help_link('soundex_search'); ?><br />
					<input type="checkbox" name="soundex_std" id="soundex_std" value="1" <?php if ($soundex_std) echo " checked=\"checked\"" ?> />
					<label for="soundex_std"><?php echo i18n::translate('Basic')?></label>
					<input type="checkbox" name="soundex_dm" id="soundex_dm" value="1" <?php if ($soundex_dm) echo " checked=\"checked\"" ?> />
					<label for="soundex_dm"><?php echo i18n::translate('Daitch-Mokotoff')?></label>
				</p>
			</td>
		</tr>
	</table>
</form>
<?php
//-- results
if ($surn) {
	$surn_script = utf8_script($surn);
	echo "<fieldset><legend>", WT_ICON_BRANCHES, " ", PrintReady($surn), "</legend>";
	$indis = indis_array($surn, $soundex_std, $soundex_dm);
	echo "<ol>";
	foreach ($indis as $k=>$person) {
		$famc = $person->getPrimaryChildFamily();
		if (!$famc || (!array_key_exists($famc->getHusbId(), $indis)) && !array_key_exists($famc->getWifeId(), $indis)) {
			print_fams($person);
		}
	}
	echo "</ol>";
	echo "</fieldset>";
	if ($rootid) {
		$person = Person::getInstance($rootid);
		echo "<p class=\"center\">", i18n::translate('Pedigree Chart Root Person'), " : <a title=\"", $person->getXref(), "\" href=\"{$person->getLinkUrl()}\">{$person->getFullName()}</a>";
		echo "<br />".i18n::translate('Direct line ancestors-ancestors')." : ", count($_SESSION['user_ancestors']), "</p>";
	}
}
print_footer();

function print_fams($person, $famid=null) {
	global $UNKNOWN_NN, $PEDI_CODES, $PEDI_CODES_F, $PEDI_CODES_M, $surn, $surn_script, $TEXT_DIRECTION;
	// select person name according to searched surname
	$person_name = "";
	foreach ($person->getAllNames() as $n=>$name) {
		list($surn1) = explode(", ", $name['list']);
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
		echo "<span title=\"", PrintReady(strip_tags($person->getFullName())), "\">", $person->getSexImage(), "...</span>";
		return;
	}
	$person_script = utf8_script($person_name);
	// current indi
	echo "<li>";
	$class = "";
	$sosa = @array_search($person->getXref(), $_SESSION['user_ancestors']);
	if ($sosa) {
		$class = "search_hit";
		$sosa = "<a dir=$TEXT_DIRECTION target=\"_blank\" class=\"details1 {$person->getBoxStyle()}\" title=\"Sosa\" href=\"relationship.php?pid2=".WT_USER_ROOT_ID."&pid1=".$person->getXref()."\">&nbsp;{$sosa}&nbsp;</a>".sosa_gen($sosa);
	}
	$current = $person->getSexImage().
		"<a target=\"_blank\" class=\"{$class}\" title=\"".$person->getXref()."\" href=\"{$person->getLinkUrl()}\">".PrintReady($person_name)."</a> ".
		$person->getBirthDeathYears()." {$sosa}"; 
	if ($famid && $person->getChildFamilyPedigree($famid)) {
		$sex = $person->getSex();
		if ($sex=="F" && isset($PEDI_CODES[$pedi]))			$label = $PEDI_CODES_F[$pedi];
		else if ($sex=="M" && isset($PEDI_CODES[$pedi]))	$label = $PEDI_CODES_M[$pedi];
		else if (isset($PEDI_CODES[$pedi]))					$label = $PEDI_CODES[$pedi];
		$current = "<span class='red'>".$label."</span> ".$current;
	}
	// spouses and children
	if (count($person->getSpouseFamilies())<1) {
		echo $current;
	}
	foreach ($person->getSpouseFamilies() as $f=>$family) {
		$txt = $current;
		$spouse = $family->getSpouse($person);
		if ($spouse) {
			$class = "";
			$sosa2 = @array_search($spouse->getXref(), $_SESSION['user_ancestors']);
			if ($sosa2) {
				$class = "search_hit";
				$sosa2 = "<a dir=$TEXT_DIRECTION target=\"_blank\" class=\"details1 {$spouse->getBoxStyle()}\" title=\"Sosa\" href=\"relationship.php?pid2=".WT_USER_ROOT_ID."&pid1=".$spouse->getXref()."\">&nbsp;{$sosa2}&nbsp;</a>".sosa_gen($sosa2);
			}
			if ($family->getMarriageYear()) {
				$txt .= "&nbsp;<span dir=$TEXT_DIRECTION class='details1' title=\"".strip_tags($family->getMarriageDate()->Display())."\">".WT_ICON_RINGS.$family->getMarriageYear()."</span>&nbsp;";
			}
			else if ($family->getMarriage()) {
				$txt .= "&nbsp;<span dir=$TEXT_DIRECTION class='details1' title=\"".i18n::translate('Yes')."\">".WT_ICON_RINGS."</span>&nbsp;";
			}
			$spouse_name = $spouse->getListName();
			foreach ($spouse->getAllNames() as $n=>$name) {
				if (utf8_script($name['list']) == $person_script) {
					$spouse_name = $name['list'];
					break;
				}
				//How can we use check_NN($names) or something else to replace the unknown unknown name from the page language to the language of the spouse's name?
				else if ($name['fullNN']=="@P.N. @N.N.") {
					$spouse_name = $UNKNOWN_NN[$person_script].", ".$UNKNOWN_NN[$person_script];
					break;
				}
			}
			list($surn2, $givn2) = explode(", ", $spouse_name.", x");
			$txt .= $spouse->getSexImage().
				"<a target=\"_blank\" class=\"{$class}\" title=\"".$family->getXref()."\" href=\"{$family->getLinkUrl()}\">".PrintReady($givn2)."</a> ".
				"<a class=\"{$class}\" title=\"{$surn2}\" href=\"javascript:document.surnlist.surn.value='{$surn2}';document.surnlist.submit();\">".PrintReady($surn2)."</a> ".
				$spouse->getBirthDeathYears()." {$sosa2}";
		}
		echo $txt;
		echo "<ol>";
		foreach ($family->getChildren() as $c=>$child) {
			print_fams($child, $family->getXref());
		}
		echo "</ol>";
	}
	echo "</li>";
}

function load_ancestors_array($xref, $sosa=1) {
	if ($xref) {
		$_SESSION['user_ancestors'][$sosa] = $xref;
		$person = Person::getInstance($xref);
		$famc = $person->getPrimaryChildFamily();
		if ($famc) {
			load_ancestors_array($famc->getHusbId(), $sosa*2);
			load_ancestors_array($famc->getWifeId(), $sosa*2+1);
		}
	}
}

function indis_array($surn, $soundex_std, $soundex_dm) {
	global $TBLPREFIX;
	$sql=
		"SELECT DISTINCT n_id".
		" FROM {$TBLPREFIX}name".
		" WHERE n_file=?".
		" AND n_type!=?".
		" AND (n_surn=? OR n_surname=?";
	$args=array(WT_GED_ID, '_MARNM', $surn, $surn);
	if ($soundex_std) {
		$sql .= " OR n_soundex_surn_std=?";
		$args[]=soundex_std($surn);
	}
	if ($soundex_dm) {
		$sql .= " OR n_soundex_surn_dm=?";
		$args[]=soundex_dm($surn);
	}
	$sql .= ") ORDER BY n_sort";
	$rows=
		WT_DB::prepare($sql)
		->execute($args)
		->fetchAll();
//	var_dump($sql); var_dump($rows);
	$data=array();
	foreach ($rows as $row) {
		$data[$row->n_id]=Person::getInstance($row->n_id);
	}
	return $data;
}

function sosa_gen($sosa) {
	$gen = (int)log($sosa, 2)+1;
	return "<sup title=\"".i18n::translate('Generations')."\">{$gen}</sup>";
}
?>
