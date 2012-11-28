<?php
// Interface to edit place locations
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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

require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$action=safe_REQUEST($_REQUEST, 'action');
if (isset($_REQUEST['placeid'])) $placeid = $_REQUEST['placeid'];
if (isset($_REQUEST['place_name'])) $place_name = $_REQUEST['place_name'];

$controller=new WT_Controller_Simple();
$controller
		->requireAdminLogin()
		->setPageTitle(WT_I18N::translate('Geographic data'))
		->addExternalJavascript(WT_STATIC_URL.'js/webtrees.js')
		->pageHeader();

echo '<link type="text/css" href ="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';

// Take a place id and find its place in the hierarchy
// Input: place ID
// Output: ordered array of id=>name values, starting with the Top Level
// e.g. array(0=>"Top Level", 16=>"England", 19=>"London", 217=>"Westminster");
// NB This function exists in both places.php and places_edit.php
function place_id_to_hierarchy($id) {
	$statement=
		WT_DB::prepare("SELECT pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=?");
	$arr=array();
	while ($id!=0) {
		$row=$statement->execute(array($id))->fetchOneRow();
		$arr=array($id=>$row->pl_place)+$arr;
		$id=$row->pl_parent_id;
	}
	return $arr;
}

// NB This function exists in both admin_places.php and places_edit.php
function getHighestIndex() {
	return (int)WT_DB::prepare("SELECT MAX(pl_id) FROM `##placelocation`")->fetchOne();
}

$where_am_i=place_id_to_hierarchy($placeid);
$level=count($where_am_i);
$link = 'module.php?mod=googlemap&amp;mod_action=admin_places&amp;parent='.$placeid;

if ($action=='addrecord' && WT_USER_IS_ADMIN) {
	$statement=
		WT_DB::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

	if (($_POST['LONG_CONTROL'] == '') || ($_POST['NEW_PLACE_LONG'] == '') || ($_POST['NEW_PLACE_LATI'] == '')) {
		$statement->execute(array(getHighestIndex()+1, $placeid, $level, $_POST['NEW_PLACE_NAME'], null, null, $_POST['NEW_ZOOM_FACTOR'], $_POST['icon']));
	} else {
		$statement->execute(array(getHighestIndex()+1, $placeid, $level, $_POST['NEW_PLACE_NAME'], $_POST['LONG_CONTROL'][3].$_POST['NEW_PLACE_LONG'], $_POST['LATI_CONTROL'][3].$_POST['NEW_PLACE_LATI'], $_POST['NEW_ZOOM_FACTOR'], $_POST['icon']));
	}

	// autoclose window when update successful unless debug on
	if (!WT_DEBUG) {
		$controller->addInlineJavaScript('closePopupAndReloadParent();');
	}
	echo "<div class=\"center\"><a href=\"#\" onclick=\"closePopupAndReloadParent();return false;\">", WT_I18N::translate('Close Window'), "</a></div><br>";
	exit;
}

if ($action=='updaterecord' && WT_USER_IS_ADMIN) {
	$statement=
		WT_DB::prepare("UPDATE `##placelocation` SET pl_place=?, pl_lati=?, pl_long=?, pl_zoom=?, pl_icon=? WHERE pl_id=?");

	if (($_POST['LONG_CONTROL'] == '') || ($_POST['NEW_PLACE_LONG'] == '') || ($_POST['NEW_PLACE_LATI'] == '')) {
		$statement->execute(array($_POST['NEW_PLACE_NAME'], null, null, $_POST['NEW_ZOOM_FACTOR'], $_POST['icon'], $placeid));
	} else {
		$statement->execute(array($_POST['NEW_PLACE_NAME'], $_POST['LATI_CONTROL'][3].$_POST['NEW_PLACE_LATI'], $_POST['LONG_CONTROL'][3].$_POST['NEW_PLACE_LONG'], $_POST['NEW_ZOOM_FACTOR'], $_POST['icon'], $placeid));
	}

	// autoclose window when update successful unless debug on
	if (!WT_DEBUG) {
		$controller->addInlineJavaScript('closePopupAndReloadParent();');
	}
	echo "<div class=\"center\"><a href=\"#\" onclick=\"closePopupAndReloadParent();return false;\">", WT_I18N::translate('Close Window'), "</a></div><br>";
	exit;
}

