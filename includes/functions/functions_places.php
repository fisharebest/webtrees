<?php
/**
 * Functions for places selection (clickable maps, autocompletion...)
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
 * @subpackage Edit
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_PLACE_PHP', '');

function get_plac_label() {
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	$HEAD = find_gedcom_record("HEAD", $ged_id);
	$HEAD_PLAC = get_sub_record(1, "1 PLAC", $HEAD);
	$HEAD_PLAC_FORM = get_sub_record(1, "2 FORM", $HEAD_PLAC);
	$HEAD_PLAC_FORM = substr($HEAD_PLAC_FORM, 7);
	if (empty($HEAD_PLAC_FORM)) $HEAD_PLAC_FORM = i18n::translate('City, County, State/Province, Country');
	$plac_label = explode(',', $HEAD_PLAC_FORM);
	$plac_label = array_reverse($plac_label);
	if ($HEAD_PLAC_FORM == i18n::translate('City, County, State/Province, Country')) $plac_label[0] = i18n::translate('CTRY');

	return $plac_label;
}

function setup_place_subfields($element_id) {
	global $WT_PLACES_SETUP;
	global $WT_IMAGE_DIR, $WT_IMAGES;

	if (!empty($WT_PLACES_SETUP)) return;
	$WT_PLACES_SETUP = true;

	$plac_label = get_plac_label();

	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	include_css('places/dropdown.css');
	-->
	</script>
	<script type="text/javascript" src="places/getobject.js"></script>
	<script type="text/javascript" src="places/modomt.js"></script>
	<script type="text/javascript" src="places/xmlextras.js"></script>
	<script type="text/javascript" src="places/acdropdown.js"></script>
	<script type="text/javascript" src="js/strings.js"></script>
	<script type="text/javascript">
	<!--
	var element_id = '<?php print $element_id; ?>';
	function http_loadmap(ctry) {
		// meaningless request?
		if (ctry=='' || ctry=='???') return;
		// already loaded ?
		if (document.getElementsByName(ctry)[0]) return;
		// load data into HTML tag <div id="mapdata"> ... </div>
		document.getElementById("mapdata").innerHTML = "";
		// get mapfile from server
		http_request = XmlHttp.create();
		// 1. user map
		mapfile = 'places/'+ctry+'/'+ctry+'.extra.htm';
		http_request.open('GET', mapfile, false); http_request.send(null);
		if (http_request.status == 200) {
			document.getElementById("mapdata").innerHTML += http_request.responseText;
		} else {
		// 2. localized map
			mapfile = 'places/'+ctry+'/'+ctry+'.<?php echo WT_LOCALE; ?>.htm';
			http_request.open('GET', mapfile, false); http_request.send(null);
			if (http_request.status == 200) {
				document.getElementById("mapdata").innerHTML += http_request.responseText;
			} else {
		// 3. default map
				mapfile = 'places/'+ctry+'/'+ctry+'.htm';
				http_request.open('GET', mapfile, false); http_request.send(null);
				// load data into HTML tag <div id="mapdata"> ... </div>
				if (http_request.status == 200) {
					document.getElementById("mapdata").innerHTML += http_request.responseText;
				}
			}
		}
	}
	// called to refresh field PLAC after any subfield change
	function updatewholeplace(place_tag) {
		place_value="";
		for (p=0; p<<?php print count($plac_label);?>; p++) {
			place_subtag=place_tag+'_'+p;
			if (document.getElementById(place_subtag)) {
				// clear data after opening bracket : Wales (WLS) ==> Wales
				subtagval = document.getElementById(place_subtag).value;
				cut = subtagval.indexOf(' (');
				if (cut>1) subtagval = subtagval.substring(0,cut);
				if (p>0) place_value = subtagval+", "+place_value;
				else place_value = subtagval;
			}
		}
		document.getElementById(place_tag).value = place_value;
	}
	// called to refresh subfields after any field PLAC change
	function splitplace(place_tag) {
		element_id = place_tag;
		place_value = document.getElementById(place_tag).value;
		var place_array=place_value.split(",");
		var len=place_array.length;
		for (p=0; p<len; p++) {
			q=len-p-1;
			place_subtag=place_tag+'_'+p;
			if (document.getElementById(place_subtag)) {
				//alert(place_subtag+':'+place_array[q]);
				document.getElementById(place_subtag).value=trim(place_array[q]);
			}
		}
		//document.getElementById(place_tag+'_0').focus();
		if (document.getElementsByName(place_tag+'_PLAC_CTRY')) {
			elt=document.getElementsByName(place_tag+'_PLAC_CTRY')[0];
			ctry=elt.value.toUpperCase();
			//alert(elt.value.charCodeAt(0)+'\n'+elt.value.charCodeAt(1));
			if (elt.value=='\u05d9\u05e9\u05e8\u05d0\u05dc') ctry='ISR'; // Israel hebrew name
			else if (ctry.length==3) elt.value=ctry;
			if (ctry=='') ctry='???';
			<?php global $iso3166; foreach (array_keys($iso3166) as $alpha3) { ?>
			else if (ctry=='<?php print addslashes(i18n::translate($alpha3)) ?>') ctry='<?php print $alpha3 ?>';
			<?php } ?>
			else if (ctry.length!=3) ctry=ctry.substr(0,3);
			pdir='places/'+ctry+'/';
			// select current country in the list
			sel=document.getElementsByName(place_tag+'_PLAC_CTRY_select')[0];
			for(i=0;i<sel.length;++i) if (sel.options[i].value==ctry) sel.options[i].selected=true;
			// refresh country flag
			var img=document.getElementsByName(place_tag+'_PLAC_CTRY_flag')[0];
			var ctryFlag = 'places/flags/'+ctry+'.gif';
			if (ctry=='???') ctryFlag = 'places/flags/blank.gif';
			img.src=ctryFlag;
			img.alt=ctry;
			img.title=ctry;
			// load html map file from server
			http_loadmap(ctry);
			// refresh country image
			img=document.getElementsByName(place_tag+'_PLAC_CTRY_img')[0];
			if (document.getElementsByName(ctry)[0]) {
				img.src=pdir+ctry+'.gif';
				img.alt=ctry;
				img.title=ctry;
				img.useMap='#'+ctry;
			}
			else {
				img.src='images/pix1.gif'; // show image only if mapname exists
				document.getElementsByName(place_tag+'_PLAC_CTRY_div')[0].style.height='auto';
			}
			// refresh state image
			/**img=document.getElementsByName(place_tag+'_PLAC_STAE_auto')[0];
			img.alt=ctry;
			img.title=ctry;**/
			stae=document.getElementsByName(place_tag+'_PLAC_STAE')[0].value;
			stae=strclean(stae);
			stae=ctry+'_'+stae;
			img=document.getElementsByName(place_tag+'_PLAC_STAE_img')[0];
			if (document.getElementsByName(stae)[0]) {
				img.src=pdir+stae+'.gif';
				img.alt=stae;
				img.title=stae;
				img.useMap='#'+stae;
			}
			else {
				img.src='images/pix1.gif'; // show image only if mapname exists
				document.getElementsByName(place_tag+'_PLAC_STAE_div')[0].style.height='auto';
			}
			// refresh county image
			/**img=document.getElementsByName(place_tag+'_PLAC_CNTY_auto')[0];
			img.alt=stae;
			img.title=stae;**/
			cnty=document.getElementsByName(place_tag+'_PLAC_CNTY')[0].value;
			cnty=strclean(cnty);
			cnty=stae+'_'+cnty;
			img=document.getElementsByName(place_tag+'_PLAC_CNTY_img')[0];
			if (document.getElementsByName(cnty)[0]) {
				img.src=pdir+cnty+'.gif';
				img.alt=cnty;
				img.title=cnty;
				img.useMap='#'+cnty;
			}
			else {
				img.src='images/pix1.gif'; // show image only if mapname exists
				document.getElementsByName(place_tag+'_PLAC_CNTY_div')[0].style.height='auto';
			}
			// refresh city image
			/**img=document.getElementsByName(place_tag+'_PLAC_CITY_auto')[0];
			img.alt=cnty;
			img.title=cnty;**/
		}
	}
	// called when clicking on +/- PLAC button
	function toggleplace(place_tag) {
		var ronly=document.getElementById(place_tag).readOnly;
		document.getElementById(place_tag).readOnly=1-ronly;
		if (ronly) {
			document.getElementById(place_tag+'_pop').style.display="inline";
			updatewholeplace(place_tag);
		}
		else {
			document.getElementById(place_tag+'_pop').style.display="none";
			splitplace(place_tag);
		}
	}
	// called when selecting a new country in country list
	function setPlaceCountry(txt, eid) {
		element_id=eid;
		document.getElementsByName(eid+'_PLAC_CTRY_div')[0].style.height='32px';
		document.getElementsByName(eid+'_PLAC_STAE_div')[0].style.height='32px';
		document.getElementsByName(eid+'_PLAC_CNTY_div')[0].style.height='32px';
		document.getElementsByName(eid+'_PLAC_CTRY')[0].value=txt;
		updatewholeplace(eid);
		splitplace(eid);
	}
	// called when clicking on a new state/region on country map
	function setPlaceState(txt) {
		if (txt!='') {
			document.getElementsByName(element_id+'_PLAC_STAE_div')[0].style.height='32px';
			document.getElementsByName(element_id+'_PLAC_CNTY_div')[0].style.height='32px';
		}
		div=document.getElementsByName(element_id+'_PLAC_CTRY_div')[0];
		if (div.style.height!='auto') { div.style.height='auto'; return; } else div.style.height='32px';
		document.getElementsByName(element_id+'_PLAC_STAE_div')[0].style.height='auto';
		p=txt.indexOf(' ('); if (1<p) txt=txt.substring(0,p); // remove code (XX)
		if (txt.length) document.getElementsByName(element_id+'_PLAC_STAE')[0].value=txt;
		updatewholeplace(element_id);
		splitplace(element_id);
	}
	// called when clicking on a new county on state map
	function setPlaceCounty(txt) {
		document.getElementsByName(element_id+'_PLAC_CNTY_div')[0].style.height='32px';
		div=document.getElementsByName(element_id+'_PLAC_STAE_div')[0];
		if (div.style.height!='auto') { div.style.height='auto'; return; } else div.style.height='32px';
		document.getElementsByName(element_id+'_PLAC_CNTY_div')[0].style.height='auto';
		p=txt.indexOf(' ('); if (1<p) txt=txt.substring(0,p); // remove code (XX)
		if (txt.length) document.getElementsByName(element_id+'_PLAC_CNTY')[0].value=txt;
		updatewholeplace(element_id);
		splitplace(element_id);
	}
	// called when clicking on a new city on county map
	function setPlaceCity(txt) {
		div=document.getElementsByName(element_id+'_PLAC_CNTY_div')[0];
		if (div.style.height!='auto') { div.style.height='auto'; return; } else div.style.height='32px';
		if (txt.length) document.getElementsByName(element_id+'_PLAC_CITY')[0].value=txt;
		updatewholeplace(element_id);
		splitplace(element_id);
	}
	//-->
	</script>
	<?php
}

