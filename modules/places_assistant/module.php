<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class places_assistant_WT_Module extends WT_Module {
	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('Places assistant');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('The places assistant provides a split mode way to enter places names.');
	}

	
	public static function get_plac_label() {
		global $GEDCOM;
		$ged_id=get_id_from_gedcom($GEDCOM);

		$HEAD = find_gedcom_record("HEAD", $ged_id);
		$HEAD_PLAC = get_sub_record(1, "1 PLAC", $HEAD);
		$HEAD_PLAC_FORM = get_sub_record(1, "2 FORM", $HEAD_PLAC);
		$HEAD_PLAC_FORM = substr($HEAD_PLAC_FORM, 7);
		if (empty($HEAD_PLAC_FORM)) $HEAD_PLAC_FORM = WT_I18N::translate('City, County, State/Province, Country');
		$plac_label = explode(',', $HEAD_PLAC_FORM);
		$plac_label = array_reverse($plac_label);
		if ($HEAD_PLAC_FORM == WT_I18N::translate('City, County, State/Province, Country')) $plac_label[0] = translate_fact('CTRY');

		return $plac_label;
	}

	public static function setup_place_subfields($element_id) {
		global $WT_PLACES_SETUP, $WT_IMAGES;

		if (!empty($WT_PLACES_SETUP)) return;
		$WT_PLACES_SETUP = true;

		$plac_label = self::get_plac_label();

		?>
		<script type="text/javascript">
		<!--
		include_css('modules/places_assistant/_css/dropdown.css');
		-->
		</script>
		<script type="text/javascript" src="modules/places_assistant/_js/getobject.js"></script>
		<script type="text/javascript" src="modules/places_assistant/_js/modomt.js"></script>
		<script type="text/javascript" src="modules/places_assistant/_js/xmlextras.js"></script>
		<script type="text/javascript" src="modules/places_assistant/_js/acdropdown.js"></script>
		<script type="text/javascript" src="js/strings.js"></script>
		<script type="text/javascript">
		<!--
		var element_id = '<?php echo $element_id; ?>';
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
			mapfile = 'modules/places_assistant/'+ctry+'/'+ctry+'.extra.htm';
			http_request.open('GET', mapfile, false); http_request.send(null);
			if (http_request.status == 200) {
				document.getElementById("mapdata").innerHTML += http_request.responseText;
			} else {
			// 2. localized map
				mapfile = 'modules/places_assistant/'+ctry+'/'+ctry+'.<?php echo WT_LOCALE; ?>.htm';
				http_request.open('GET', mapfile, false); http_request.send(null);
				if (http_request.status == 200) {
					document.getElementById("mapdata").innerHTML += http_request.responseText;
				} else {
			// 3. default map
					mapfile = 'modules/places_assistant/'+ctry+'/'+ctry+'.htm';
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
			for (p=0; p<<?php echo count($plac_label); ?>; p++) {
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
				<?php foreach (get_all_countries() as $country_code=>$country_name) { ?>
				else if (ctry=='<?php echo utf8_strtoupper(addslashes($country_name)); ?>') ctry='<?php echo $country_code; ?>';
				<?php } ?>
				else if (ctry.length!=3) ctry=ctry.substr(0,3);
				pdir='modules/places_assistant/'+ctry+'/';
				// select current country in the list
				sel=document.getElementsByName(place_tag+'_PLAC_CTRY_select')[0];
				for (i=0;i<sel.length;++i) if (sel.options[i].value==ctry) sel.options[i].selected=true;
				// refresh country flag
				var img=document.getElementsByName(place_tag+'_PLAC_CTRY_flag')[0];
				var ctryFlag = 'modules/places_assistant/flags/'+ctry+'.png';
				if (ctry=='???') ctryFlag = 'modules/places_assistant/flags/blank.png';
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
	 * @param string $element_id id of PLAC input element in the form
	 */
	public static function print_place_subfields($element_id) {
		global $iso3166, $WT_IMAGES;

		$plac_label = self::get_plac_label();
		$countries = get_all_countries();
		uasort($countries, 'utf8_strcasecmp');

		echo "<div id='mapdata'></div>";

		$cols=40;
		echo "&nbsp;<a href=\"javascript:;\" onclick=\"expand_layer('".$element_id."_div'); toggleplace('".$element_id."'); return false;\"><img id=\"".$element_id."_div_img\" src=\"".$WT_IMAGES["plus"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" />&nbsp;</a>";
		echo "<br /><div id=\"".$element_id."_div\" style=\"display: none; border-width:thin; border-style:none; padding:0px\">";
		// subtags creation : _0 _1 _2 etc...
		$icountry=-1;
		$istate=-1;
		$icounty=-1;
		$icity=-1;
		for ($i=0; $i<count($plac_label); $i++) {
			$subtagid=$element_id."_".$i;
			$subtagname=$element_id."_".$i;
			$plac_label[$i]=trim($plac_label[$i]);
			if (in_array(utf8_strtolower($plac_label[$i]), array("country", "pays", "land", "zeme", "ülke", "país", "ország", "nazione", "kraj", "maa", utf8_strtolower(translate_fact('CTRY'))))) {
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
			echo "<small>";
			// Translate certain tags.  The should be specified in english, as the gedcom file format is english.
			switch (strtolower($plac_label[$i])) {
			case 'country':  echo WT_I18N::translate('Country'); break;
			case 'state':    echo WT_I18N::translate('State'); break;
			case 'province': echo WT_I18N::translate('Province'); break;
			case 'county':   echo WT_I18N::translate('County'); break;
			case 'city':     echo WT_I18N::translate('City'); break;
			case 'parish':   echo WT_I18N::translate('Parish'); break;
			default:         echo $plac_label[$i]; break;
			}
			echo "</small><br />";
			echo "<input type=\"text\" id=\"".$subtagid."\" name=\"".$subtagname."\" value=\"\" size=\"".$cols."\"";
			echo " onblur=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
			echo " onchange=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
			echo " onmouseout=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
			if ($icountry<$i and $i<=$icity) echo " acdropdown=\"true\" autocomplete_list=\"url:modules/places_assistant/getdata.php?localized=".WT_LOCALE."&amp;field={$subtagname}&amp;s=\" autocomplete=\"off\" autocomplete_matchbegin=\"false\"";
			echo " />";
			// country selector
			if ($i==$icountry) {
				echo " <img id=\"".$element_id."_PLAC_CTRY_flag\" name=\"".$element_id."_PLAC_CTRY_flag\" src=\"modules/places_assistant/flags/blank.gif\" class=\"brightflag border1\" style=\"vertical-align:bottom\" alt=\"\" /> ";
				echo "<select id=\"".$subtagid."_select\" name=\"".$subtagname."_select\" class=\"submenuitem\"";
				echo " onchange=\"setPlaceCountry(this.value, '".$element_id."');\"";
				echo " >";
				echo "<option value=\"???\">??? : ".WT_I18N::translate('???')."</option>";
				foreach ($countries as $country_code=>$country_name) {
					if ($country_code!="???") {
						$txt=$country_code." : ".$country_name;
						if (utf8_strlen($txt)>40) $txt = utf8_substr($txt, 0, 40).WT_I18N::translate('…');
						echo "<option value=\"".$country_code."\">".$txt."</option>";
					}
				}
				echo "</select>";
			} else {
				print_specialchar_link($subtagid, false);
			}
			// clickable map
			if ($i<$icountry or $i>$icounty) echo "<br />";
			else echo "<div id='".$subtagname."_div' name='".$subtagname."_div' style='overflow:hidden; height:32px; width:auto; border-width:thin; border-style:none;'><img name='".$subtagname."_img' src='".$WT_IMAGES["spacer"]."' usemap='usemap' border='0' alt='' title='' style='height:inherit; width:inherit;' /></div>";
		}
		echo "</div>";
	}
}