// Update placelocation STREETVIEW fields ----------------------------------------------------------
if ($action=='update_sv_params' && WT_USER_IS_ADMIN) {	
	echo "Google Street View™ parameters updated";
	echo "<br><br>";
	echo "LATI = ".$_REQUEST['svlati']."<br>";
	echo "LONG = ".$_REQUEST['svlong']."<br>";
	echo "BEAR = ".$_REQUEST['svbear']."<br>";
	echo "ELEV = ".$_REQUEST['svelev']."<br>";
	echo "ZOOM = ".$_REQUEST['svzoom']."<br>";
	echo "<br><br>";	
	$statement=
		WT_DB::prepare("UPDATE `##placelocation` SET sv_lati=?, sv_long=?, sv_bearing=?, sv_elevation=?, sv_zoom=? WHERE pl_id=?");		
	$statement->execute(array($_REQUEST['svlati'], $_REQUEST['svlong'], $_REQUEST['svbear'], $_REQUEST['svelev'], $_REQUEST['svzoom'], $placeid));
	if (!WT_DEBUG) {
		$controller->addInlineJavaScript('closePopupAndReloadParent();');
	}
	echo "<div class=\"center\"><a href=\"#\" onclick=\"closePopupAndReloadParent();return false;\">", WT_I18N::translate('Close Window'), "</a></div><br>";
	exit;
}

if ($action=="update") {
	// --- find the place in the file
	$row=
		WT_DB::prepare("SELECT pl_place, pl_lati, pl_long, pl_icon, pl_parent_id, pl_level, pl_zoom FROM `##placelocation` WHERE pl_id=?")
		->execute(array($placeid))
		->fetchOneRow();
	$place_name = $row->pl_place;
	$place_icon = $row->pl_icon;
	$selected_country = explode("/", $place_icon);
	if (isset($selected_country[1]) && $selected_country[1]!="flags")
		$selected_country = $selected_country[1];
	else
		$selected_country = "Countries";
	$parent_id = $row->pl_parent_id;
	$level = $row->pl_level;
	$zoomfactor = $row->pl_zoom;
	$parent_lati = "0.0";
	$parent_long = "0.0";
	if ($row->pl_lati!==null && $row->pl_long!==null) {
		$place_lati = (float)(str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati));
		$place_long = (float)(str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long));
		$show_marker = true;
	} else {
		$place_lati = null;
		$place_long = null;
		$zoomfactor = 1;
		$show_marker = false;
	}

	do {
		$row=
			WT_DB::prepare("SELECT pl_lati, pl_long, pl_parent_id, pl_zoom FROM `##placelocation` WHERE pl_id=?")
			->execute(array($parent_id))
			->fetchOneRow();
		if (!$row) {
			break;
		}
		if ($row->pl_lati!==null && $row->pl_long!==null) {
			$parent_lati = (float)(str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati));
			$parent_long = (float)(str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long));
			if ($zoomfactor == 1) {
				$zoomfactor = $row->pl_zoom;
			}
		}
		$parent_id = $row->pl_parent_id;
	} 
	while ($row->pl_parent_id!=0 && $row->pl_lati===null && $row->pl_long===null);

	$success = false;

	echo '<b>', htmlspecialchars(str_replace('Unknown', WT_I18N::translate('unknown'), implode(WT_I18N::$list_separator, array_reverse($where_am_i, true)))), '</b><br>';
}

