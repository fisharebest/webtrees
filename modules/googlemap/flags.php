<?php
/**
 * Interface to edit place locations
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
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
 * @subpackage Edit
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'modules/googlemap/defaultconfig.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$countries=get_all_countries();
$action=safe_REQUEST($_REQUEST, 'action');

if (isset($_REQUEST['countrySelected'])) $countrySelected = $_REQUEST['countrySelected'];
if (!isset($countrySelected)) $countrySelected="Countries";
if (isset($_REQUEST['stateSelected'])) $stateSelected = $_REQUEST['stateSelected'];
if (!isset($stateSelected)) $stateSelected="States";

print_simple_header(WT_I18N::translate('Select flag'));

if (!is_dir('./modules/googlemap/places/flags/')) {
	echo '<br /><div class="optionbox wrap"><b>', WT_I18N::translate('The flags directory doesn\'t exist'), '</b><br /><br />', WT_I18N::translate('To make the flags work make sure that %s directory exists and contains flags files.', '<b>./modules/googlemap/places/flags/</b>'), '</div><br />';
	echo '<div class="center"><a href="javascript:;" onclick="window.close();">', WT_I18N::translate('Close Window'), "</a></div><br />\n";
	print_simple_footer();
	exit;
}
$country = array();
$rep = opendir('./modules/googlemap/places/flags/');
while ($file = readdir($rep)) {
	if (stristr($file, ".gif")) {
		$country[] = substr($file, 0, strlen($file)-4);
	}
}
closedir($rep);
sort($country);

if ($countrySelected == "Countries") {
	$flags = $country;
}
else {
	$flags = array();
	$rep = opendir('./modules/googlemap/places/'.$countrySelected.'/flags/');
	while ($file = readdir($rep)) {
		if (stristr($file, ".gif")) {
			$flags[] = substr($file, 0, strlen($file)-4);
		}
	}
	closedir($rep);
	sort($flags);
}
$flags_s = array();
if ($stateSelected != "States" && is_dir('./modules/googlemap/places/'.$countrySelected.'/flags/'.$stateSelected.'/')) {
	$rep = opendir('./modules/googlemap/places/'.$countrySelected.'/flags/'.$stateSelected.'/');
	while ($file = readdir($rep)) {
		if (stristr($file, ".gif")) {
			$flags_s[] = substr($file, 0, strlen($file)-4);
		}
	}
	closedir($rep);
	sort($flags_s);
}

if ($action == "ChangeFlag") {
?>
	<script type="text/javascript">
	<!--
		function edit_close() {
<?php if ($_POST["selcountry"] == "Countries") { ?>
			window.opener.document.editplaces.icon.value = "modules/googlemap/places/flags/<?php echo $flags[$_POST["FLAGS"]]; ?>.gif";
			window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"modules/googlemap/places/flags/<?php echo $country[$_POST["FLAGS"]]; ?>.gif\">&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
<?php } else if ($_POST["selstate"] != "States"){ ?>
			window.opener.document.editplaces.icon.value = "modules/googlemap/places/<?php echo $countrySelected, "/flags/", $_POST["selstate"], "/", $flags_s[$_POST["FLAGS"]]; ?>.gif";
			window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"modules/googlemap/places/<?php echo $countrySelected, "/flags/", $_POST["selstate"], "/", $flags_s[$_POST["FLAGS"]]; ?>.gif\">&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
<?php } else { ?>
			window.opener.document.editplaces.icon.value = "modules/googlemap/places/<?php echo $countrySelected, "/flags/", $flags[$_POST["FLAGS"]]; ?>.gif";
			window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"modules/googlemap/places/<?php echo $countrySelected, "/flags/", $flags[$_POST["FLAGS"]]; ?>.gif\">&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
<?php } ?>
			window.close();
		}
	//-->
	</script>
<?php
	// autoclose window when update successful unless debug on
	if (!WT_DEBUG) {
		echo "\n<script type=\"text/javascript\">\n<!--\nedit_close();\n//-->\n</script>";
	}
	echo "<div class=\"center\"><a href=\"javascript:;\" onclick=\"edit_close();\">", WT_I18N::translate('Close Window'), "</a></div><br />\n";
	print_simple_footer();
	exit;
}
else {
?>
<script type="text/javascript">
<!--
	function enableButtons() {
		document.flags.save1.disabled = "";
		document.flags.save2.disabled = "";
	}

	function selectCountry() {
		if (document.flags.COUNTRYSELECT.value == "Countries") {
			window.location="module.php?mod=googlemap&mod_action=flags";
		}
		else if (document.flags.STATESELECT.value != "States") {
			window.location="module.php?mod=googlemap&mod_action=flags&countrySelected=" + document.flags.COUNTRYSELECT.value + "&stateSelected=" + document.flags.STATESELECT.value;
		}
		else {
			window.location="module.php?mod=googlemap&mod_action=flags&countrySelected=" + document.flags.COUNTRYSELECT.value;
		}
	}

	function edit_close() {
		window.close();
	}

//-->
</script>
<?php
}
	if (!isset($_SESSION['flags_countrylist'])) {
		$countryList = array();
		$placesDir = scandir('./modules/googlemap/places/');
		for ($i = 0; $i < count($country); $i++) {
			if (count(preg_grep('/'.$country[$i].'/', $placesDir)) != 0) {
				$rep = opendir('./modules/googlemap/places/'.$country[$i].'/');
				while ($file = readdir($rep)) {
					if (stristr($file, "flags")) {
						$countryList[$country[$i]] = $countries[$country[$i]];
					}
				}
				closedir($rep);
			}
		}
		asort($countryList);
		$_SESSION['flags_countrylist'] = serialize($countryList);
	} else {
		$countryList = unserialize($_SESSION['flags_countrylist']);
	}
	$stateList = array();
	if ($countrySelected != "Countries") {
		$placesDir = scandir('./modules/googlemap/places/'.$countrySelected.'/flags/');
		for ($i = 0; $i < count($flags); $i++) {
			if (in_array($flags[$i], $placesDir)) {
				$rep = opendir('./modules/googlemap/places/'.$countrySelected.'/flags/'.$flags[$i].'/');
				while ($file = readdir($rep)) {
					$stateList[$flags[$i]] = $flags[$i];
				}
				closedir($rep);
			}
		}
		asort($stateList);
	}
?>

<form method="post" id="flags" name="flags" action="module.php?mod=googlemap&mod_action=flags&countrySelected=<?php echo $countrySelected; ?>&stateSelected=<?php echo $stateSelected; ?>">
	<input type="hidden" name="action" value="ChangeFlag" />
	<input type="hidden" name="selcountry" value="<?php echo $countrySelected; ?>" />
	<input type="hidden" name="selstate" value="<?php echo $stateSelected; ?>" />
	<input id="savebutton" name="save1" type="submit" disabled="true" value="<?php echo WT_I18N::translate('Save'); ?>" /><br />
	<table class="facts_table">
		<tr>
			<td class="optionbox" colspan="4">
				<?php echo help_link('PLE_FLAGS','googlemap'); ?>
				<select name="COUNTRYSELECT" dir="ltr" onchange="selectCountry()">
					<option value="Countries"><?php echo WT_I18N::translate('Countries'); ?></option>
					<?php foreach ($countryList as $country_key=>$country_name) {
						echo "<option value=\"", $country_key, "\"";
						if ($countrySelected == $country_key) echo " selected=\"selected\" ";
						echo ">", $country_name, "</option>\n";
					} ?>
				</select>
			</td>
		</tr>
		<tr>
<?php
		$j = 1;
		for ($i = 0; $i < count($flags); $i++) {
			if ($countrySelected == "Countries") {
				$tempstr = "<td><input type=\"radio\" dir=\"ltr\" name=\"FLAGS\" value=\"".$i."\" onchange=\"enableButtons();\"><img src=\"modules/googlemap/places/flags/".$flags[$i].".gif\" alt=\"".$flags[$i]."\"  title=\"";
				if ($flags[$i]!='blank') $tempstr.=$countries[$flags[$i]];
				else $tempstr.=$countries['???'];
				echo $tempstr, "\">&nbsp;&nbsp;", $flags[$i], "</input></td>\n";
			}
			else {
				echo "<td><input type=\"radio\" dir=\"ltr\" name=\"FLAGS\" value=\"", $i, "\" onchange=\"enableButtons();\"><img src=\"modules/googlemap/places/", $countrySelected, "/flags/", $flags[$i], ".gif\">&nbsp;&nbsp;", $flags[$i], "</input></td>\n";
			}
			if ($j == 4) {
				echo "</tr><tr>\n";
				$j = 0;
			}
			$j++;
		}
		echo "</tr><tr";
		if ($countrySelected == "Countries" || count($stateList)==0) {
			echo ' style=" visibility: hidden"';
		}
		echo ">";
?>
			<td class="optionbox" colspan="4">
				<?php echo help_link('PLE_FLAGS','googlemap'); ?>
				<select name="STATESELECT" dir="ltr" onchange="selectCountry()">
					<option value="States"><?php echo /* I18N: Part of a country, state/region/county */ WT_I18N::translate('Subdivision'); ?></option>
					<?php foreach ($stateList as $state_key=>$state_name) {
						echo "<option value=\"", $state_key, "\"";
						if ($stateSelected == $state_key) echo " selected=\"selected\" ";
						echo ">", $state_name, "</option>\n";
					} ?>
				</select>
			</td>
		</tr>
		<tr>
<?php
		$j = 1;
		for ($i = 0; $i < count($flags_s); $i++) {
			if ($stateSelected != "States") {
				echo "<td><input type=\"radio\" dir=\"ltr\" name=\"FLAGS\" value=\"", $i, "\" onchange=\"enableButtons();\"><img src=\"modules/googlemap/places/", $countrySelected, "/flags/", $stateSelected, "/", $flags_s[$i], ".gif\">&nbsp;&nbsp;", $flags_s[$i], "</input></td>\n";
			}
			if ($j == 4) {
				echo "</tr><tr>\n";
				$j = 0;
			}
			$j++;
		}
?>
		</tr>
	</table>
	<input id="savebutton" name="save2" type="submit" disabled="true" value="<?php echo WT_I18N::translate('Save'); ?>" /><br />
</form>
<?php
echo "<div class=\"center\"><a href=\"javascript:;\" onclick=\"edit_close();\">", WT_I18N::translate('Close Window'), "</a></div><br />\n";

print_simple_footer();
