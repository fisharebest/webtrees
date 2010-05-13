<?php
/**
 * Google map module for phpGedView
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @package webtrees
 * @subpackage Module
 * $Id$
 * @author Johan Borkhuis
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'modules/googlemap/defaultconfig.php';

global $SESSION_HIDE_GOOGLEMAP;
$SESSION_HIDE_GOOGLEMAP = "empty";
if ((isset($_REQUEST["HIDE_GOOGLEMAP"])) && (empty($SEARCH_SPIDER))) {
	if(stristr("true", $_REQUEST["HIDE_GOOGLEMAP"])) {
		$SESSION_HIDE_GOOGLEMAP = "true";
	}
	if(stristr("false", $_REQUEST["HIDE_GOOGLEMAP"])) {
		$SESSION_HIDE_GOOGLEMAP = "false";
	}
}

// change the session values and store if needed.
if($SESSION_HIDE_GOOGLEMAP == "true") $_SESSION['hide_googlemap'] = true;
if($SESSION_HIDE_GOOGLEMAP == "false") $_SESSION['hide_googlemap'] = false;
if($SESSION_HIDE_GOOGLEMAP == "empty") {
	if((isset($_SESSION['hide_googlemap'])) && ($_SESSION['hide_googlemap'] == true))
		$SESSION_HIDE_GOOGLEMAP = "true";
	else
		$SESSION_HIDE_GOOGLEMAP = "false";
}

// functions copied from print_fact_place
function print_fact_place_map($factrec) {
	$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
	if ($ct>0) {
		$retStr = " ";
		$levels = explode(",", $match[1]);
		$place = trim($match[1]);
		// reverse the array so that we get the top level first
		$levels = array_reverse($levels);
		$retStr .= "<a href=\"placelist.php?action=show&amp;";
		foreach($levels as $pindex=>$ppart) {
			// routine for replacing ampersands
			$ppart = preg_replace("/amp\%3B/", "", trim($ppart));
			$retStr .= "parent[$pindex]=".PrintReady($ppart)."&amp;";
		}
		$retStr .= "level=".count($levels);
		$retStr .= "\"> ".PrintReady($place)."</a>";
		return $retStr;
	}
	return "";
}


function print_address_structure_map($factrec, $level) {
	global $WORD_WRAPPED_NOTES;
	global $POSTAL_CODE;

	//  $POSTAL_CODE = 'false' - before city, 'true' - after city and/or state
	//-- define per gedcom till can do per address countries in address languages
	//-- then this will be the default when country not recognized or does not exist
	//-- both Finland and Suomi are valid for Finland etc.
	//-- see http://www.bitboost.com/ref/international-address-formats.html

	$nlevel = $level+1;
	$ct = preg_match_all("/$level ADDR(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$arec = get_sub_record($level, "$level ADDR", $factrec, $i+1);
		$resultText = "";
		$cn = preg_match("/$nlevel _NAME (.*)/", $arec, $cmatch);
		if ($cn>0) $resultText .= str_replace("/", "", $cmatch[1])."<br />";
		$resultText .= PrintReady(trim($omatch[$i][1]));
		$cont = get_cont($nlevel, $arec);
		if (!empty($cont)) $resultText .= str_replace(array(" ", "<br&nbsp;"), array("&nbsp;", "<br "), PrintReady($cont));
		else {
			if (strlen(trim($omatch[$i][1])) > 0) echo "<br />";
				$cs = preg_match("/$nlevel ADR1 (.*)/", $arec, $cmatch);
			if ($cs>0) {
				if ($cn==0) {
					$resultText .= "<br />";
					$cn=0;
				}
				$resultText .= PrintReady($cmatch[1]);
			}
			$cs = preg_match("/$nlevel ADR2 (.*)/", $arec, $cmatch);
			if ($cs>0) {
				if ($cn==0) {
					$resultText .= "<br />";
					$cn=0;
				}
				$resultText .= PrintReady($cmatch[1]);
			}

			if ($POSTAL_CODE) {
				if (preg_match("/$nlevel CITY (.*)/", $arec, $cmatch))
					$resultText.=" ".PrintReady($cmatch[1]);
				if (preg_match("/$nlevel STAE (.*)/", $arec, $cmatch))
					$resultText.=", ".PrintReady($cmatch[1]);
				if (preg_match("/$nlevel POST (.*)/", $arec, $cmatch))
					$resultText.="<br />".PrintReady($cmatch[1]);
			} else {
				if (preg_match("/$nlevel POST (.*)/", $arec, $cmatch))
					$resultText.="<br />".PrintReady($cmatch[1]);
				if (preg_match("/$nlevel CITY (.*)/", $arec, $cmatch))
					$resultText.=" ".PrintReady($cmatch[1]);
				if (preg_match("/$nlevel STAE (.*)/", $arec, $cmatch))
					$resultText.=", ".PrintReady($cmatch[1]);
			}
		}
		if (preg_match("/$nlevel CTRY (.*)/", $arec, $cmatch))
			$resultText.="<br />".PrintReady($cmatch[1]);
		$resultText.= "<br />";
		// Here we can examine the resultant text and remove empty tags
		echo str_replace(chr(10), ' ' , $resultText);
	}
	$resultText = "<table>";
	$ct = preg_match_all("/$level PHON (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$resultText .= "<tr><td><span class=\"label\"><b>".translate_fact('PHON').": </b></span></td><td><span class=\"field\">";
		$resultText .= getLRM() . $omatch[$i][1]. getLRM();
		$resultText .= "</span></td></tr>";
	}
	$ct = preg_match_all("/$level FAX (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$resultText .= "<tr><td><span class=\"label\"><b>".translate_fact('FAX').": </b></span></td><td><span class=\"field\">";
		$resultText .= getLRM() . $omatch[$i][1] . getLRM();
		$resultText .= "</span></td></tr>";
	}
	$ct = preg_match_all("/$level EMAIL (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$resultText .= "<tr><td><span class=\"label\"><b>".translate_fact('EMAIL').": </b></span></td><td><span class=\"field\">";
		$resultText .= "<a href=\"mailto:".$omatch[$i][1]."\">".$omatch[$i][1]."</a>";
		$resultText .= "</span></td></tr>";
	}
	$ct = preg_match_all("/$level (WWW|URL) (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$resultText .= "<tr><td><span class=\"label\"><b>".translate_fact('URL').": </b></span></td><td><span class=\"field\">";
		$resultText .= "<a href=\"".$omatch[$i][2]."\" target=\"_blank\">".$omatch[$i][2]."</a>";
		$resultText .= "</span></td></tr>";
	}
	$resultText .= "</table>";
	if ($resultText!="<table></table>") echo str_replace(chr(10), ' ' , $resultText);
}

function rem_prefix_from_placename($prefix_list, $place, $placelist) {
	$prefix_split = explode(";", $prefix_list);
	foreach ($prefix_split as $prefix) {
		if (!empty($prefix)) {
			if (preg_match('/^'.$prefix.' (.*)/', $place, $matches) != 0) {
				$placelist[] = $matches[1];
			}
		}
	}
	return $placelist;
}

function rem_postfix_from_placename($postfix_list, $place, $placelist) {
	$postfix_split = explode (";", $postfix_list);
	foreach ($postfix_split as $postfix) {
		if (!empty($postfix)) {
			if (preg_match('/^(.*) '.$postfix.'$/', $place, $matches) != 0) {
				$placelist[] = $matches[1];
			}
		}
	}
	return $placelist;
}

function rem_prefix_postfix_from_placename($prefix_list, $postfix_list, $place, $placelist) {
	$prefix_split = explode (";", $prefix_list);
	$postfix_split = explode (";", $postfix_list);
	foreach ($prefix_split as $prefix) {
		if (!empty($prefix)) {
			foreach ($postfix_split as $postfix) {
				if (!empty($postfix)) {
					if (preg_match('/^'.$prefix.' (.*) '.$postfix.'$/', $place, $matches) != 0) {
						$placelist[] = $matches[1];
					}
				}
			}
		}
	}
	return $placelist;
}

function create_possible_place_names ($placename, $level) {
	global $GM_PREFIX, $GM_POSTFIX, $GM_PRE_POST_MODE;

	$retlist = array();

	switch (@$GM_PRE_POST_MODE[$level]) {
	case 0:     // 0: no pre/postfix
		$retlist[] = $placename;
		break;
	case 1:     // 1 = Normal name, Prefix, Postfix, Both
		$retlist[] = $placename;
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist);
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist);
		break;
	case 2:     // 2 = Normal name, Postfix, Prefxi, Both
		$retlist[] = $placename;
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist);
		break;
	case 3:     // 3 = Prefix, Postfix, Both, Normal name
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist);
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist);
		$retlist[] = $placename;
		break;
	case 4:     // 4 = Postfix, Prefix, Both, Normal name
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist);
		$retlist[] = $placename;
		break;
	case 5:     // 5 = Prefix, Postfix, Normal name, Both
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist);
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist);
		$retlist[] = $placename;
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist);
		break;
	case 6:     // 6 = Postfix, Prefix, Normal name, Both
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist);
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist);
		$retlist[] = $placename;
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist);
		break;
	}
	return $retlist;
}

function abbreviate($text) {
	if (utf8_strlen($text)>13) {
		if (trim(utf8_substr($text, 10, 1))!="") 
			$desc = utf8_substr($text, 0, 11).".";
		else $desc = trim(utf8_substr($text, 0, 11));
	}
	else $desc = $text;
	return $desc;
}

function get_lati_long_placelocation ($place) {
	global $TBLPREFIX;
	$parent = explode (",", $place);
	$parent = array_reverse($parent);
	$place_id = 0;
	for($i=0; $i<count($parent); $i++) {
		$parent[$i] = trim($parent[$i]);
		if (empty($parent[$i])) $parent[$i]="unknown";// GoogleMap module uses "unknown" while GEDCOM uses , ,
		$placelist = create_possible_place_names($parent[$i], $i+1);
		foreach ($placelist as $key => $placename) {
			$pl_id=
				WT_DB::prepare("SELECT pl_id FROM {$TBLPREFIX}placelocation WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
				->execute(array($i, $place_id, $placename))
				->fetchOne();
			if (!empty($pl_id)) break;
		}
		if (empty($pl_id)) break;
		$place_id = $pl_id;
	}

	$row=
		WT_DB::prepare("SELECT pl_lati, pl_long, pl_zoom, pl_icon, pl_level FROM {$TBLPREFIX}placelocation WHERE pl_id=? ORDER BY pl_place")
		->execute(array($place_id))
		->fetchOneRow();
	if ($row) {
		return array('lati'=>$row->pl_lati, 'long'=>$row->pl_long, 'zoom'=>$row->pl_zoom, 'icon'=>$row->pl_icon, 'level'=>$row->pl_level);
	} else {
		return array();
	}
}

function setup_map() {
	global $GOOGLEMAP_ENABLED, $GOOGLEMAP_API_KEY, $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM;
	if (!$GOOGLEMAP_ENABLED) {
		return;
	}
	?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $GOOGLEMAP_API_KEY; ?>" type="text/javascript"></script>
	<script src="modules/googlemap/wt_googlemap.js" type="text/javascript"></script>
	<script type="text/javascript">
	// <![CDATA[
		if (window.attachEvent) {
			window.attachEvent("onunload", function() {
				GUnload();      // Internet Explorer
			});
		} else {
			window.addEventListener("unload", function() {
				GUnload(); // Firefox and standard browsers
			}, false);
		}
	var GOOGLEMAP_MAP_TYPE =<?php echo $GOOGLEMAP_MAP_TYPE;?>;
	var minZoomLevel = <?php echo $GOOGLEMAP_MIN_ZOOM;?>;
	var maxZoomLevel = <?php echo $GOOGLEMAP_MAX_ZOOM;?>;
	var startZoomLevel = <?php echo $GOOGLEMAP_MAX_ZOOM;?>;
	//]]>
	</script>
	<?php

}

function tool_tip_text($marker) {
	$tool_tip=$marker['fact'];
	if (!empty($marker['info']))
		$tool_tip.=": {$marker['info']}";
	if (!empty($marker['name'])) {
		$person=Person::getInstance($marker['name']);
		if ($person && $person->canDisplayName()) {
			$tool_tip.=": ".PrintReady($person->getFullName());
		}
	}
	if (!empty($marker['date'])) {
		$date=new GedcomDate($marker['date']);
		$tool_tip.=" - ".$date->Display(false);
	}
	return $tool_tip;
// dates & RTL is not OK - adding PrintReady does not solve it
}

function create_indiv_buttons() {
	?>
	<style type="text/css">
	#map_type
	{
		margin: 0;
		padding: 0;
		font-family: Arial;
		font-size: 10px;
		list-style: none;
	}
	#map_type li
	{
		display: block;
		width: 70px;
		text-align: center;
		padding: 2px;
		border: 1px solid black;
		cursor: pointer;
		float: left;
		margin-left: 2px;
	}
	#map_type li.non_active
	{
		background: white;
		color: black;
		font-weight: normal;
	}
	#map_type li.active
	{
		background: gray;
		color: white;
		font-weight: bold;
	}
	#map_type li:hover
	{
		background: #ddd;
	}
	#map_nav
	{
		position: relative;
		top: -484px;
		left: 101%;
	}
	
	</style>
	<script type='text/javascript'>
	<!--
	function Map_type() {}
	Map_type.prototype = new GControl();

	Map_type.prototype.refresh = function()
	{
		this.button1.className = 'non_active';
		if(this.map.getCurrentMapType() != G_NORMAL_MAP)
			this.button2.className = 'non_active';
		else
			this.button2.className = 'active';
		if(this.map.getCurrentMapType() != G_SATELLITE_MAP)
			this.button3.className = 'non_active';
		else
			this.button3.className = 'active';
		if(this.map.getCurrentMapType() != G_HYBRID_MAP)
			this.button4.className = 'non_active';
		else
			this.button4.className = 'active';
		if(this.map.getCurrentMapType() != G_PHYSICAL_MAP)
			this.button5.className = 'non_active';
		else
			this.button5.className = 'active';
	}

	Map_type.prototype.initialize = function(place_map)
	{
		var list 	= document.createElement("ul");
		list.id	= 'map_type';

		var button1 = document.createElement('li');
		var button2 = document.createElement('li');
		var button3 = document.createElement('li');
		var button4 = document.createElement('li');
		var button5 = document.createElement('li');

		button1.innerHTML = '<?php echo i18n::translate('Redraw map')?>';
		button2.innerHTML = '<?php echo i18n::translate('Map')?>';
		button3.innerHTML = '<?php echo i18n::translate('Satellite')?>';
		button4.innerHTML = '<?php echo i18n::translate('Hybrid')?>';
		button5.innerHTML = '<?php echo i18n::translate('Terrain')?>';

		button1.onclick = function() { javascript:ResizeMap(); return false; };
		button2.onclick = function() { map.setMapType(G_NORMAL_MAP); return false; };
		button3.onclick = function() { map.setMapType(G_SATELLITE_MAP); return false; };
		button4.onclick = function() { map.setMapType(G_HYBRID_MAP); return false; };
		button5.onclick = function() { map.setMapType(G_PHYSICAL_MAP); return false; };

		list.appendChild(button1);
		list.appendChild(button2);
		list.appendChild(button3);
		list.appendChild(button4);
		list.appendChild(button5);

		this.button1 = button1;
		this.button2 = button2;
		this.button3 = button3;
		this.button4 = button4;
		this.button5 = button5;
		this.map = map;
		map.getContainer().appendChild(list);
		return list;
	}

	Map_type.prototype.getDefaultPosition = function()
	{
		return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(2, 2));
	}
	var map_type;
	</script>
	<?php
}

function build_indiv_map($indifacts, $famids) {
	global $GOOGLEMAP_API_KEY, $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM, $GEDCOM;
	global $GOOGLEMAP_XSIZE, $GOOGLEMAP_YSIZE, $SHOW_LIVING_NAMES;
	global $GOOGLEMAP_ENABLED, $TBLPREFIX, $TEXT_DIRECTION, $GM_DEFAULT_TOP_VALUE, $GOOGLEMAP_COORD;

	if (!$GOOGLEMAP_ENABLED) {
		echo "<table class=\"facts_table\">\n";
		echo "<tr><td colspan=\"2\" class=\"facts_value\">", i18n::translate('GoogleMap module disabled'), "<script language=\"JavaScript\" type=\"text/javascript\">tabstyles[5]='tab_cell_inactive_empty'; document.getElementById('pagetab5').className='tab_cell_inactive_empty';</script></td></tr>\n";
		echo "<script type=\"text/javascript\">\n";
		echo "function ResizeMap ()\n{\n}\nfunction SetMarkersAndBounds ()\n{\n}\n</script>\n";
		if (WT_USER_IS_ADMIN) {
			echo "<tr><td align=\"center\" colspan=\"2\">\n";
			echo "<a href=\"module.php?mod=googlemap&mod_action=editconfig\">", i18n::translate('Manage GoogleMap configuration'), "</a>";
			echo "</td></tr>\n";
		}
		echo "\n\t</table>\n<br />";
		?>
		<script type="text/javascript">
			document.getElementById("googlemap_left").innerHTML = document.getElementById("googlemap_content").innerHTML;
			document.getElementById("googlemap_content").innerHTML = "";
		</script>
		<?php
		return;
	}

	$markers=array();

	$zoomLevel = $GOOGLEMAP_MAX_ZOOM;
	$placelocation=WT_DB::table_exists("{$TBLPREFIX}placelocation");
	//-- sort the facts
	//sort_facts($indifacts); facts should already be sorted
	$i = 0;
	foreach ($indifacts as $key => $value) {
			$fact = $value->getTag();
			$fact_data=$value->getDetail();
			$factrec = $value->getGedComRecord();
			$placerec = null;
			if ($value->getPlace()!=null) {
				$placerec = get_sub_record(2, "2 PLAC", $factrec);
				$addrFound = false;
			} else {
				if (preg_match("/\d ADDR (.*)/", $factrec, $match)) {
					$placerec = get_sub_record(1, "\d ADDR", $factrec);
					$addrFound = true;
				}
			}
			if (!empty($placerec)) {
				$ctla = preg_match("/\d LATI (.*)/", $placerec, $match1);
				$ctlo = preg_match("/\d LONG (.*)/", $placerec, $match2);
				$spouserec = get_sub_record(2, "2 _PGVS", $factrec);
				$ctlp = preg_match("/\d _PGVS @(.*)@/", $spouserec, $spouseid);
				if ($ctlp>0) {
					$useThisItem = displayDetailsById($spouseid[1]);
				} else {
					$useThisItem = true;
				}
				if (($ctla>0) && ($ctlo>0) && ($useThisItem==true)) {
					$i = $i + 1;
					$markers[$i]=array('class'=>'optionbox', 'index'=>'', 'tabindex'=>'', 'placed'=>'no');
					if ($fact == "EVEN" || $fact=="FACT") {
						$eventrec = get_sub_record(1, "2 TYPE", $factrec);
						if (preg_match("/\d TYPE (.*)/", $eventrec, $match3)) {
							$markers[$i]["fact"]=translate_fact($match3[1]);
						} else {
							$markers[$i]["fact"]=translate_fact($fact);
						}
					} else {
						$markers[$i]["fact"]=translate_fact($fact);
					}
					if (!empty($fact_data) && $fact_data!='Y')
						$markers[$i]["info"] = $fact_data;
					$markers[$i]["placerec"] = $placerec;
					$match1[1] = trim($match1[1]);
					$match2[1] = trim($match2[1]);
					$markers[$i]["lati"] = str_replace(array('N', 'S', ','), array('', '-', '.') , $match1[1]);
					$markers[$i]["lng"] = str_replace(array('E', 'W', ','), array('', '-', '.') , $match2[1]);
					$ctd = preg_match("/2 DATE (.+)/", $factrec, $match);
					if ($ctd>0)
						$markers[$i]["date"] = $match[1];
					if ($ctlp>0)
						$markers[$i]["name"]=$spouseid[1];
				} else {
					if (($placelocation == true) && ($useThisItem==true) && ($addrFound==false)) {
						$ctpl = preg_match("/\d PLAC (.*)/", $placerec, $match1);
						$latlongval = get_lati_long_placelocation($match1[1]);
						if ((count($latlongval) == 0) && (!empty($GM_DEFAULT_TOP_VALUE))) {
							$latlongval = get_lati_long_placelocation($match1[1].", ".$GM_DEFAULT_TOP_VALUE);
							if ((count($latlongval) != 0) && ($latlongval["level"] == 0)) {
								$latlongval["lati"] = NULL;
								$latlongval["long"] = NULL;
							}
						}
						if ((count($latlongval) != 0) && ($latlongval["lati"] != NULL) && ($latlongval["long"] != NULL)) {
							$i = $i + 1;
							$markers[$i]=array('class'=>'optionbox', 'index'=>'', 'tabindex'=>'', 'placed'=>'no');
							if ($fact == "EVEN" || $fact=="FACT") {
								$eventrec = get_sub_record(1, "2 TYPE", $factrec);
								if (preg_match("/\d TYPE (.*)/", $eventrec, $match3)) {
									$markers[$i]["fact"]=translate_fact($match3[1]);
								} else {
									$markers[$i]["fact"]=translate_fact($fact);
								}
							} else {
								$markers[$i]["fact"]=translate_fact($fact);
							}
							if (!empty($fact_data) && $fact_data!='Y')
								$markers[$i]["info"] = $fact_data;
							$markers[$i]["icon"] = $latlongval["icon"];
							$markers[$i]["placerec"] = $placerec;
							if ($zoomLevel > $latlongval["zoom"]) $zoomLevel = $latlongval["zoom"];
							$markers[$i]["lati"] = str_replace(array('N', 'S', ','), array('', '-', '.') , $latlongval["lati"]);
							$markers[$i]["lng"] = str_replace(array('E', 'W', ','), array('', '-', '.') , $latlongval["long"]);
							$ctd = preg_match("/2 DATE (.+)/", $factrec, $match);
							if ($ctd>0)
								$markers[$i]["date"] = $match[1];
							if ($ctlp>0)
								$markers[$i]["name"]=$spouseid[1];
						}
					}
				}
			}
	}

	// Add children to the list
	if (count($famids)>0) {
		$hparents=false;
		for($f=0; $f<count($famids); $f++) {
			if (!empty($famids[$f])) {
				$famrec = find_gedcom_record($famids[$f], WT_GED_ID, true);
				if ($famrec) {
					$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch, PREG_SET_ORDER);
					for($j=0; $j<$num; $j++) {
						$person=Person::getInstance($smatch[$j][1]);
						if ($person->canDisplayDetails()) {
							$srec = find_person_record($smatch[$j][1], WT_GED_ID);
							$birthrec = '';
							$placerec = '';
							foreach ($person->getAllFactsByType('BIRT') as $sEvent) {
								$birthrec = $sEvent->getGedcomRecord();
								$placerec = get_sub_record(2, "2 PLAC", $birthrec);
								if (!empty($placerec)) {
									$ctd = preg_match("/\d DATE (.*)/", $birthrec, $matchd);
									$ctla = preg_match("/\d LATI (.*)/", $placerec, $match1);
									$ctlo = preg_match("/\d LONG (.*)/", $placerec, $match2);
									if (($ctla>0) && ($ctlo>0)) {
										$i = $i + 1;
										$markers[$i]=array('index'=>'', 'tabindex'=>'', 'placed'=>'no');
										if (strpos($srec, "\n1 SEX F")!==false) {
											$markers[$i]["fact"] = i18n::translate('Daughter');
											$markers[$i]["class"]  = "person_boxF";
										} else
											if (strpos($srec, "\n1 SEX M")!==false) {
												$markers[$i]["fact"] = i18n::translate('Son');
												$markers[$i]["class"]  = "person_box";
											} else {
												$markers[$i]["fact"]     = translate_fact('CHIL');
												$markers[$i]["class"]    = "person_boxNN";
											}
										$markers[$i]["placerec"] = $placerec;
										$match1[1] = trim($match1[1]);
										$match2[1] = trim($match2[1]);
										$markers[$i]["lati"] = str_replace(array('N', 'S', ','), array('', '-', '.'), $match1[1]);
										$markers[$i]["lng"]  = str_replace(array('E', 'W', ','), array('', '-', '.'), $match2[1]);
										if ($ctd > 0)
											$markers[$i]["date"] = $matchd[1];
										$markers[$i]["name"] = $smatch[$j][1];
									} else {
										if ($placelocation == true) {
											$ctpl = preg_match("/\d PLAC (.*)/", $placerec, $match1);
											$latlongval = get_lati_long_placelocation($match1[1]);
											if ((count($latlongval) == 0) && (!empty($GM_DEFAULT_TOP_VALUE))) {
												$latlongval = get_lati_long_placelocation($match1[1].", ".$GM_DEFAULT_TOP_VALUE);
												if ((count($latlongval) != 0) && ($latlongval["level"] == 0)) {
													$latlongval["lati"] = NULL;
													$latlongval["long"] = NULL;
												}
											}
											if ((count($latlongval) != 0) && ($latlongval["lati"] != NULL) && ($latlongval["long"] != NULL)) {
												$i = $i + 1;
												$markers[$i]=array('index'=>'', 'tabindex'=>'', 'placed'=>'no');
												$markers[$i]["fact"]     = translate_fact('CHIL');
												$markers[$i]["class"]    = "option_boxNN";
												if (strpos($srec, "\n1 SEX F")!==false) {
													$markers[$i]["fact"] = i18n::translate('Daughter');
													$markers[$i]["class"]  = "person_boxF";
												}
												if (strpos($srec, "\n1 SEX M")!==false) {
													$markers[$i]["fact"] = i18n::translate('Son');
													$markers[$i]["class"]  = "person_box";
												}
												$markers[$i]["icon"] = $latlongval["icon"];
												$markers[$i]["placerec"] = $placerec;
												if ($zoomLevel > $latlongval["zoom"]) $zoomLevel = $latlongval["zoom"];
												$markers[$i]["lati"]     = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval["lati"]);
												$markers[$i]["lng"]      = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval["long"]);
												if ($ctd > 0)
													$markers[$i]["date"] = $matchd[1];
												$markers[$i]["name"]   = $smatch[$j][1];
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	if ($i == 0) {
		echo "<table class=\"facts_table\">\n";
		echo "<tr><td colspan=\"2\" class=\"facts_value\">".i18n::translate('No map data for this person');
		//echo "<script language=\"JavaScript\" type=\"text/javascript\">tabstyles[5]='tab_cell_inactive_empty'; document.getElementById('pagetab5').className='tab_cell_inactive_empty';</script>";
		echo "</td></tr>\n";
		echo "<script type=\"text/javascript\">\n";
		echo "function ResizeMap ()\n{\n}\n</script>\n";
		if (WT_USER_IS_ADMIN) {
			echo "<tr><td align=\"center\" colspan=\"2\">\n";
			echo "<a href=\"module.php?mod=googlemap&mod_action=editconfig\">", i18n::translate('Manage GoogleMap configuration'), "</a>";
			echo "</td></tr>\n";
		}
	} else {
		?>
		<script type="text/javascript">
		function SetMarkersAndBounds () {
			var bounds = new GLatLngBounds();
		<?php
		foreach ($markers as $marker)
			echo "bounds.extend(new GLatLng({$marker["lati"]}, {$marker["lng"]}));\n";
		echo "SetBoundaries(bounds);\n";

		echo "var icon = new GIcon();";
		echo "icon.image = \"http://maps.google.com/intl/pl_ALL/mapfiles/marker.png\";";
		echo "icon.shadow = \"modules/googlemap/images/shadow50.png\";";
		echo "icon.iconAnchor = new GPoint(10, 34);";
		echo "icon.infoWindowAnchor = new GPoint(5, 1);";

		echo "\nmarkers.clear();\n";
		$indexcounter = 0;
		for ($j=1; $j<=$i; $j++) {
			// Use @ because some installations give warnings (but not errors?) about UTF-8
			$tooltip=@html_entity_decode(strip_tags(tool_tip_text($markers[$j])), ENT_QUOTES, 'UTF-8');
			if ($markers[$j]["placed"] == "no") {
				$multimarker = -1;
				// Count nr of locations where the long/lati is identical
				for($k=$j; $k<=$i; $k++)
					if (($markers[$j]["lati"] == $markers[$k]["lati"]) && ($markers[$j]["lng"] == $markers[$k]["lng"]))
						$multimarker = $multimarker + 1;

				if ($multimarker == 0) {        // Only one location with this long/lati combination
					$markers[$j]["placed"] = "yes";
					if (($markers[$j]["lati"] == NULL) || ($markers[$j]["lng"] == NULL) || (($markers[$j]["lati"] == "0") && ($markers[$j]["lng"] == "0"))) { 
						echo "var Marker{$j}_flag = new GIcon();\n";
						echo "	Marker{$j}_flag.image = \"modules/googlemap/images/marker_yellow.png\";\n";
						echo "	Marker{$j}_flag.shadow = \"modules/googlemap/images/shadow50.png\";\n";
						echo "	Marker{$j}_flag.iconSize = new GSize(20, 34);\n";
						echo "	Marker{$j}_flag.shadowSize = new GSize(37, 34);\n";
						echo "	Marker{$j}_flag.iconAnchor = new GPoint(10, 34);\n";
						echo "	Marker{$j}_flag.infoWindowAnchor = new GPoint(5, 1);\n";
						echo "var Marker{$j} = new GMarker(new GLatLng(0, 0), {icon:Marker{$j}_flag, title:\"", addslashes($tooltip), "\"});\n";
					} else if (empty($markers[$j]["icon"])) {
						echo "var Marker{$j} = new GMarker(new GLatLng({$markers[$j]["lati"]}, {$markers[$j]["lng"]}), {icon:icon, title:\"", addslashes($tooltip), "\"});\n";
					} else {
						echo "var Marker{$j}_flag = new GIcon();\n";
						echo "    Marker{$j}_flag.image = \"", $markers[$j]["icon"], "\";\n";
						echo "    Marker{$j}_flag.shadow = \"modules/googlemap/images/flag_shadow.png\";\n";
						echo "    Marker{$j}_flag.iconSize = new GSize(25, 15);\n";
						echo "    Marker{$j}_flag.shadowSize = new GSize(35, 45);\n";
						echo "    Marker{$j}_flag.iconAnchor = new GPoint(1, 45);\n";
						echo "    Marker{$j}_flag.infoWindowAnchor = new GPoint(5, 1);\n";
						echo "var Marker{$j} = new GMarker(new GLatLng(", $markers[$j]["lati"], ", ", $markers[$j]["lng"], "), {icon:Marker{$j}_flag, title:\"", addslashes($tooltip), "\"});\n";
					}
					echo "GEvent.addListener(Marker{$j}, \"click\", function() {\n";
					echo "Marker{$j}.openInfoWindowHtml(\"<div class='iwstyle'>";
					echo PrintReady($markers[$j]["fact"]);
					if (!empty($markers[$j]['info']))
						echo ': ', addslashes($markers[$j]['info']);
					if (!empty($markers[$j]["name"])) {
						$person=Person::getInstance($markers[$j]['name']);
						if ($person) {
							echo ': <a href=\"', $person->getLinkUrl(), '\">', $person->canDisplayName() ? PrintReady(addcslashes($person->getFullName(), '"')) : i18n::translate('Private'), '</a>';
						}
					}
					echo "<br />";
					if (preg_match("/2 PLAC (.*)/", $markers[$j]["placerec"]) == 0) {
						print_address_structure_map($markers[$j]["placerec"], 1);
					} else {
						echo preg_replace("/\"/", "\\\"", print_fact_place_map($markers[$j]["placerec"]));
					}
					if (!empty($markers[$j]["date"])) {
						$date=new GedcomDate($markers[$j]["date"]);
						echo "<br />", addslashes($date->Display(true));
					}
					if (($markers[$j]["lati"] == NULL) || ($markers[$j]["lng"] == NULL) || (($markers[$j]["lati"] == "0") && ($markers[$j]["lng"] == "0"))) {
						echo "<br /><br />", i18n::translate('This place has no coordinates');
						if (WT_USER_IS_ADMIN)
							echo '<br /><a href=\"module.php?mod=googlemap&mod_action=places&display=inactive\">', i18n::translate('Edit geographic location'), '</a>';
						echo "\");\n";
					}
					else if (!$GOOGLEMAP_COORD){
						echo "\");\n";
					} else {
						echo "<br /><br />";
						if ($markers[$j]["lati"]>'0'){echo "N", str_replace('-', '', $markers[$j]["lati"]);}else{ echo str_replace('-', 'S', $markers[$j]["lati"]);}
						echo ", ";
						if ($markers[$j]["lng"]>'0'){echo "E", str_replace('-', '', $markers[$j]["lng"]);}else{ echo str_replace('-', 'W', $markers[$j]["lng"]);}
						echo "\");\n";
					}
					echo "});\n";
					echo "markers.push(Marker{$j});\n";
					echo "map.addOverlay(Marker{$j});\n";
					$markers[$j]["index"] = $indexcounter;
					$markers[$j]["tabindex"] = 0;
					$indexcounter = $indexcounter + 1;
				} else {
					$tabcounter = 0;
					$markersindex = 0;
					$markers[$j]["placed"] = "yes";
					if (empty($markers[$j]["icon"])) {
						echo "var Marker{$j}_{$markersindex} = new GMarker(new GLatLng(", $markers[$j]["lati"], ", ", $markers[$j]["lng"], "), {icon:icon, title:\"", addslashes($tooltip), "\"});\n";
					} else {
						echo "var Marker{$j}_{$markersindex}_flag = new GIcon();\n";
						echo "    Marker{$j}_{$markersindex}_flag.image = \"", $markers[$j]["icon"], "\";\n";
						echo "    Marker{$j}_{$markersindex}_flag.shadow = \"modules/googlemap/images/flag_shadow.png\";\n";
						echo "    Marker{$j}_{$markersindex}_flag.iconSize = new GSize(25, 15);\n";
						echo "    Marker{$j}_{$markersindex}_flag.shadowSize = new GSize(35, 45);\n";
						echo "    Marker{$j}_{$markersindex}_flag.iconAnchor = new GPoint(1, 45);\n";
						echo "    Marker{$j}_{$markersindex}_flag.infoWindowAnchor = new GPoint(5, 1);\n";
						echo "var Marker{$j}_{$markersindex} = new GMarker(new GLatLng(", $markers[$j]["lati"], ", ", $markers[$j]["lng"], "), {icon:Marker{$j}_{$markersindex}_flag, title:\"", addslashes($tooltip), "\"});\n";
					}
					echo "var Marker{$j}_{$markersindex}Info = [\n";
					$markers[$j]["index"] = $indexcounter;
					$markers[$j]["tabindex"] = $tabcounter;
					$tabcounter = $tabcounter + 1;
					echo "new GInfoWindowTab(\"", abbreviate($markers[$j]["fact"]), "\", \"<div class='iwstyle'>", PrintReady($markers[$j]["fact"]);
					if (!empty($markers[$j]['info']))
						echo ': ', addslashes($markers[$j]['info']);
					if (!empty($markers[$j]["name"])) {
						$person=Person::getInstance($markers[$j]['name']);
						if ($person) {
							echo ': <a href=\"', $person->getLinkUrl(), '\">', $person->canDisplayName() ? PrintReady(addcslashes($person->getFullName(), '"')) : i18n::translate('Private'), '</a>';
						}
					}
					echo "<br />";
					if (preg_match("/2 PLAC (.*)/", $markers[$j]["placerec"]) == 0) {
						print_address_structure_map($markers[$j]["placerec"], 1);
					} else {
						echo preg_replace("/\"/", "\\\"", print_fact_place_map($markers[$j]["placerec"]));
					}
					if (!empty($markers[$j]["date"])) {
						$date=new GedcomDate($markers[$j]["date"]);
						echo "<br />", addslashes($date->Display(true));
					}
					if (!$GOOGLEMAP_COORD){
						echo "\")";
					} else {
						echo "<br /><br />";
						if ($markers[$j]["lati"]>='0'){echo "N", str_replace('-', '', $markers[$j]["lati"]);}else{ echo str_replace('-', 'S', $markers[$j]["lati"]);}
						echo ", ";
						if ($markers[$j]["lng"]>='0'){echo "E", str_replace('-', '', $markers[$j]["lng"]);}else{ echo str_replace('-', 'W', $markers[$j]["lng"]);}
						echo "\")";
					}
					for($k=$j+1; $k<=$i; $k++) {
						if (($markers[$j]["lati"] == $markers[$k]["lati"]) && ($markers[$j]["lng"] == $markers[$k]["lng"])) {
							$markers[$k]["placed"] = "yes";
							$markers[$k]["index"] = $indexcounter;
							if ($tabcounter == 4) {
								// Use @ because some installations give warnings (but not errors?) about UTF-8
								$tooltip=@html_entity_decode(strip_tags(tool_tip_text($markers[$k])), ENT_QUOTES, 'UTF-8');
								echo "\n";
								echo "];\n";
								echo "GEvent.addListener(Marker{$j}_{$markersindex}, \"click\", function(tabToSelect) {\n";
								echo "if (tabToSelect>0) \n";
								echo "Marker{$j}_{$markersindex}.openInfoWindowTabsHtml(Marker{$j}_{$markersindex}Info, {selectedTab: tabToSelect});\n";
								echo "else Marker{$j}_{$markersindex}.openInfoWindowTabsHtml(Marker{$j}_{$markersindex}Info);\n";
								echo "});\n";
								echo "markers.push(Marker{$j}_{$markersindex});\n";
								echo "map.addOverlay(Marker{$j}_{$markersindex});\n";
								$indexcounter = $indexcounter + 1;
								$tabcounter = 0;
								$markersindex = $markersindex + 1;

								if (empty($markers[$j]["icon"])) {
									echo "var Marker{$j}_{$markersindex} = new GMarker(new GLatLng(", ($markers[$j]["lati"]-(0.0015*$markersindex)), ", ", ($markers[$j]["lng"]+(0.0025*$markersindex)), "), {icon:icon, title:\"", addslashes($tooltip), "\"});\n";
								} else {
									echo "var Marker{$j}_{$markersindex}_flag = new GIcon();\n";
									echo "    Marker{$j}_{$markersindex}_flag.image = \"", $markers[$j]["icon"], "\";\n";
									echo "    Marker{$j}_{$markersindex}_flag.shadow = \"modules/googlemap/images/flag_shadow.png\";\n";
									echo "    Marker{$j}_{$markersindex}_flag.iconSize = new GSize(25, 15);\n";
									echo "    Marker{$j}_{$markersindex}_flag.shadowSize = new GSize(35, 45);\n";
									echo "    Marker{$j}_{$markersindex}_flag.iconAnchor = new GPoint(1, 45);\n";
									echo "    Marker{$j}_{$markersindex}_flag.infoWindowAnchor = new GPoint(5, 1);\n";
									echo "var Marker{$j}_{$markersindex} = new GMarker(new GLatLng(", ($markers[$j]["lati"]-(0.0015*$markersindex)), ", ", ($markers[$j]["lng"]+(0.0025*$markersindex)), "), {icon:Marker{$j}_{$markersindex}_flag, title:\"", addslashes($tooltip), "\"});\n";
								}
								echo "var Marker{$j}_{$markersindex}Info = [\n";
							} else {
								echo ", \n";
							}
							$markers[$k]["index"] = $indexcounter;
							$markers[$k]["tabindex"] = $tabcounter;
							$tabcounter = $tabcounter + 1;
							echo "new GInfoWindowTab(\"", abbreviate($markers[$k]["fact"]), "\", \"<div class='iwstyle'>", $markers[$k]["fact"];
							if (!empty($markers[$k]['info']))
								echo ': ', addslashes($markers[$k]['info']);
							if (!empty($markers[$k]["name"])) {
								$person=Person::getInstance($markers[$k]['name']);
								if ($person) {
									echo ': <a href=\"', $person->getLinkUrl(), '\">', $person->canDisplayName() ? PrintReady(addcslashes($person->getFullName(), '"')) : i18n::translate('Private'), '</a>';
								}
							}
							echo "<br />";
							if (preg_match("/2 PLAC (.*)/", $markers[$k]["placerec"]) == 0) {
								print_address_structure_map($markers[$k]["placerec"], 1);
							} else {
								echo preg_replace("/\"/", "\\\"", print_fact_place_map($markers[$k]["placerec"]));
							}
							if (!empty($markers[$k]["date"])) {
								$date=new GedcomDate($markers[$k]["date"]);
								echo "<br />", addslashes($date->Display(true));
							}
							if (!$GOOGLEMAP_COORD){
								echo "\")";
							} else {
								echo "<br /><br />";
								if ($markers[$j]["lati"]>='0'){echo "N", str_replace('-', '', $markers[$j]["lati"]);}else{ echo str_replace('-', 'S', $markers[$j]["lati"]);}
								echo ", ";
								if ($markers[$j]["lng"]>='0'){echo "E", str_replace('-', '', $markers[$j]["lng"]);}else{ echo str_replace('-', 'W', $markers[$j]["lng"]);}
								echo "\")";
							}
						}
					}
					echo "\n";
					echo "];\n";
					echo "GEvent.addListener(Marker{$j}_{$markersindex}, \"click\", function(tabToSelect) {\n";
					echo "if (tabToSelect>0) \n";
					echo "Marker{$j}_{$markersindex}.openInfoWindowTabsHtml(Marker{$j}_{$markersindex}Info, {selectedTab: tabToSelect});\n";
					echo "else Marker{$j}_{$markersindex}.openInfoWindowTabsHtml(Marker{$j}_{$markersindex}Info);\n";
					echo "});\n";
					echo "markers.push(Marker{$j}_{$markersindex});\n";
					echo "map.addOverlay(Marker{$j}_{$markersindex});\n";
					$indexcounter = $indexcounter + 1;
				}
			}
		}
		?>
		} </script>
		<?php
		echo "<div style=\"overflow: auto; overflow-x: hidden; overflow-y: auto; height: {$GOOGLEMAP_YSIZE}px;\"><table class=\"facts_table\">";
		foreach($markers as $marker) {
			echo "<tr><td class=\"facts_label\">";
			echo "<a href=\"javascript:highlight({$marker["index"]}, {$marker["tabindex"]})\">{$marker["fact"]}</a></td>";
			echo "<td class=\"{$marker['class']}\" style=\"white-space: normal\">";
			if (!empty($marker["info"]))
				echo "<span class=\"field\">{$marker["info"]}</span><br />";
			if (!empty($marker["name"])) {
				$person=Person::getInstance($marker['name']);
				if ($person) {
					echo '<a href="', $person->getLinkUrl(), '">', $person->canDisplayName() ? PrintReady($person->getFullName()) : i18n::translate('Private'), '</a>';
				}
				echo '<br />';
			}
			if (preg_match("/2 PLAC (.*)/", $marker["placerec"]) == 0) {
				print_address_structure_map($marker["placerec"], 1);
			} else {
				echo print_fact_place_map($marker["placerec"]), "<br />";
			}
			if (!empty($marker['date'])) {
				$date=new GedcomDate($marker['date']);
				echo $date->Display(true), "<br />";
			}
			echo "</td></tr>";
		}
		echo "</table></div><br />";
	}
	echo "\n<br />";

	return $i;
}

?>