if ($action=='add') {
	// --- find the parent place in the file
	if ($placeid != 0) {
		if (!isset($place_name)) $place_name  = '';
		$place_lati = null;
		$place_long = null;
		$zoomfactor = 1;
		$parent_lati = '0.0';
		$parent_long = '0.0';
		$place_icon = '';
		$parent_id=$placeid;
		do {
			$row=
				WT_DB::prepare("SELECT pl_lati, pl_long, pl_parent_id, pl_zoom, pl_level FROM `##placelocation` WHERE pl_id=?")
				->execute(array($parent_id))
				->fetchOneRow();
			if ($row->pl_lati!==null && $row->pl_long!==null) {
				$parent_lati=str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati);
				$parent_long=str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long);
				$zoomfactor=$row->pl_zoom;
				if ($zoomfactor>$GOOGLEMAP_MAX_ZOOM) {
					$zoomfactor=$GOOGLEMAP_MAX_ZOOM;
				}
				$level=$row->pl_level+1;
			}
			$parent_id = $row->pl_parent_id;
		} while ($row->pl_parent_id!=0 && $row->pl_lati===null && $row->pl_long===null);
	}
	else {
		if (!isset($place_name)) $place_name  = '';
		$place_lati  = null;
		$place_long  = null;
		$parent_lati = "0.0";
		$parent_long = "0.0";
		$place_icon  = '';
		$parent_id   = 0;
		$level = 0;
		$zoomfactor  = $GOOGLEMAP_MIN_ZOOM;
	}
	$selected_country = 'Countries';
	$show_marker = false;
	$success = false;

	if (!isset($place_name) || $place_name=="") echo '<b>', WT_I18N::translate('unknown');
	else echo '<b>', $place_name;
	if (count($where_am_i)>0)
		echo ', ', htmlspecialchars(str_replace('Unknown', WT_I18N::translate('unknown'), implode(WT_I18N::$list_separator, array_reverse($where_am_i, true)))), '</b><br>';
	echo '</b><br>';
}

include_once 'wt_v3_places_edit.js.php';
$api='v3';

?>