/**
 * creates PLAC input subfields (Country, District ...) according to Gedcom HEAD>PLACE>FORM
 *
 * data split/copy is done locally by javascript functions
 *
 * @param string $element_id	id of PLAC input element in the form
 */
function print_place_subfields($element_id) {
	global $iso3166, $WT_IMAGE_DIR, $WT_IMAGES;

	//if ($element_id=="DEAT_PLAC") return; // known bug - waiting for a patch
	$plac_label = get_plac_label();
	print "<div id='mapdata'></div>";

	$cols=40;
	print "&nbsp;<a href=\"javascript:;\" onclick=\"expand_layer('".$element_id."_div'); toggleplace('".$element_id."'); return false;\"><img id=\"".$element_id."_div_img\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" />&nbsp;</a>";
	print "<br /><div id=\"".$element_id."_div\" style=\"display: none; border-width:thin; border-style:none; padding:0px\">\n";
	// subtags creation : _0 _1 _2 etc...
	$icountry=-1;
	$istate=-1;
	$icounty=-1;
	$icity=-1;
	for ($i=0; $i<count($plac_label); $i++) {
		$subtagid=$element_id."_".$i;
		$subtagname=$element_id."_".$i;
		$plac_label[$i]=trim($plac_label[$i]);
		if (in_array(utf8_strtolower($plac_label[$i]), array("country", "pays", "land", "zeme", "ülke", "país", "ország", "nazione", "kraj", "maa", utf8_strtolower(i18n::translate('CTRY'))))) {
			$cols="8";
			$subtagname=$element_id."_PLAC_CTRY";
			$icountry=$i;
			$istate=$i+1;
			$icounty=$i+2;
			$icity=$i+3;
		} else $cols=40;
		if ($i==$istate) $subtagname=$element_id."_PLAC_STAE";
		if ($i==$icounty) $subtagname=$element_id."_PLAC_CNTY";
		if ($i==$icity) $subtagname=$element_id."_PLAC_CITY";
		print "<small>";
		// Translate certain tags.  The should be specified in english, as the gedcom file format is english.
		switch (strtolower($plac_label[$i])) {
		case 'country':  echo i18n::translate('Country'); break;
		case 'state':    echo i18n::translate('State'); break;
		case 'province': echo i18n::translate('Province'); break;
		case 'county':   echo i18n::translate('County'); break;
		case 'city':     echo i18n::translate('City'); break;
		case 'parish':   echo i18n::translate('Parish'); break;
		default:         echo $plac_label[$i]; break;
		}
		print "</small><br />";
		print "<input type=\"text\" id=\"".$subtagid."\" name=\"".$subtagname."\" value=\"\" size=\"".$cols."\"";
		print " tabindex=\"".($i+1)."\" ";
		print " onblur=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
		print " onchange=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
		print " onmouseout=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
		if ($icountry<$i and $i<=$icity) print " acdropdown=\"true\" autocomplete_list=\"url:".encode_url("places/getdata.php?localized=".WT_LOCALE."&field={$subtagname}&s=")."\" autocomplete=\"off\" autocomplete_matchbegin=\"false\"";
		print " />\n";
		// country selector
		if ($i==$icountry) {
			print " <img id=\"".$element_id."_PLAC_CTRY_flag\" name=\"".$element_id."_PLAC_CTRY_flag\" src=\"places/flags/blank.gif\" class=\"brightflag border1\" style=\"vertical-align:bottom\" alt=\"\" /> ";
			print "<select id=\"".$subtagid."_select\" name=\"".$subtagname."_select\" class=\"submenuitem\"";
			print " onchange=\"setPlaceCountry(this.value, '".$element_id."');\"";
			print " >\n";
			print "<option value=\"???\">??? : ".i18n::translate('???')."</option>\n";
			foreach (array_keys($iso3166) as $alpha3) {
				if ($alpha3!="???") {
					$txt=$alpha3." : ".i18n::translate($alpha3);
					if (utf8_strlen($txt)>32) $txt = utf8_substr($txt, 0, 32).i18n::translate('…');
					print "<option value=\"".$alpha3."\">".$txt."</option>\n";
				}
			}
			print "</select>\n";
		} else {
			print_specialchar_link($subtagid, false);
		}
		// clickable map
		if ($i<$icountry or $i>$icounty) print "<br />\n";
		else print "<div id='".$subtagname."_div' name='".$subtagname."_div' style='overflow:hidden; height:32px; width:auto; border-width:thin; border-style:none;'><img name='".$subtagname."_img' src='images/spacer.gif' usemap='usemap' border='0' alt='' title='' style='height:inherit; width:inherit;' /></div>";
	}
	print "</div>";
}

