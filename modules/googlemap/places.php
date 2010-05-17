<?php
/**
 * Online UI for editing config.php site configuration variables
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team. All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * This Page Is Valid XHTML 1.0 Transitional! > 17 September 2005
 *
 * @package webtrees
 * @subpackage GoogleMap
 * @see config.php
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'modules/googlemap/defaultconfig.php';

global $TBLPREFIX, $iso3166;
$action=safe_REQUEST($_REQUEST, 'action');
if (isset($_REQUEST['parent']))	 $parent=$_REQUEST['parent'];
if (isset($_REQUEST['display'])) $display=$_REQUEST['display'];
if (isset($_REQUEST['mode']))	 $mode=$_REQUEST['mode'];
if (isset($_REQUEST['deleteRecord'])) $deleteRecord=$_REQUEST['deleteRecord'];

if (!isset($parent)) $parent=0;
if (!isset($display)) $display="";

// Take a place id and find its place in the hierarchy
// Input: place ID
// Output: ordered array of id=>name values, starting with the Top Level
// e.g. array(0=>"Top Level", 16=>"England", 19=>"London", 217=>"Westminster");
// NB This function exists in both places.php and places_edit.php
function place_id_to_hierarchy($id) {
	global $TBLPREFIX;

	$statement=
		WT_DB::prepare("SELECT pl_parent_id, pl_place FROM {$TBLPREFIX}placelocation WHERE pl_id=?");
	$arr=array();
	while ($id!=0) {
		$row=$statement->execute(array($id))->fetchOneRow();
		$arr=array($id=>$row->pl_place)+$arr;
		$id=$row->pl_parent_id;
	}
	return $arr;
}

// NB This function exists in both places.php and places_edit.php
function getHighestIndex() {
	global $TBLPREFIX;

	return (int)WT_DB::prepare("SELECT MAX(pl_id) FROM {$TBLPREFIX}placelocation")->fetchOne();
}

function getHighestLevel() {
	global $TBLPREFIX;

	return (int)WT_DB::prepare("SELECT MAX(pl_level) FROM {$TBLPREFIX}placelocation")->fetchOne();
}

/**
 * Find all of the places in the hierarchy
 */
function get_place_list_loc($parent_id) {
	global $display, $TBLPREFIX;
	if ($display=="inactive") {
		$rows=
			WT_DB::prepare("SELECT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=? ORDER BY pl_place")
			->execute(array($parent_id))
			->fetchAll();
	} else {
		$rows=
			WT_DB::prepare(
				"SELECT DISTINCT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon".
				" FROM {$TBLPREFIX}placelocation".
				" INNER JOIN {$TBLPREFIX}places ON {$TBLPREFIX}placelocation.pl_place={$TBLPREFIX}places.p_place AND {$TBLPREFIX}placelocation.pl_level={$TBLPREFIX}places.p_level".
				" WHERE pl_parent_id=? ORDER BY pl_place"
			)
			->execute(array($parent_id))
			->fetchAll();
	}

	$placelist=array();
	foreach ($rows as $row) {
		$placelist[]=array("place_id"=>$row->pl_id, "place"=>$row->pl_place, "lati"=>$row->pl_lati, "long"=>$row->pl_long, "zoom"=>$row->pl_zoom, "icon"=>$row->pl_icon);
	}
	uasort($placelist, "placesort");
	return $placelist;
}

function outputLevel($parent_id) {
	global $TBLPREFIX;
	$tmp = place_id_to_hierarchy($parent_id);
	$maxLevel = getHighestLevel();
	if ($maxLevel>8) $maxLevel = 8;
	$prefix = implode(';', $tmp);
	if ($prefix!='')
		$prefix.=';';
	$suffix=str_repeat(';', $maxLevel-count($tmp));
	$level=count($tmp);

	$rows=
		WT_DB::prepare("SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=? ORDER BY pl_place")
		->execute(array($parent_id))
		->fetchAll();

	foreach ($rows as $row) {
		echo "{$level};{$prefix}{$row->pl_place}{$suffix};{$row->pl_long};{$row->pl_lati};{$row->pl_zoom};{$row->pl_icon}\r\n";
		if ($level < $maxLevel)
			outputLevel($row->pl_id);
	}
}

/**
 * recursively find all of the csv files on the server
 *
 * @param string $path
 */