<form method="post" id="editplaces" name="editplaces" action="module.php?mod=googlemap&amp;mod_action=places_edit">
	<input type="hidden" name="action" value="<?php echo $action; ?>record">
	<input type="hidden" name="placeid" value="<?php echo $placeid; ?>">
	<input type="hidden" name="level" value="<?php echo $level; ?>">
	<input type="hidden" name="icon" value="<?php echo $place_icon; ?>">
	<input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">
	<input type="hidden" name="place_long" value="<?php echo $place_long; ?>">
	<input type="hidden" name="place_lati" value="<?php echo $place_lati; ?>">
	<input type="hidden" name="parent_long" value="<?php echo $parent_long; ?>">
	<input type="hidden" name="parent_lati" value="<?php echo $parent_lati; ?>">

	<table class="facts_table">
	<tr>
		<td class="optionbox" colspan="3">
		<center><div id="map_pane" style="width: 100%; height: 300px"></div></center>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo WT_Gedcom_Tag::getLabel('PLAC'); ?></td>
		 <td class="optionbox"><input type="text" id="new_pl_name" name="NEW_PLACE_NAME" value="<?php echo htmlspecialchars($place_name); ?>" size="25" class="address_input">
			<div id="INDI_PLAC_pop" style="display: inline;">
			<?php echo print_specialchar_link('NEW_PLACE_NAME'); ?></div></td><td class="optionbox">
			<label for="new_pl_name"><a href="#" onclick="showLocation_all(document.getElementById('new_pl_name').value); return false">&nbsp;<?php echo WT_I18N::translate('Search globally'); ?></a></label>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<label for="new_pl_name"><a href="#" onclick="showLocation_level(document.getElementById('new_pl_name').value); return false">&nbsp;<?php echo WT_I18N::translate('Search locally'); ?></a></label>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo WT_I18N::translate('Precision'), help_link('PLE_PRECISION','googlemap'); ?></td>
		<?php
			$exp = explode(".", $place_lati);
			if (isset($exp[1])) {
				$precision1 = strlen($exp[1]);
			} else {
				$precision1 = -1;
			}
			$exp = explode(".", $place_long);
			if (isset($exp[1])) {
				$precision2 = strlen($exp[1]);
			} else {
				$precision2 = -1;
			}
			($precision1 > $precision2) ? ($precision = $precision1) : ($precision = $precision2);
			if ($precision == -1 ) ($level > 3) ? ($precision = 3) : ($precision = $level);
			elseif ($precision > 5) {
				$precision = 5;
			}
		?>
		<td class="optionbox" colspan="2">
			<input type="radio" id="new_prec_0" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$GOOGLEMAP_PRECISION_0) echo "checked=\"checked\""; ?> value="<?php echo $GOOGLEMAP_PRECISION_0; ?>">
			<label for="new_prec_0"><?php echo WT_I18N::translate('Country'); ?></label>
			<input type="radio" id="new_prec_1" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$GOOGLEMAP_PRECISION_1) echo "checked=\"checked\""; ?> value="<?php echo $GOOGLEMAP_PRECISION_1; ?>">
			<label for="new_prec_1"><?php echo WT_I18N::translate('State'); ?></label>
			<input type="radio" id="new_prec_2" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$GOOGLEMAP_PRECISION_2) echo "checked=\"checked\""; ?> value="<?php echo $GOOGLEMAP_PRECISION_2; ?>">
			<label for="new_prec_2"><?php echo WT_I18N::translate('City'); ?></label>
			<input type="radio" id="new_prec_3" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$GOOGLEMAP_PRECISION_3) echo "checked=\"checked\""; ?> value="<?php echo $GOOGLEMAP_PRECISION_3; ?>">
			<label for="new_prec_3"><?php echo WT_I18N::translate('Neighborhood'); ?></label>
			<input type="radio" id="new_prec_4" name="NEW_PRECISION" onchange="updateMap();"<?php if ($precision==$GOOGLEMAP_PRECISION_4) echo "checked=\"checked\""; ?> value="<?php echo $GOOGLEMAP_PRECISION_4; ?>">
			<label for="new_prec_4"><?php echo WT_I18N::translate('House'); ?></label>
			<input type="radio" id="new_prec_5" name="NEW_PRECISION" onchange="updateMap();"<?php if ($precision>$GOOGLEMAP_PRECISION_4) echo "checked=\"checked\""; ?> value="<?php echo $GOOGLEMAP_PRECISION_5; ?>">
			<label for="new_prec_5"><?php echo WT_I18N::translate('Max'); ?></label>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo WT_Gedcom_Tag::getLabel('LATI'); ?></td>
		<td class="optionbox" colspan="2">
			<input type="text" id="NEW_PLACE_LATI" name="NEW_PLACE_LATI" placeholder="<?php echo /* I18N: Measure of latitude/longitude */ WT_I18N::translate('degrees') ?>" value="<?php if ($place_lati != null) echo abs($place_lati); ?>" size="20" onchange="updateMap();">
			<select name="LATI_CONTROL" onchange="updateMap();">
				<option value="PL_N" <?php if ($place_lati > 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('north'); ?></option>
				<option value="PL_S" <?php if ($place_lati < 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('south'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo WT_Gedcom_Tag::getLabel('LONG'); ?></td>
		<td class="optionbox" colspan="2">
			<input type="text" id="NEW_PLACE_LONG" name="NEW_PLACE_LONG" placeholder="<?php echo WT_I18N::translate('degrees') ?>" value="<?php if ($place_long != null) echo abs($place_long); ?>" size="20" onchange="updateMap();">
			<select name="LONG_CONTROL" onchange="updateMap();">
				<option value="PL_E" <?php if ($place_long > 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('east'); ?></option>
				<option value="PL_W" <?php if ($place_long < 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('west'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo WT_I18N::translate('Zoom factor'), help_link('PLE_ZOOM','googlemap'); ?></td>
		<td class="optionbox" colspan="2">
			<input type="text" id="NEW_ZOOM_FACTOR" name="NEW_ZOOM_FACTOR" value="<?php echo $zoomfactor; ?>" size="20" onchange="updateMap();"></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo WT_I18N::translate('Flag'), help_link('PLE_ICON','googlemap'); ?></td>
		<td class="optionbox" colspan="2">
			<div id="flagsDiv">
<?php
		if (($place_icon == NULL) || ($place_icon == "")) { ?>
				<a href="#" onclick="change_icon();return false;"><?php echo WT_I18N::translate('Change flag'); ?></a>
<?php   }
		else { ?>
				<img alt="<?php echo /* I18N: The emblem of a country or region */ WT_I18N::translate('Flag'); ?>" src="<?php echo WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place_icon; ?>">&nbsp;&nbsp;
				<a href="#" onclick="change_icon();return false;"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;
				<a href="#" onclick="remove_icon();return false;"><?php echo WT_I18N::translate('Remove flag'); ?></a>
<?php   } ?>
			</div></td>
	</tr>
	</table>
	<p id="save-cancel">
		<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
		<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
	</p>
</form>
