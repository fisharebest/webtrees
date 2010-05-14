<?php
/**
 * Print pedigree map using Googlemaps.
 * It requires that your place coordinates are stored on the Google Map
 * 'place_locations' table. It will NOT find coordinates stored only as tags in
 * your GEDCOM file. As in the Google Maps module, it can only display place
 * markers where the location exists with identical spelling in both your
 * GEDCOM '2 PLAC' tag (within the '1 BIRT' event) and the place_locations table.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License or,
 * at your discretion, any later version.
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
 * @author Nigel Osborne
 * @Developed for the 'Our-Families' web site (http://www.our-families.info)
 * @modified and added to PGV by Łukasz Wileński
 * @package webtrees
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'includes/controllers/pedigree_ctrl.php';
require WT_ROOT.'modules/googlemap/defaultconfig.php';

global $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $ENABLE_AUTOCOMPLETE, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $WT_IMAGES, $WT_IMAGE_DIR, $GEDCOM;

// Default is show for both of these.
$hideflags = safe_GET('hideflags');
$hidelines = safe_GET('hidelines');

$controller = new PedigreeController();
$controller->init();

// Default of 5
$clustersize = 5;
if (!empty($_REQUEST['clustersize'])) {
	if ($_REQUEST['clustersize'] == '3')
		$clustersize = 3;
	else if ($_REQUEST['clustersize'] == '1')
		$clustersize = 1;
}

// Start of internal configuration variables
//
// The Cloudy theme resizes itself to max screen width, sometimes making
// tables hard to construct for browser windows smaller that the screen
// width.  Setting $cloudy_locked = 1/0 will/won't force a the map to use
// the width chosen in the GoogleMap configuration.  All other themes
// float the width of the map to fill the browser width nicely.
$cloudy_locked = 1;

// Limit this to match available number of icons.
// 8 generations equals 255 individuals
$MAX_PEDIGREE_GENERATIONS = min($MAX_PEDIGREE_GENERATIONS, 8);

// End of internal configuration variables

global $theme_name, $TEXT_DIRECTION;

// -- print html header information
print_header($controller->getPageTitle());

if (!$GOOGLEMAP_ENABLED) {
	echo "<table class=\"facts_table\">\n";
	echo "<tr><td class=\"facts_value\">", i18n::translate('GoogleMap module disabled'), "</td></tr>\n";
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td align=\"center\">\n";
		echo "<a href=\"module.php?mod=googlemap&mod_action=editconfig\">", i18n::translate('Manage GoogleMap configuration'), "</a>";
		echo "</td></tr>\n";
	}
	echo "</table><br />";
	print_footer();
	return;
}

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
	</style>
	<?php
if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
echo '<div><table><tr><td valign="middle">';
echo "<h2>" . i18n::translate('Pedigree Map') . " " . i18n::translate('for') . " ";
echo PrintReady($controller->getPersonName())."</h2>";

// -- print the form to change the number of displayed generations
if (!$controller->isPrintPreview()) {
?>
<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
</script>
</td><td width="50px">&nbsp;</td><td>
	  <form name="people" method="get" action="module.php?ged=<?php echo $GEDCOM; ?>&amp;mod=googlemap&amp;mod_action=pedigree_map">
		<input type="hidden" name="mod" value="googlemap" />
		<input type="hidden" name="mod_action" value="pedigree_map" />
		<table class="pedigree_table <?php echo $TEXT_DIRECTION; ?>" width="555">
			<tr>
				<td colspan="5" class="topbottombar" style="text-align:center; ">
					<?php echo i18n::translate('Pedigree Map Options'); ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Root Person ID'), help_link('rootid'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Generations'), help_link('PEDIGREE_GENERATIONS'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Cluster size'), help_link('PEDIGREE_MAP_clustersize','googlemap'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php
					echo i18n::translate('Hide flags'), help_link('PEDIGREE_MAP_hideflags','googlemap');
					?>
				</td>
				<td class="descriptionbox wrap">
					<?php
					echo i18n::translate('Hide lines'), help_link('PEDIGREE_MAP_hidelines','googlemap');
					?>
				</td>
			</tr>
			<tr>
				<td class="optionbox">
					<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->rootid; ?>" />
					<?php print_findindi_link("rootid","");?>
				</td>
				<td class="optionbox">
					<select name="PEDIGREE_GENERATIONS">
					<?php
						for ($p=3; $p<=$MAX_PEDIGREE_GENERATIONS; $p++) {
							echo "<option value=\"".$p."\" " ;
							if ($p == $controller->PEDIGREE_GENERATIONS) echo "selected=\"selected\" ";
							echo ">".$p."</option>";
						}
					?>
					</select>
				</td>
				<td class="optionbox">
					<select name="clustersize">
					<?php
						for ($p=1; $p<6; $p = $p+2) {
							echo "<option value=\"".$p."\" " ;
							if ($p == $clustersize) echo "selected=\"selected\" ";
							echo ">".$p."</option>";
						}
					?>
					</select>
				</td>
				<td class="optionbox">
					<?php
					echo "<input name=\"hideflags\" type=\"checkbox\"";
					if ($hideflags) {echo " checked=\"checked\"";}
						echo " />";
					?>
				</td>
				<td class="optionbox">
					<?php
					echo "<input name=\"hidelines\" type=\"checkbox\"";
					if ($hidelines) {echo " checked=\"checked\"";}
					echo " />";
					?>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="5">
					<input type="submit" value="<?php echo i18n::translate('View'); ?>" />
				</td>
			</tr>
		</table>
 	  </form>
	</td></tr>
</table>
	
<?php } ?>
<!-- end of form -->

<!-- count records by type -->
<?php
$curgen=1;
$priv=0;
$count=0;
$miscount=0;
$missing = "";

for ($i=0; $i<($controller->treesize); $i++) {
	// -- check to see if we have moved to the next generation
	if ($i+1 >= pow(2, $curgen)) {$curgen++;}
	$person = Person::getInstance($controller->treeid[$i]);
	if (!empty($person)) {
		$pid = $controller->treeid[$i];
		$name = $person->getFullName();
		if ($name == i18n::translate('Private')) $priv++;
		$place = $person->getBirthPlace();
		if (empty($place)) {
			$latlongval[$i] = NULL;
		} else {
			$latlongval[$i] = get_lati_long_placelocation($person->getBirthPlace());
			if ($latlongval[$i] != NULL && $latlongval[$i]["lati"]=='0' && $latlongval[$i]["long"]=='0') {
				$latlongval[$i] = NULL;
			}
		}
		if ($latlongval[$i] != NULL) {
			$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]["lati"]);
			$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]["long"]);
			if (($lat[$i] != NULL) && ($lon[$i] != NULL)) {
				$count++;
			}
			else { // The place is in the table but has empty values
				if (!empty($name)) {
					if (!empty($missing)) $missing .= ",\n ";
					$addlist = "<a href=\"individual.php?pid=" . $pid . "\">". $name . "</a>";
					$missing .= $addlist;
					$miscount++;
				}
			}
		}
		else { // There was no place, or not listed in the map table
			if (!empty($name)) {
				if (!empty($missing)) $missing .= ",\n ";
				$addlist = "<a href=\"individual.php?pid=" . $pid . "\">". $name . "</a>";
				$missing .= $addlist;
				$miscount++;
			}
		}
	}
}
//<!-- end of count records by type -->

//<!-- start of map display -->
echo "<table ";
if (($cloudy_locked == 0) || ($theme_name != "Cloudy")) {
	echo "width=\"100%\"";
}
echo " style=\"tabs_table\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
echo "<tr>\n";
echo "<td valign=\"top\">\n";
echo "<img src=\"images/spacer.gif\" width=\"".$GOOGLEMAP_XSIZE."\" height=\"0\" alt=\"\" border=\"0\"/>\n";
echo "<div id=\"pm_map\" style=\"border: 1px solid gray; height: ".$GOOGLEMAP_YSIZE."px; font-size: 0.9em;";
if (($cloudy_locked) && ($theme_name == "Cloudy")) {
	echo " width: ".$GOOGLEMAP_XSIZE."px;";
}
echo " background-image: url('images/loading.gif'); background-position: center; background-repeat: no-repeat; overflow: hidden;\"></div>\n";
if (WT_USER_IS_ADMIN) {
	echo "<table width=\"100%\">";
	echo "<tr><td align=\"left\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=editconfig\">", i18n::translate('Manage GoogleMap configuration'), "</a>";
	echo "</td>\n";
	echo "<td align=\"center\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=places\">", i18n::translate('Edit geographic place locations'), "</a>";
	echo "</td>\n";
	echo "<td align=\"right\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=placecheck\">", i18n::translate('Place Check'), "</a>";
	echo "</td></tr>\n";
	echo "</table>\n";
}
echo "</td><td width=\"15px\">&nbsp;</td>\n";
echo "<td width=\"310px\" valign=\"top\">\n";
echo "<div id=\"side_bar\" style=\"width: 300px; font-size: 0.9em; overflow: auto; overflow-x: hidden; overflow-y: auto; height: ".$GOOGLEMAP_YSIZE."px; \"></div></td>\n";
echo "</tr>\n";
echo "</table>\n";
// display info under map
echo "<hr />";
echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">";
echo "  <tr>";
echo "	<td valign=\"top\">";
// print summary statistics
	if (isset($curgen)){
		$total=pow(2,$curgen)-1;
		$miss=$total-$count-$priv;
		echo i18n::plural(
			'%1$d individual displayed, out of the normal total of %2$d, from %3$d generations.',
			'%1$d individuals displayed, out of the normal total of %2$d, from %3$d generations.',
			$count,
			$count, $total, $curgen
		), '<br/>';
		echo "</td>\n";
		echo "  </tr>\n";
		echo "  <tr>\n";
		echo "	<td valign=\"top\">\n";
		if ($priv) {
			echo i18n::plural('%s individual is private.', '%s individuals are private.', $priv, $priv), '<br/>';
		}
		if ($count+$priv != $total) {
			if ($miscount == 1) {
				echo "<strong>".$miscount."</strong> ";
				echo " ".i18n::translate('individual is missing birth place map coordinates:')."<br />\n";
			} else if ($miscount > 1 && $miscount < 5) {
				echo "<strong>".$miscount."</strong> ";
				echo " ".i18n::translate('individuals are missing birth place map coordinates:')."<br />\n";
			} else if ($miscount > 21 && substr($miscount, -1, 1) > 1 && substr($miscount, -1, 1) < 5 && substr($miscount, -2, 1) != 1) {
				echo "<strong>".$miscount."</strong> ";
				echo " ".i18n::translate('individuals are missing birth place map coordinates:')."<br />\n";
			} else if ($miscount == 0) {
				echo " ".i18n::translate('No ancestors in the database.')."<br />\n";
			} else {
				echo "<strong>".$miscount."</strong> ";
				echo " ".i18n::translate('individuals are missing birth place map coordinates:')."<br />\n";
			}
			echo $missing . "<br />\n";
		}
	}
echo "	</td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</div>";
?>
<!-- end of map display -->

<!-- Start of map scripts -->
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $GOOGLEMAP_API_KEY; ?>" type="text/javascript"></script>

<script type="text/javascript">

//<![CDATA[

if (GBrowserIsCompatible()) {
	
// this variable will collect the html which will eventually be placed in the side_bar
var side_bar_html = "";

// arrays to hold copies of the markers and html used by the side_bar
// because the function closure trick doesnt work there
var gmarkers = [];
var i = 0;
var lastlinkid;

// === Create an associative array of GIcons() ===
var gicons = [];
// First the templates
gicons["LEFT"] = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon2L.png")
gicons["LEFT"].iconSize = new GSize(32,32);
gicons["LEFT"].iconAnchor = new GPoint(28,28);
gicons["LEFT"].infoWindowAnchor = new GPoint(11,4);
gicons["LEFT"].shadow = "modules/googlemap/images/shadow-left-large.png";
gicons["LEFT"].shadowSize = new GSize(49, 32);
gicons["LEFT"].transparent = "modules/googlemap/images/transparent-left-large.png";
gicons["LEFT"].imageMap = [28,28,7,20,2,13,2,5,8,0,17,2,21,8,22,16,28,28];

gicons["RIGHT"] = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon2R.png")
gicons["RIGHT"].iconSize = new GSize(32,32);
gicons["RIGHT"].iconAnchor = new GPoint(2,28);
gicons["RIGHT"].infoWindowAnchor = new GPoint(21,4);
gicons["RIGHT"].shadow = "modules/googlemap/images/shadow-right-large.png";
gicons["RIGHT"].shadowSize = new GSize(49, 32);
gicons["RIGHT"].transparent = "modules/googlemap/images/transparent-right-large.png";
gicons["RIGHT"].imageMap = [2,28,10,15,10,6,19,0,25,2,30,8,29,15,23,20,14,21,2,28];

gicons["LEFTsmall"] = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon2Ls.png")
gicons["LEFTsmall"].iconSize = new GSize(24, 24);
gicons["LEFTsmall"].iconAnchor = new GPoint(22,22);
gicons["LEFTsmall"].infoWindowAnchor = new GPoint(12, 0);
gicons["LEFTsmall"].shadow = "modules/googlemap/images/shadow-left-small.png";
gicons["LEFTsmall"].shadowSize = new GSize(37, 24);
gicons["LEFTsmall"].transparent = "modules/googlemap/images/transparent-left-small.png";
gicons["LEFTsmall"].imageMap = [21,21,11,15,3,13,0,9,0,5,5,1,11,1,15,6,16,12,21,21];

gicons["RIGHTsmall"] = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon2Rs.png")
gicons["RIGHTsmall"].iconSize = new GSize(24, 24);
gicons["RIGHTsmall"].iconAnchor = new GPoint(2,22);
gicons["RIGHTsmall"].infoWindowAnchor = new GPoint(12, 0);
gicons["RIGHTsmall"].shadow = "modules/googlemap/images/shadow-right-small.png";
gicons["RIGHTsmall"].shadowSize = new GSize(37, 24);
gicons["RIGHTsmall"].transparent = "modules/googlemap/images/transparent-right-small.png";
gicons["RIGHTsmall"].imageMap = [3,20,9,11,9,4,14,1,19,1,23,4,23,12,17,15,9,16,3,20];

// Now the icons for each generation
gicons["1"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon1.png")

gicons["2"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon2.png")
gicons["2L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon2L.png")
gicons["2R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon2R.png")
// Actually overkill.  We are not going to get to the 4th and fifth icon in this generation.
gicons["2Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon2Ls.png")
gicons["2Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon2Rs.png")

gicons["3"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon3.png")
gicons["3L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon3L.png")
gicons["3R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon3R.png")
gicons["3Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon3Ls.png")
gicons["3Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon3Rs.png")

gicons["4"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon4.png")
gicons["4L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon4L.png")
gicons["4R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon4R.png")
gicons["4Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon4Ls.png")
gicons["4Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon4Rs.png")

gicons["5"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon5.png")
gicons["5L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon5L.png")
gicons["5R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon5R.png")
gicons["5Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon5Ls.png")
gicons["5Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon5Rs.png")

gicons["6"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon6.png")
gicons["6L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon6L.png")
gicons["6R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon6R.png")
gicons["6Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon6Ls.png")
gicons["6Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon6Rs.png")

gicons["7"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon7.png")
gicons["7L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon7L.png")
gicons["7R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon7R.png")
gicons["7Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon7Ls.png")
gicons["7Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon7Rs.png")

gicons["8"]  = new GIcon(G_DEFAULT_ICON, "modules/googlemap/images/icon8.png")
gicons["8L"] = new GIcon(gicons["LEFT"], "modules/googlemap/images/icon8L.png")
gicons["8R"] = new GIcon(gicons["RIGHT"], "modules/googlemap/images/icon8R.png")
gicons["8Ls"] = new GIcon(gicons["LEFTsmall"], "modules/googlemap/images/icon8Ls.png")
gicons["8Rs"] = new GIcon(gicons["RIGHTsmall"], "modules/googlemap/images/icon8Rs.png")

// / A function to create the marker and set up the event window
function createMarker(point, name, html, mhtml, icontype) {
// === create a marker with the requested icon ===
var marker = new GMarker(point, gicons[icontype]);
var linkid = "link"+i;

GEvent.addListener(marker, "click", function() {
marker.openInfoWindowHtml(mhtml);
document.getElementById(linkid).className = 'person_box';
lastlinkid=linkid;
});

// save the info we need to use later for the side_bar
gmarkers[i] = marker;

// add a line to the side_bar html
side_bar_html += '<br /><div id="'+linkid+'"><a href="javascript:myclick(' + i + ')">' + html +'</a><br></div>';
i++;
return marker;
};

// This function picks up the click and opens the corresponding info window
function myclick(i) {
GEvent.trigger(gmarkers[i], "click");
}
function Map_type() {}
	Map_type.prototype = new GControl();

	Map_type.prototype.refresh = function()
	{
		this.button1.className = 'non_active';
		if (this.pm_map.getCurrentMapType() != G_NORMAL_MAP) {
			this.button2.className = 'non_active';
		} else {
			this.button2.className = 'active';
		}
		if (this.pm_map.getCurrentMapType() != G_SATELLITE_MAP) {
			this.button3.className = 'non_active';
		} else {
			this.button3.className = 'active';
		}
		if (this.pm_map.getCurrentMapType() != G_HYBRID_MAP) {
			this.button4.className = 'non_active';
		} else {
			this.button4.className = 'active';
		}
		if (this.pm_map.getCurrentMapType() != G_PHYSICAL_MAP) {
			this.button5.className = 'non_active';
		} else {
			this.button5.className = 'active';
		}
	}

	Map_type.prototype.initialize = function(pm_map)
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

		button1.onclick = function() { pm_map.setCenter(bounds.getCenter(), pm_map.getBoundsZoomLevel(bounds)); return false; };
		button2.onclick = function() { pm_map.setMapType(G_NORMAL_MAP); return false; };
		button3.onclick = function() { pm_map.setMapType(G_SATELLITE_MAP); return false; };
		button4.onclick = function() { pm_map.setMapType(G_HYBRID_MAP); return false; };
		button5.onclick = function() { pm_map.setMapType(G_PHYSICAL_MAP); return false; };

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
		this.pm_map = pm_map;
		pm_map.getContainer().appendChild(list);
		return list;
	}

	// create the map
	Map_type.prototype.getDefaultPosition = function()
	{
		return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(2, 2));
	}
	var map_type;
	var pm_map = new GMap2(document.getElementById("pm_map"));
	map_type = new Map_type();
	pm_map.addControl(map_type);
	GEvent.addListener(pm_map, 'maptypechanged', function()
	{
		map_type.refresh();
	});

// create the map
var bounds = new GLatLngBounds();
// for further street view
//pm_map.addControl(new GLargeMapControl3D(true));
pm_map.addControl(new GLargeMapControl3D());
pm_map.addControl(new GScaleControl());
var mini = new GOverviewMapControl();
pm_map.addControl(mini);
// displays blank minimap - probably google api's bug
//mini.hide();

function wheelevent(e)
{
	if (true){//document.getElementById("prevent").checked
			if (!e){
					e = window.event
			}
	if (e.preventDefault){
			e.preventDefault()
	}
	e.returnValue = false;
}}

GEvent.addDomListener(pm_map.getContainer(), "DOMMouseScroll", wheelevent);
pm_map.getContainer().onmousewheel = wheelevent;

<?php
// add the points
$curgen=1;
$priv=0;
$count=0;
$event = "<img src='modules/googlemap/images/sq1.png' width='10' height='10'>" .
	 "<strong>&nbsp;".i18n::translate('Root').":&nbsp;</strong>";
$colored_line = array("1"=>"#FF0000","2"=>"#0000FF","3"=>"#00FF00",
				"4"=>"#FFFF00","5"=>"#00FFFF","6"=>"#FF00FF",
				"7"=>"#C0C0FF","8"=>"#808000");

for ($i=0; $i<($controller->treesize); $i++) {
	// moved up to grab the sex of the individuals
	$person = Person::getInstance($controller->treeid[$i]);
	if (!empty($person)) {
		$pid = $controller->treeid[$i];
		$indirec = $person->getGedcomRecord();
		$sex = $person->getSex();
		$bplace = trim($person->getBirthPlace());
		$bdate = $person->getBirthDate();
		$name = $person->getFullName();

		// -- check to see if we have moved to the next generation
		if ($i+1 >= pow(2, $curgen)) {
			$curgen++;
		}
		$relationship=get_relationship_name(get_relationship($controller->rootid, $pid, false));
		if (empty($relationship)) $relationship=i18n::translate('self');
		$event = "<img src='modules/googlemap/images/sq".$curgen.".png' width='10' height='10'>".
			 "<strong>&nbsp;".$relationship.":&nbsp;</strong>";

		// add thumbnail image
		$image = "";
		if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES && showFact("OBJE", $pid)) {
			$object = find_highlighted_object($pid, WT_GED_ID, $indirec);
			if (!empty($object["thumb"])) {
				$size = findImageSize($object["thumb"]);
				$class = "pedigree_image_portrait";
				if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
				if ($TEXT_DIRECTION=="rtl") $class .= "_rtl";
				$image = "<img src='{$object["thumb"]}' vspace='0' hspace='0' class='{$class}' alt ='' title='' >";
			} else {
				$class = "pedigree_image_portrait";
				if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
				$sex = $person->getSex();
				$image = "<img src=\'./";
				if ($sex == 'F') { $image .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_F"]["other"]; }
				elseif ($sex == 'M') { $image .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_M"]["other"]; }
				else { $image .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_U"]["other"]; }
				$image .="\' align=\'left\' class=\'".$class."\' border=\'none\' alt=\'\' />";
			}
		}
		// end of add image

		$dataleft  = $image . $event . addslashes($name);
		$datamid   = " <span><a href='individual.php?pid=". $pid . "' id='alturl' title='" . i18n::translate('Individual information') . "'>";
		if ($TEXT_DIRECTION == "rtl") $datamid .= PrintReady("(".$pid.")");
		else $datamid .= "(". $pid . ")";
		$datamid  .= "</a></span>";
		$dataright = "<br /><strong>". i18n::translate('Birth:') . "&nbsp;</strong>" .
				addslashes($bdate->Display(false))."<br />".$bplace;
	
		$latlongval[$i] = get_lati_long_placelocation($person->getBirthPlace());
		if ($latlongval[$i] != NULL){
			$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]["lati"]);
			$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]["long"]);
			if (!($lat[$i] == NULL && $lon[$i] == NULL) && !($lat[$i] == 0 && $lon[$i] == 0)) {
				if ((!$hideflags) && ($latlongval[$i]["icon"] != NULL)) {
					$flags[$i] = $latlongval[$i]["icon"];
					$ffile = strrchr($latlongval[$i]["icon"], '/');
					$ffile = substr($ffile,1, strpos($ffile, '.')-1);
					if (empty($flags[$ffile])) {
						$flags[$ffile] = $i; // Only generate the flag once
						if (($lat[$i] != NULL) && ($lon[$i] != NULL)) {
							echo "var point = new GLatLng(" . $lat[$i] . "," . $lon[$i]. ");\n";
							echo "var Marker1_0_flag = new GIcon();\n";
							echo "Marker1_0_flag.image = \"". $flags[$i]. "\";\n";
							echo "Marker1_0_flag.shadow = \"modules/googlemap/images/flag_shadow.png\";\n";
							echo "Marker1_0_flag.iconSize = new GSize(25, 15);\n";
							echo "Marker1_0_flag.shadowSize = new GSize(35, 45);\n";
							echo "Marker1_0_flag.iconAnchor = new GPoint(1, 45);\n";
							echo "Marker1_0_flag.infoWindowAnchor = new GPoint(5, 1);\n";
							echo "var Marker1_0 = new GMarker(point, {icon:Marker1_0_flag});\n";
							echo "pm_map.addOverlay(Marker1_0);\n\n";
						}
					}
				}
				$marker_number = "$curgen";
				$dups=0;
				for ($k=0; $k<$i; $k++) {
					if ($latlongval[$i] == $latlongval[$k]) {
						if ($clustersize == 1) {
							$lon[$i] = $lon[$i]+0.0025;
							$lat[$i] = $lat[$i]+0.0025;
						}
						else {
							$dups++;
							switch($dups) {
								case 1: $marker_number = "$curgen" . "L"; break;
								case 2: $marker_number = "$curgen" . "R"; break;
								case 3: if ($clustersize==5) {
									$marker_number = "$curgen" . "Ls"; break;
									}
								case 4: if ($clustersize==5) {
									$marker_number = "$curgen" . "Rs"; break;
									}
								case 5: //adjust position where markers have same coodinates
								default: $marker_number = "$curgen";
									$lon[$i] = $lon[$i]+0.0025;
									$lat[$i] = $lat[$i]+0.0025;
									break;
							}
						 }
					}
				}
				echo "var point = new GLatLng(" . $lat[$i] . "," . $lon[$i]. ");\n";
				echo "var marker = createMarker(point, \"" . addslashes($name). "\",\n\t\"<div>".$dataleft.$datamid.$dataright."</div>\", \"";
				echo "<div class='iwstyle'>";
				echo "<a href='module.php?ged={$GEDCOM}&mod=googlemap&mod_action=pedigree_map&rootid={$pid}&PEDIGREE_GENERATIONS={$PEDIGREE_GENERATIONS}";
				if ($hideflags) echo "&hideflags=1";
				if ($hidelines) echo "&hidelines=1";
				if ($clustersize != 5) echo "&clustersize=". $clustersize; // ignoring the default of 5
				echo "' title='" . i18n::translate('Pedigree Map') . "'>" . $dataleft . "</a>" . $datamid . $dataright . "</div>\", \"".$marker_number."\");\n";
				echo "pm_map.addOverlay(marker);\n";
	
				if (!$hidelines) {
					$to_child = (intval(($i-1)/2)); // Draw a line from parent to child
					if (($to_child > -1) && ($to_child < 127) && (!empty($lat[$to_child]))) {
						if (($lat[$to_child]!=NULL) && (($lat[$to_child]!=$lat[$i]) &&
						   ($lon[$to_child]!=$lon[$i]))) {
							echo "var pline = new GPolyline([new GLatLng(".$lat[$i].",".$lon[$i]."), ";
							echo "new GLatLng(".$lat[$to_child].",".$lon[$to_child].")";
							echo "], \"".$colored_line[$curgen]."\", 3);\n";
							echo "pm_map.addOverlay(pline);\n";
						}
					}
				}
				echo "bounds.extend(point);\n";
				echo "\n";
				$count++;
			}
		}
	}
	else {
		$latlongval[$i] = NULL;
	}
}
?>
pm_map.setCenter(bounds.getCenter());
pm_map.setZoom(pm_map.getBoundsZoomLevel(bounds));

GEvent.addListener(pm_map,"infowindowclose", function() {
document.getElementById(lastlinkid).className = 'person_box:target';
});

// put the assembled side_bar_html contents into the side_bar div
document.getElementById("side_bar").innerHTML = side_bar_html;

// === create the context menu div ===
	  var contextmenu = document.createElement("div");
	  contextmenu.style.visibility="hidden";
	  contextmenu.innerHTML = '<a href="javascript:zoomIn()"><div class="optionbox">&nbsp;&nbsp;<?php echo i18n::translate('Zoom in');?>&nbsp;&nbsp;</div></a>'
							+ '<a href="javascript:zoomOut()"><div class="optionbox">&nbsp;&nbsp;<?php echo i18n::translate('Zoom out');?>&nbsp;&nbsp;</div></a>'
							+ '<a href="javascript:zoomInHere()"><div class="optionbox">&nbsp;&nbsp;<?php echo i18n::translate('Zoom in here');?>&nbsp;&nbsp;</div></a>'
							+ '<a href="javascript:zoomOutHere()"><div class="optionbox">&nbsp;&nbsp;<?php echo i18n::translate('Zoom out here');?>&nbsp;&nbsp;</div></a>'
							+ '<a href="javascript:centreMapHere()"><div class="optionbox">&nbsp;&nbsp;<?php echo i18n::translate('Center map here');?>&nbsp;&nbsp;</div></a>';
	  pm_map.getContainer().appendChild(contextmenu);

	  // === listen for singlerightclick ===
	  GEvent.addListener(pm_map,"singlerightclick",function(pixel,tile) {
		// store the "pixel" info in case we need it later
		// adjust the context menu location if near an egde
		// create a GControlPosition
		// apply it to the context menu, and make the context menu visible
		clickedPixel = pixel;
		var x=pixel.x;
		var y=pixel.y;
		if (x > pm_map.getSize().width - 120) { x = pm_map.getSize().width - 120 }
		if (y > pm_map.getSize().height - 100) { y = pm_map.getSize().height - 100 }
		var pos = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(x,y));
		pos.apply(contextmenu);
		contextmenu.style.visibility = "visible";
	  });

	  // === functions that perform the context menu options ===
	  function zoomIn() {
		// perform the requested operation
		pm_map.zoomIn();
		// hide the context menu now that it has been used
		contextmenu.style.visibility="hidden";
	  }
	  function zoomOut() {
		// perform the requested operation
		pm_map.zoomOut();
		// hide the context menu now that it has been used
		contextmenu.style.visibility="hidden";
	  }
	  function zoomInHere() {
		// perform the requested operation
		var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
		pm_map.zoomIn(point,true);
		// hide the context menu now that it has been used
		contextmenu.style.visibility="hidden";
	  }
	  function zoomOutHere() {
		// perform the requested operation
		var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
		pm_map.setCenter(point,pm_map.getZoom()-1); // There is no pm_map.zoomOut() equivalent
		// hide the context menu now that it has been used
		contextmenu.style.visibility="hidden";
	  }
	  function centreMapHere() {
		// perform the requested operation
		var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
		pm_map.setCenter(point);
		// hide the context menu now that it has been used
		contextmenu.style.visibility="hidden";
	  }


	  // === If the user clicks on the map, close the context menu ===
	  GEvent.addListener(pm_map, "click", function() {
		contextmenu.style.visibility="hidden";
	  });
	<?php if ($GOOGLEMAP_PH_CONTROLS) {?>
		// hide controls
		GEvent.addListener(pm_map, 'mouseout', function() {pm_map.hideControls();});
		// show controls
		GEvent.addListener(pm_map, 'mouseover', function() {pm_map.showControls();});
		GEvent.trigger(pm_map, 'mouseout');
		<?php
	}
	if ($GOOGLEMAP_PH_WHEEL) echo "pm_map.enableScrollWheelZoom();\n";
	echo "	pm_map.setMapType($GOOGLEMAP_MAP_TYPE);\n";
	?>
// End context menu creation
}

	else {
	  alert("Sorry, the Google Maps API is not compatible with this browser");
	}

	// This Javascript is based on code provided by the
	// Blackpool Community Church Javascript Team
	// http://www.commchurch.freeserve.co.uk/
	// http://econym.googlepages.com/index.htm

//]]>
</script>
<?php
if ($controller->isPrintPreview()) {
	echo "<br /><br /><br />\n";
}
print_footer();
?>