function findFiles($path) {
	global $placefiles;
	if (file_exists($path)) {
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			if ($entry!="." && $entry!=".." && $entry!=".svn") {
				if (is_dir($path."/".$entry)) {
					findFiles($path."/".$entry);
				} elseif (strstr($entry, ".csv")!==false) {
					$placefiles[] = preg_replace("~modules/googlemap/extra~", "", $path)."/".$entry;
				}
			}
		}
		$dir->close();
	}
}

if (!WT_USER_IS_ADMIN) {
	echo "<span class=\"subheaders\">".i18n::translate('Edit geographic place locations')."</span><br /><br />";
	echo "<table class=\"facts_table\">\n";
	echo "<tr><td colspan=\"2\" class=\"facts_value\">", i18n::translate('Page only for Administrators');
	echo "</td></tr></table>\n";
	echo "<br /><br /><br />\n";
	print_footer();
	exit;
}

global $GOOGLEMAP_MAX_ZOOM;

if ($action=="ExportFile" && WT_USER_IS_ADMIN) {
	$tmp = place_id_to_hierarchy($parent);
	$maxLevel = getHighestLevel();
	if ($maxLevel>8) $maxLevel=8;
	$tmp[0] = "places";
	$outputFileName=preg_replace('/[:;\/\\\(\)\{\}\[\] $]/', '_', implode('-', $tmp)).'.csv';
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$outputFileName.'"');
	echo "\"".i18n::translate('Level')."\";\"".i18n::translate('Country')."\";";
	if ($maxLevel>0) echo '"', i18n::translate('State'), '";';
	if ($maxLevel>1) echo '"', i18n::translate('County'), '";';
	if ($maxLevel>2) echo '"', i18n::translate('City'), '";';
	if ($maxLevel>3) echo '"', i18n::translate('Place'), '";';
	if ($maxLevel>4) echo '"', i18n::translate('Place'), '";';
	if ($maxLevel>5) echo '"', i18n::translate('Place'), '";';
	if ($maxLevel>6) echo '"', i18n::translate('Place'), '";';
	if ($maxLevel>7) echo '"', i18n::translate('Place'), '";';
	echo '"', i18n::translate('Longitude'), '";"', i18n::translate('Latitude'), '";';
	echo '"', i18n::translate('Zoom factor'), '";"', i18n::translate('Icon'), WT_EOL;
	outputLevel($parent);
	exit;
}

print_header(i18n::translate('Edit geographic place locations'));