/**
 * get the URL to link to a place
 * @string a url that can be used to link to placelist
 */
function get_place_url($gedcom_place) {
	global $GEDCOM;
	$exp = explode(",", $gedcom_place);
	$level = count($exp);
	$url = "placelist.php?action=show&level=".$level;
	for ($i=0; $i<$level; $i++) {
		$url .= "&parent[".$i."]=".trim($exp[$level-$i-1]);
	}
	$url .= "&ged=".$GEDCOM;
	return $url;
}

/**
 * get the first part of a place record
 * @param string $gedcom_place	The original place to shorten
 * @return string 	a shortened version of the place
 */
function get_place_short($gedcom_place) {
	global $GEDCOM, $SHOW_LIST_PLACES;
	if ($SHOW_LIST_PLACES==9) {
		return $gedcom_place;
	}
	$gedcom_place = trim($gedcom_place, " ,");
	$exp = explode(",", $gedcom_place);
	$place = "";
	for($i=0; $i<$SHOW_LIST_PLACES && $i<count($exp); $i++) {
		if ($i>0) $place .= ", ";
		$place.=trim($exp[$i]);
	}
	return $place;
}

/**
 * get the last part of a place record
 * @param string $gedcom_place	The original place to country
 * @return string 				a country version of the place
 */
function getPlaceCountry($gedcom_place) {
	global $GEDCOM;
	$gedcom_place = trim($gedcom_place, " ,");
	$exp = explode(",", $gedcom_place);
	$place = trim($exp[count($exp)-1]);
	return $place;
}
?>