if ($action=="ImportGedcom") {
	$placelist=array();
	$j=0;
	if ($mode=="all") {
		$statement=
			WT_DB::prepare("SELECT i_gedcom FROM {$TBLPREFIX}individuals UNION ALL SELECT f_gedcom FROM {$TBLPREFIX}families")
			->execute();
	} else {
		$statement=
			WT_DB::prepare("SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_file=? UNION ALL SELECT f_gedcom FROM {$TBLPREFIX}families WHERE f_file=?")
			->execute(array(WT_GED_ID, WT_GED_ID));
	}
	while ($gedrec=$statement->fetchColumn()) {
		$i = 1;
		$placerec = get_sub_record(2, "2 PLAC", $gedrec, $i);
		while (!empty($placerec)) {
			if (preg_match("/2 PLAC (.+)/", $placerec, $match)) {
				$placelist[$j] = array();
				$placelist[$j]["place"] = trim($match[1]);
				if (preg_match("/4 LATI (.*)/", $placerec, $match)) {
					$placelist[$j]["lati"] = trim($match[1]);
					if (($placelist[$j]["lati"][0] != "N") && ($placelist[$j]["lati"][0] != "S")) {
						if ($placelist[$j]["lati"] < 0) {
							$placelist[$j]["lati"][0] = "S";
						} else {
							$placelist[$j]["lati"] = "N".$placelist[$j]["lati"];
						}
					}
				}
				else $placelist[$j]["lati"] = "0";
				if (preg_match("/4 LONG (.*)/", $placerec, $match)) {
					$placelist[$j]["long"] = trim($match[1]);
					if (($placelist[$j]["long"][0] != "E") && ($placelist[$j]["long"][0] != "W")) {
						if ($placelist[$j]["long"] < 0) {
							$placelist[$j]["long"][0] = "W";
						} else {
							$placelist[$j]["long"] = "E".$placelist[$j]["long"];
						}
					}
				}
				else $placelist[$j]["long"] = "0";
				$j = $j + 1;
			}
			$i = $i + 1;
			$placerec = get_sub_record(2, "2 PLAC", $gedrec, $i);
		}
	}
	asort($placelist);

	$prevPlace = "";
	$prevLati = "";
	$prevLong = "";
	$placelistUniq = array();
	$j = 0;
	foreach ($placelist as $k=>$place) {
		if ($place["place"] != $prevPlace) {
			$placelistUniq[$j] = array();
			$placelistUniq[$j]["place"] = $place["place"];
			$placelistUniq[$j]["lati"] = $place["lati"];
			$placelistUniq[$j]["long"] = $place["long"];
			$j = $j + 1;
		} else if (($place["place"] == $prevPlace) && (($place["lati"] != $prevLati) || ($place["long"] != $prevLong))) {
			if (($placelistUniq[$j-1]["lati"] == 0) || ($placelistUniq[$j-1]["long"] == 0)) {
				$placelistUniq[$j-1]["lati"] = $place["lati"];
				$placelistUniq[$j-1]["long"] = $place["long"];
			} else if (($place["lati"] != "0") || ($place["long"] != "0")) {
				echo "Verscil: vorige waarde = $prevPlace, $prevLati, $prevLong, huidige = ", $place["place"], ", ", $place["lati"], ", ", $place["long"], "<br />";
			}
		}
		$prevPlace = $place["place"];
		$prevLati = $place["lati"];
		$prevLong = $place["long"];
	}

	$highestIndex = getHighestIndex();

	$default_zoom_level=array(4, 7, 10, 12);
	foreach ($placelistUniq as $k=>$place) {
        $parent=preg_split('/ *, */', $place["place"]);
		$parent=array_reverse($parent);
		$parent_id=0;
		for($i=0; $i<count($parent); $i++) {
			if (!isset($default_zoom_level[$i]))
				$default_zoom_level[$i]=$default_zoom_level[$i-1];
			$escparent=$parent[$i];
			if ($escparent == "") {
				$escparent = "Unknown";
			}
			$row=
				WT_DB::prepare("SELECT pl_id, pl_long, pl_lati, pl_zoom FROM {$TBLPREFIX}placelocation WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ?")
				->execute(array($i, $parent_id, $escparent))
				->fetchOneRow();
			if ($i < count($parent)-1) {
				// Create higher-level places, if necessary
				if (empty($row)) {
					$highestIndex++;
					WT_DB::prepare("INSERT INTO {$TBLPREFIX}placelocation (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom) VALUES (?, ?, ?, ?, ?)")
						->execute(array($highestIndex, $parent_id, $i, $escparent, $default_zoom_level[$i]));
					echo htmlspecialchars($escparent), '<br />';
					$parent_id=$highestIndex;
				} else {
					$parent_id=$row->pl_id;
				}
			} else {
				// Create lowest-level place, if necessary
				if (empty($row->pl_id)) {
					$highestIndex++;
					WT_DB::prepare("INSERT INTO {$TBLPREFIX}placelocation (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom) VALUES (?, ?, ?, ?, ?, ?, ?)")
						->execute(array($highestIndex, $parent_id, $i, $escparent, $place["long"], $place["lati"], $default_zoom_level[$i]));
					echo htmlspecialchars($escparent), '<br />';
				} else {
					if (empty($row->pl_long) && empty($row->pl_lati) && $place['lati']!="0" && $place['long']!="0") {
						WT_DB::prepare("UPDATE {$TBLPREFIX}placelocation SET pl_lati=?, pl_long=? WHERE pl_id=?")
							->execute(array($place["lati"], $place["long"], $row->pl_id));
						echo htmlspecialchars($escparent), '<br />';
					}
				}
			}
		}
	}
	$parent=0;
}

if ($action=="ImportFile") {
	$placefiles = array();
	findFiles("modules/googlemap/extra");
	sort($placefiles);
?>
<form method="post" enctype="multipart/form-data" id="importfile" name="importfile" action="module.php?mod=googlemap&mod_action=places">
	<input type="hidden" name="action" value="ImportFile2" />
	<table class="facts_table">
		<tr>
			<td class="descriptionbox"><?php echo i18n::translate('File containing places (CSV)'), help_link('PLIF_FILENAME','googlemap'); ?></td>
			<td class="optionbox"><input type="file" name="placesfile" size="50"></td>
		</tr>
		<?php if (count($placefiles)>0) { ?>
		<tr>
			<td class="descriptionbox"><?php echo i18n::translate('Server file containing places (CSV)'), help_link('PLIF_LOCALFILE','googlemap'); ?></td>
			<td class="optionbox">
				<select name="localfile">
					<option></option>
					<?php foreach($placefiles as $p=>$placefile) { ?>
					<option value="<?php echo htmlspecialchars($placefile); ?>"><?php 
						if (substr($placefile, 0, 1)=="/") echo substr($placefile, 1); 
						else echo $placefile; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td class="descriptionbox"><?php echo i18n::translate('Clear all place-locations before import?'), help_link('PLIF_CLEAN','googlemap');?></td>
			<td class="optionbox"><input type="checkbox" name="cleardatabase"></td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php echo i18n::translate('Update existing places only?'), help_link('PLIF_UPDATE','googlemap'); ?></td>
			<td class="optionbox"><input type="checkbox" name="updateonly"></td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php echo i18n::translate('Overwrite location-data with data from file?'), help_link('PLIF_OVERWRITE','googlemap'); ?></td>
			<td class="optionbox"><input type="checkbox" name="overwritedata"></td>
		</tr>
	</table>
	<input id="savebutton" type="submit" value="<?php echo i18n::translate('Continue Adding');?>" /><br />
</form>
<?php
	print_footer();
	exit;
}

if ($action=="ImportFile2") {
	$country_names=array();
	foreach ($iso3166 as $key=>$value) {
		$country_names[$key]=i18n::translate($key);
	}
	if (isset($_POST["cleardatabase"])) {
		WT_DB::exec("DELETE FROM {$TBLPREFIX}placelocation WHERE 1=1");
	}
	if (!empty($_FILES["placesfile"]["tmp_name"])) {
		$lines = file($_FILES["placesfile"]["tmp_name"]);
	} elseif (!empty($_REQUEST['localfile'])) {
		$lines = file("modules/googlemap/extra".$_REQUEST['localfile']);
	}
	// Strip BYTE-ORDER-MARK, if present
	if (!empty($lines[0]) && substr($lines[0], 0, 3)==WT_UTF8_BOM) $lines[0]=substr($lines[0], 3);
	asort($lines);
	$highestIndex = getHighestIndex();
	$placelist = array();
	$j = 0;
	$maxLevel = 0;
	foreach ($lines as $p => $placerec){
		$fieldrec = explode(';', $placerec);
		if($fieldrec[0] > $maxLevel) $maxLevel = $fieldrec[0];
	}
	$fields = count($fieldrec);
	foreach ($lines as $p => $placerec){
		$fieldrec = explode(';', $placerec);
		if (is_numeric($fieldrec[0]) && $fieldrec[0]<=$maxLevel) {
			$placelist[$j] = array();
			$placelist[$j]["place"] = "";
			for ($ii=$fields-4; $ii>1; $ii--) {
				if ($fieldrec[0] > $ii-2) $placelist[$j]["place"] .= $fieldrec[$ii].",";
			}
			foreach ($country_names as $countrycode => $countryname) {
				if ($countrycode == strtoupper($fieldrec[1])) {
					$fieldrec[1] = $countryname;
					break;
				}
			}
			$placelist[$j]["place"] .= $fieldrec[1];
			$placelist[$j]["long"] = $fieldrec[$fields-4];
			$placelist[$j]["lati"] = $fieldrec[$fields-3];
			$placelist[$j]["zoom"] = $fieldrec[$fields-2];
			$placelist[$j]["icon"] = trim($fieldrec[$fields-1]);
			$j = $j + 1;
		}
	}

	$prevPlace = "";
	$prevLati = "";
	$prevLong = "";
	$placelistUniq = array();
	$j = 0;
	foreach ($placelist as $k=>$place) {
		if ($place["place"] != $prevPlace) {
			$placelistUniq[$j] = array();
			$placelistUniq[$j]["place"] = $place["place"];
			$placelistUniq[$j]["lati"] = $place["lati"];
			$placelistUniq[$j]["long"] = $place["long"];
			$placelistUniq[$j]["zoom"] = $place["zoom"];
			$placelistUniq[$j]["icon"] = $place["icon"];
			$j = $j + 1;
		} else if (($place["place"] == $prevPlace) && (($place["lati"] != $prevLati) || ($place["long"] != $prevLong))) {
			if (($placelistUniq[$j-1]["lati"] == 0) || ($placelistUniq[$j-1]["long"] == 0)) {
				$placelistUniq[$j-1]["lati"] = $place["lati"];
				$placelistUniq[$j-1]["long"] = $place["long"];
				$placelistUniq[$j-1]["zoom"] = $place["zoom"];
				$placelistUniq[$j-1]["icon"] = $place["icon"];
			} else if (($place["lati"] != "0") || ($place["long"] != "0")) {
				echo "Differenc: last value = $prevPlace, $prevLati, $prevLong, current = ", $place["place"], ", ", $place["lati"], ", ", $place["long"], "<br />";
			}
		}
		$prevPlace = $place["place"];
		$prevLati = $place["lati"];
		$prevLong = $place["long"];
	}

	$default_zoom_level = array();
	$default_zoom_level[0] = 4;
	$default_zoom_level[1] = 7;
	$default_zoom_level[2] = 10;
	$default_zoom_level[3] = 12;
	foreach ($placelistUniq as $k=>$place) {
		$parent = explode(',', $place["place"]);
		$parent = array_reverse($parent);
		$parent_id=0;
		for($i=0; $i<count($parent); $i++) {
			$escparent=$parent[$i];
			if ($escparent == "") {
				$escparent = "Unknown";
			}
			$row=
				WT_DB::prepare("SELECT pl_id, pl_long, pl_lati, pl_zoom, pl_icon FROM {$TBLPREFIX}placelocation WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
				->execute(array($i, $parent_id, $escparent))
				->fetchOneRow();
			if (empty($row)) {       // this name does not yet exist: create entry
				if (!isset($_POST["updateonly"])) {
					$highestIndex = $highestIndex + 1;
					if (($i+1) == count($parent)) {
						$zoomlevel = $place["zoom"];
					} elseif (isset($default_zoom_level[$i])) {
						$zoomlevel = $default_zoom_level[$i];
					} else {
						$zoomlevel = $GOOGLEMAP_MAX_ZOOM;
					}
					if (($place["lati"] == "0") || ($place["long"] == "0") || (($i+1) < count($parent))) {
						WT_DB::prepare("INSERT INTO {$TBLPREFIX}placelocation (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?)")
							->execute(array($highestIndex, $parent_id, $i, $escparent, $zoomlevel, $place["icon"]));
					} else {
						//delete leading zero
						$pl_lati = str_replace(array('N', 'S', ','), array('', '-', '.') , $place["lati"]);
						$pl_long = str_replace(array('E', 'W', ','), array('', '-', '.') , $place["long"]);
						if ($pl_lati >= 0) {
							$place["lati"] = "N".abs($pl_lati);
						} elseif ($pl_lati < 0) {
							$place["lati"] = "S".abs($pl_lati);
						}
						if ($pl_long >= 0) {
							$place["long"] = "E".abs($pl_long);
						} elseif ($pl_long < 0) {
							$place["long"] = "W".abs($pl_long);
						}
						WT_DB::prepare("INSERT INTO {$TBLPREFIX}placelocation (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
							->execute(array($highestIndex, $parent_id, $i, $escparent, $place["long"], $place["lati"], $zoomlevel, $place["icon"]));
					}
					$parent_id = $highestIndex;
				}
			} else {
				$parent_id = $row->pl_id;
				if ((isset($_POST["overwritedata"])) && ($i+1 == count($parent))) {
					WT_DB::prepare("UPDATE {$TBLPREFIX}placelocation SET pl_lati=?, pl_long=?, pl_zoom=?, pl_icon=? WHERE pl_id=?")
						->execute(array($place["lati"], $place["long"], $place["zoom"], $place["icon"], $parent_id));
				} else {
					if ((($row->pl_long == "0") || ($row->pl_long == null)) && (($row->pl_lati == "0") || ($row->pl_lati == null))) {
						WT_DB::prepare("UPDATE {$TBLPREFIX}placelocation SET pl_lati=?, pl_long=? WHERE pl_id=?")
							->execute(array($place["lati"], $place["long"], $parent_id));
					}
					if (empty($row->pl_icon) && !empty($place['icon'])) {
						WT_DB::prepare("UPDATE {$TBLPREFIX}placelocation SET pl_icon=? WHERE pl_id=?")
							->execute(array($place["icon"], $parent_id));
					}
				}
			}
		}
	}
	$parent=0;
}

if ($action=="DeleteRecord") {
	$exists=
		WT_DB::prepare("SELECT 1 FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=?")
		->execute(array($deleteRecord))
		->fetchOne();
	
	if (!$exists) {
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}placelocation WHERE pl_id=?")
			->execute(array($deleteRecord));
	} else {
		echo "<table class=\"facts_table\"><tr><td class=\"optionbox\">".i18n::translate('Location not removed: this location contains sub-locations')."</td></tr></table>";
	}
}

?>
<script language="JavaScript" type="text/javascript">
<!--
function showchanges() {
	window.location = '<?php echo basename($_SERVER["REQUEST_URI"]); ?>&show_changes=yes';
}

function edit_place_location(placeid) {
	window.open('module.php?mod=googlemap&mod_action=places_edit&action=update&placeid='+placeid+"&"+sessionname+"="+sessionid, '_blank', 'top=50, left=50, width=700, height=550, resizable=1, scrollbars=1');
	return false;
}

function add_place_location(placeid) {
	window.open('module.php?mod=googlemap&mod_action=places_edit&action=add&placeid='+placeid+"&"+sessionname+"="+sessionid, '_blank', 'top=50, left=50, width=700, height=550, resizable=1, scrollbars=1');
	return false;
}

function delete_place(placeid) {
	var answer=confirm("<?php echo i18n::translate('Remove this location?');?>");
	if (answer == true) {
		window.location = "<?php echo $_SERVER["REQUEST_URI"]; ?>&action=DeleteRecord&deleteRecord=" + placeid;
	}
}

//-->
</script>
<?php

echo "<span class=\"subheaders\">".i18n::translate('Edit geographic place locations').": </span>";
$where_am_i=place_id_to_hierarchy($parent);
foreach (array_reverse($where_am_i, true) as $id=>$place) {
	if ($id==$parent) {
		if ($place != "Unknown") {
			echo PrintReady($place);
		} else {
			echo i18n::translate('Unknown');
		}
	} else {
		echo "<a href=\"module.php?mod=googlemap&mod_action=places&parent={$id}&display={$display}\">";
		if ($place != "Unknown") {
			echo PrintReady($place), "</a>";
		} else {
			echo i18n::translate('Unknown'), "</a>";
		}
	}
	echo " - ";
}
echo "<a href=\"module.php?mod=googlemap&mod_action=places&parent=0&display=$display\">".i18n::translate('Top Level')."</a>";
echo "<br /><br /><form name=\"active\" method=\"post\" action=\"module.php?mod=googlemap&mod_action=places&parent=$parent&display=$display\">";
echo "\n<table><tr><td class=\"optionbox\">", i18n::translate('Click here to show inactive places'), ": <input type=\"checkbox\" name=\"display\" value=\"inactive\"";
if ($display == 'inactive') echo " checked=\"checked\"";
echo ">\n<input type=\"submit\" value=\"", i18n::translate('View'), "\" >";
echo help_link('PLE_ACTIVE','googlemap');
echo "</td></tr></table>";
echo "</form>";

$placelist=get_place_list_loc($parent);

echo "<table class=\"facts_table\"><tr>";
echo "<th class=\"descriptionbox\">", translate_fact('PLAC'), "</th>";
echo "<th class=\"descriptionbox\">", translate_fact('LATI'), "</th>";
echo "<th class=\"descriptionbox\">", translate_fact('LONG'), "</th>";
echo "<th class=\"descriptionbox\">".i18n::translate('Zoom factor')."</th>";
echo "<th class=\"descriptionbox\">".i18n::translate('Icon')."</th>";
echo "<th class=\"descriptionbox\" colspan=\"2\">";
echo help_link('PL_EDIT_LOCATION','googlemap');
echo i18n::translate('Edit geographic location'), "</th></tr>";
if (count($placelist) == 0)
	echo "<tr><td colspan=\"7\" class=\"facts_value\">", i18n::translate('No places found'), "</td></tr>";
foreach ($placelist as $place) {
	echo "<tr><td class=\"optionbox\"><a href=\"module.php?mod=googlemap&mod_action=places&parent={$place['place_id']}&display={$display}\">";
	if ($place["place"] != "Unknown")
			echo PrintReady($place["place"]), "</a></td>";
		else
			echo i18n::translate('Unknown'), "</a></td>";
	echo "<td class=\"optionbox\">{$place['lati']}</td>";
	echo "<td class=\"optionbox\">{$place['long']}</td>";
	echo "<td class=\"optionbox\">{$place['zoom']}</td>";
	echo "<td class=\"optionbox\">";
	if (($place["icon"] == NULL) || ($place["icon"] == "")) {
		if (($place['lati'] == NULL) || ($place['long'] == NULL) || (($place['lati'] == "0") && ($place['long'] == "0"))) {
			echo "&nbsp;";
			echo "<img src=\"http://labs.google.com/ridefinder/images/mm_20_yellow.png\">";
		}
		else {
			echo "&nbsp;";
			echo "<img src=\"http://labs.google.com/ridefinder/images/mm_20_red.png\">";
		}
	} else {
		echo "<img src=\"", $place["icon"], " \"width=\"25\" height=\"15\">";
	}
	echo "</td>";
	echo "<td class=\"optionbox\"><a href=\"javascript:;\" onclick=\"edit_place_location({$place['place_id']});return false;\">", i18n::translate('Edit'), "</a></td>";
	$noRows=
		WT_DB::prepare("SELECT COUNT(pl_id) FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=?")
		->execute(array($place["place_id"]))
		->fetchOne();
	if ($noRows==0) { ?>
	<td class="optionbox"><a href="javascript:;" onclick="delete_place(<?php echo $place["place_id"], ");return false;\">";?><img src="images/remove.gif" border="0" alt="<?php echo i18n::translate('Remove');?>" /></a></td>
<?php       } else { ?>
		<td class="optionbox"><img src="images/remove-dis.png" border="0" alt="" /> </td>
<?php       } ?>
	</tr>
	<?php
}
?>
</table>
<?php
?>
	<table class="facts_table">
		<tr>
			<td class="optionbox" colspan="2"><a href="javascript:;" onclick="add_place_location(<?php echo $parent;?>);return false;"><?php echo i18n::translate('Add place');?></a><?php echo help_link('PL_ADD_LOCATION','googlemap'); ?></td>
		</tr>
		<tr>
			<td class="optionbox"><a href="module.php?mod=googlemap&mod_action=places&action=ImportGedcom&mode=curr"><?php echo i18n::translate('Import from current GEDCOM');?></a><?php echo help_link('PL_IMPORT_GEDCOM','googlemap'); ?></td>
			<td class="optionbox"><a href="module.php?mod=googlemap&mod_action=places&action=ImportGedcom&mode=all"><?php echo i18n::translate('Import from all GEDCOMs');?></a><?php echo help_link('PL_IMPORT_ALL_GEDCOM','googlemap'); ?></td>
		</tr>
		<tr>
			<td class="optionbox" colspan="2"><a href="module.php?mod=googlemap&mod_action=places&action=ImportFile&mode=add"><?php echo i18n::translate('Import from file');?></a><?php echo help_link('PL_IMPORT_FILE','googlemap'); ?></td>
		</tr>
		<tr>
			<td class="optionbox">
<?php
	if (count($where_am_i)<=4) {
		echo "<a href=\"module.php?mod=googlemap&mod_action=places&action=ExportFile&parent={$parent}\">";
		echo i18n::translate('Export current view to file'), "</a>";
		echo help_link('PL_EXPORT_FILE','googlemap');
	} else {
		echo "&nbsp;";
	}
	echo "</td><td class=\"optionbox\">";
	echo "<a href=\"module.php?mod=googlemap&mod_action=places&action=ExportFile&parent=0\">";
	echo i18n::translate('Export all locations to file'), "</a>";
	echo help_link('PL_EXPORT_ALL_FILE','googlemap');
	echo "</td></tr></table><br />";
if(empty($SEARCH_SPIDER))
	print_footer();
else {
	echo i18n::translate('Search Engine Spider Detected'), ": ", $SEARCH_SPIDER;
	echo "\n</div>\n\t</body>\n</html>";
}
?>
