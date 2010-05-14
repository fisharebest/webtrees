<?php
/**
 * Displays a fan chart
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'fanchart.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

/**
 * split and center text by lines
 *
 * @param string $data input string
 * @param int $maxlen max length of each line
 * @return string $text output string
 */
function split_align_text($data, $maxlen) {
	global $RTLOrd;

	$lines = explode("\n", $data);
	// more than 1 line : recursive calls
	if (count($lines)>1) {
		$text = "";
		foreach ($lines as $indexval => $line) $text .= split_align_text($line, $maxlen)."\n";
		return $text;
	}
	// process current line word by word
	$split = explode(" ", $data);
	$text = "";
	$line = "";
	// do not split hebrew line

	$found = false;
	foreach($RTLOrd as $indexval => $ord) {
		if (strpos($data, chr($ord)) !== false) $found=true;
	}
	if ($found) $line=$data;
	else
	foreach ($split as $indexval => $word) {
		$len = strlen($line);
		//if (!empty($line) and ord($line{0})==215) $len/=2; // hebrew text
		$wlen = strlen($word);
		// line too long ?
		if (($len+$wlen)<$maxlen) {
			if (!empty($line)) $line .= " ";
			$line .= "$word";
		}
		else {
			$p = max(0,floor(($maxlen-$len)/2));
			if (!empty($line)) {
				$line = str_repeat(" ", $p) . "$line"; // center alignment using spaces
				$text .= "$line\n";
			}
			$line = $word;
		}
	}
	// last line
	if (!empty($line)) {
		$len = strlen($line);
		if (in_array(ord($line{0}),$RTLOrd)) $len/=2;
		$p = max(0,floor(($maxlen-$len)/2));
		$line = str_repeat(" ", $p) . "$line"; // center alignment using spaces
		$text .= "$line";
	}
	return $text;
}

/**
 * print ancestors on a fan chart
 *
 * @param array $treeid ancestry pid
 * @param int $fanw fan width in px (default=640)
 * @param int $fandeg fan size in deg (default=270)
 */
function print_fan_chart($treeid, $fanw=640, $fandeg=270) {
	global $PEDIGREE_GENERATIONS, $fan_width, $fan_style;
	global $name, $SHOW_ID_NUMBERS, $view, $TEXT_DIRECTION;
	global $stylesheet, $print_stylesheet;
	global $WT_IMAGE_DIR, $WT_IMAGES, $LINK_ICONS, $GEDCOM, $SERVER_URL;
	global $fanChart;

	// check for GD 2.x library
	if (!defined("IMG_ARC_PIE")) {
		echo "<span class=\"error\">".i18n::translate('PHP server misconfiguration: GD 2.x library required to use image functions.')."</span>";
		echo " <a href=\"" . i18n::translate('http://www.php.net/gd') . "\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["help"]["small"]."\" class=\"icon\" alt=\"\" /></a><br /><br />";
		return false;
	}
	if (!function_exists("ImageTtfBbox")) {
		echo "<span class=\"error\">".i18n::translate('PHP server misconfiguration: FreeType library required to use TrueType fonts.')."</span>";
		echo " <a href=\"" . i18n::translate('http://www.php.net/gd') . "\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["help"]["small"]."\" class=\"icon\" alt=\"\" /></a><br /><br />";
		return false;
	}

	// Validate
	if (!file_exists($fanChart['font'])) {
		echo '<span class="error">', i18n::translate('Font file not found on PHP server'), ' : ', $fanChart['font']. '</span>';
		return false;
	}

	$fanChart['size'] = intval($fanChart['size']);
	if ($fanChart['size']<2) $fanChart['size'] = 7;

	if (empty($fanChart['color']) || $fanChart['color']{0}!='#') $fanChart['color'] = '#000000';
	if (empty($fanChart['bgColor']) || $fanChart['bgColor']{0}!='#') $fanChart['bgColor'] = '#EEEEEE';
	if (empty($fanChart['bgMColor']) || $fanChart['bgMColor']{0}!='#') $fanChart['bgMColor'] = '#D0D0AC';
	if (empty($fanChart['bgFColor']) || $fanChart['bgFColor']{0}!='#') $fanChart['bgFColor'] = '#D0ACD0';

	$treesize=count($treeid);
	if ($treesize<1) return;

	// generations count
	$gen=log($treesize)/log(2)-1;
	$sosa=$treesize-1;

	// fan size
	if ($fandeg==0) $fandeg=360;
	$fandeg=min($fandeg, 360);
	$fandeg=max($fandeg, 90);
	$cx=$fanw/2-1; // center x
	$cy=$cx; // center y
	$rx=$fanw-1;
	$rw=$fanw/($gen+1);
	$fanh=$fanw; // fan height
	if ($fandeg==180) $fanh=round($fanh*($gen+1)/($gen*2));
	if ($fandeg==270) $fanh=round($fanh*.86);
	$scale=$fanw/640;

	// image init
	$image = ImageCreate($fanw, $fanh);
	$black = ImageColorAllocate($image, 0, 0, 0);
	$white = ImageColorAllocate($image, 0xFF, 0xFF, 0xFF);
	ImageFilledRectangle ($image, 0, 0, $fanw, $fanh, $white);
	ImageColorTransparent($image, $white);

	$color = ImageColorAllocate($image, hexdec(substr($fanChart['color'],1,2)), hexdec(substr($fanChart['color'],3,2)), hexdec(substr($fanChart['color'],5,2)));
	$bgcolor = ImageColorAllocate($image, hexdec(substr($fanChart['bgColor'],1,2)), hexdec(substr($fanChart['bgColor'],3,2)), hexdec(substr($fanChart['bgColor'],5,2)));
	$bgcolorM = ImageColorAllocate($image, hexdec(substr($fanChart['bgMColor'],1,2)), hexdec(substr($fanChart['bgMColor'],3,2)), hexdec(substr($fanChart['bgMColor'],5,2)));
	$bgcolorF = ImageColorAllocate($image, hexdec(substr($fanChart['bgFColor'],1,2)), hexdec(substr($fanChart['bgFColor'],3,2)), hexdec(substr($fanChart['bgFColor'],5,2)));

	// imagemap
	$imagemap="<map id=\"fanmap\" name=\"fanmap\">";

	// loop to create fan cells
	while ($gen>=0) {
		// clean current generation area
		$deg2=360+($fandeg-180)/2;
		$deg1=$deg2-$fandeg;
		ImageFilledArc($image, $cx, $cy, $rx, $rx, $deg1, $deg2, $bgcolor, IMG_ARC_PIE);
		$rx-=3;

		// calculate new angle
		$p2=pow(2, $gen);
		$angle=$fandeg/$p2;
		$deg2=360+($fandeg-180)/2;
		$deg1=$deg2-$angle;
		// special case for rootid cell
		if ($gen==0) {
			$deg1=90;
			$deg2=360+$deg1;
		}

		// draw each cell
		while ($sosa >= $p2) {
			$pid=$treeid[$sosa];
			if (!empty($pid)) {
				$indirec=find_gedcom_record($pid, WT_GED_ID, WT_USER_CAN_EDIT);

				if ($sosa%2) $bg=$bgcolorF;
				else $bg=$bgcolorM;
				if ($sosa==1) {
					$bg=$bgcolor; // sex unknown
					if (strpos($indirec, "\n1 SEX F")!==false) $bg=$bgcolorF;
					elseif (strpos($indirec, "\n1 SEX M")!==false) $bg=$bgcolorM;
				}
				ImageFilledArc($image, $cx, $cy, $rx, $rx, $deg1, $deg2, $bg, IMG_ARC_PIE);
				$person =Person::getInstance($pid);
				$name   =$person->getFullName();
				$addname=$person->getAddName();

//$name = str_replace(array('<span class="starredname">', '</span>'), '', $name);
//$addname = str_replace(array('<span class="starredname">', '</span>'), '', $addname);
//$name = str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $name); //@@
//$addname = str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $addname); //@@
// ToDo - print starred names underlined - 1985154
// Todo - print Arabic letters combined - 1360209

				$text = reverseText($name) . "\n";
				if (!empty($addname)) $text .= reverseText($addname). "\n";

				if (displayDetailsById($pid)) {
					$birthrec = get_sub_record(1, "1 BIRT", $indirec);
					$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $birthrec, $match);
					if ($ct>0) $text.= trim($match[1]);
					$deathrec = get_sub_record(1, "1 DEAT", $indirec);
					$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $deathrec, $match);
					if ($ct>0) $text.= "-".trim($match[1]);
				}

				$text = unhtmlentities($text);
				$text = strip_tags($text);
//Do we still need?

				// split and center text by lines
				$wmax = floor($angle*7/$fanChart['size']*$scale);
				$wmax = min($wmax, 35*$scale);
				if ($gen==0) $wmax = min($wmax, 17*$scale);
				$text = split_align_text($text, $wmax);

				// text angle
				$tangle = 270-($deg1+$angle/2);
				if ($gen==0) $tangle=0;

				// calculate text position
				$bbox=ImageTtfBbox((double)$fanChart['size'], 0, $fanChart['font'], $text);
				$textwidth = $bbox[4];
				$deg = $deg1+.44;
				if ($deg2-$deg1>40) $deg = $deg1+($deg2-$deg1)/11;
				if ($deg2-$deg1>80) $deg = $deg1+($deg2-$deg1)/7;
				if ($deg2-$deg1>140) $deg = $deg1+($deg2-$deg1)/4;
				if ($gen==0) $deg=180;
				$rad=deg2rad($deg);
				$mr=($rx-$rw/4)/2;
				if ($gen>0 and $deg2-$deg1>80) $mr=$rx/2;
				$tx=$cx + ($mr) * cos($rad);
				$ty=$cy - $mr * -sin($rad);
				if ($sosa==1) $ty-=$mr/2;

				// print text
				ImageTtfText($image, (double)$fanChart['size'], $tangle, $tx, $ty, $color, $fanChart['font'], $text);

				$imagemap .= "<area shape=\"poly\" coords=\"";
				// plot upper points
				$mr=$rx/2;
				$deg=$deg1;
				while ($deg<=$deg2) {
					$rad=deg2rad($deg);
					$tx=round($cx + ($mr) * cos($rad));
					$ty=round($cy - $mr * -sin($rad));
					$imagemap .= "$tx, $ty, ";
					$deg+=($deg2-$deg1)/6;
				}
				// plot lower points
				$mr=($rx-$rw)/2;
				$deg=$deg2;
				while ($deg>=$deg1) {
					$rad=deg2rad($deg);
					$tx=round($cx + ($mr) * cos($rad));
					$ty=round($cy - $mr * -sin($rad));
					$imagemap .= "$tx, $ty, ";
					$deg-=($deg2-$deg1)/6;
				}
				// join first point
				$mr=$rx/2;
				$deg=$deg1;
				$rad=deg2rad($deg);
				$tx=round($cx + ($mr) * cos($rad));
				$ty=round($cy - $mr * -sin($rad));
				$imagemap .= "$tx, $ty";
				// add action url
				$tempURL = "javascript://".htmlspecialchars(strip_tags($name));
				if ($SHOW_ID_NUMBERS) $tempURL .= " (".$pid.")";
				$imagemap .= "\" href=\"$tempURL\" ";
				$tempURL = "fanchart.php?rootid={$pid}&PEDIGREE_GENERATIONS={$PEDIGREE_GENERATIONS}&fan_width={$fan_width}&fan_style={$fan_style}";
				if (!empty($view)) $tempURL .= "&view={$view}";
				$count=0;
				$lbwidth=200;
				echo "<div id=\"I".$pid.".".$count."links\" style=\"position:absolute; >";
				echo "left:".$tx."px; top:".$ty."px; width: ".($lbwidth)."px; visibility:hidden; z-index:'100';\">";
				echo "<table class=\"person_box\"><tr><td class=\"details1\">";
				echo "<a href=\"individual.php?pid=$pid\" class=\"name1\">" . PrintReady($name);
				if (!empty($addname)) echo "<br />" . PrintReady($addname);
				echo "</a>";
				echo "<br /><a href=\"pedigree.php?rootid=$pid\" >".i18n::translate('Pedigree Tree')."</a>";
				if (file_exists(WT_ROOT.'modules/googlemap/pedigree_map.php')) {
					echo "<br /><a href=\"module.php?mod=googlemap&mod_action=pedigree_map&rootid=".$pid."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Pedigree Map')."</a>";
				}
				if (WT_USER_GEDCOM_ID && WT_USER_GEDCOM_ID!=$pid) {
					echo "<br /><a href=\"".encode_url("relationship.php?pid1=".WT_USER_GEDCOM_ID."&pid2={$pid}&ged={$GEDCOM}")."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Relationship to me')."</a>";
				}
				echo "<br /><a href=\"descendancy.php?pid=$pid\" >".i18n::translate('Descendancy chart')."</a>";
				echo "<br /><a href=\"ancestry.php?rootid=$pid\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Ancestry chart')."</a>";
				echo "<br /><a href=\"compact.php?rootid=$pid\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Compact Chart')."</a>";
				echo "<br /><a href=\"".encode_url($tempURL)."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Circle diagram')."</a>";
				echo "<br /><a href=\"hourglass.php?pid=$pid\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Hourglass chart')."</a>";
				echo "<br /><a href=\"treenav.php?rootid=$pid\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".i18n::translate('Interactive tree')."</a>";
				if ($sosa>=1) {
					$famids = find_sfamily_ids($pid);
					//-- make sure there is more than 1 child in the family with parents
					$cfamids = find_family_ids($pid);
					$num=0;
					for ($f=0; $f<count($cfamids); $f++) {
						$famrec = find_family_record($cfamids[$f], WT_GED_ID);
						if ($famrec) $num += preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
					}
					if ($famids ||($num>1)) {
						//-- spouse(s) and children
						for ($f=0; $f<count($famids); $f++) {
							$famrec = find_family_record(trim($famids[$f]), WT_GED_ID);
							if ($famrec) {
								$parents = find_parents($famids[$f]);
								if ($parents) {
									if ($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
									else $spid=$parents["WIFE"];
									$person=Person::getInstance($spid);
									if ($person) {
										echo '<br /><a href="', $person->getLinkUrl(), '" class="name1">', $person->getFullName(), '</a>';
									}
								}
								$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
								for ($i=0; $i<$num; $i++) {
									$person=Person::getInstance($smatch[$i][1]);
									if ($person) {
										echo '<br />&nbsp;&nbsp;<a href="', $person->getLinkUrl(), '" class="name1">&lt; ', $person->getFullName(), '</a>';
									}
								}
							}
						}
						//-- siblings
						for ($f=0; $f<count($cfamids); $f++) {
							$famrec = find_family_record($cfamids[$f], WT_GED_ID);
							if ($famrec) {
								$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
								if ($num>2) echo "<br /><span class=\"name1\">".i18n::translate('Siblings')."</span>";
								if ($num==2) echo "<br /><span class=\"name1\">".i18n::translate('Sibling')."</span>";
								for($i=0; $i<$num; $i++) {
									$cpid = $smatch[$i][1];
									if ($cpid!=$pid) {
										$person=Person::getInstance($cpid);
										if ($person) {
											echo '<br />&nbsp;&nbsp;<a href="', $person->getLinkUrl(), '" class="name1"> ', $person->getFullName(), '</a>';
										}
									}
								}
							}
						}
					}
				}
				echo "</td></tr></table>";
				echo "</div>";
				$imagemap .= " onclick=\"show_family_box('".$pid.".".$count."', 'relatives'); return false;\"";
				$imagemap .= " onmouseout=\"family_box_timeout('".$pid.".".$count."'); return false;\"";
				$imagemap .= " alt=\"".PrintReady(strip_tags($name))."\" title=\"".PrintReady(strip_tags($name))."\" />";
			}
			$deg1-=$angle;
			$deg2-=$angle;
			$sosa--;
		}
		$rx-=$rw;
		$gen--;
	}

	$imagemap .= "</map>";
	echo $imagemap;

	if (!empty($SERVER_URL)) {
		ImageStringUp($image, 1, $fanw-10, $fanh/3, $SERVER_URL, $color);
	} else {
		// PGV banner ;-)
		ImageStringUp($image, 1, $fanw-10, $fanh/3, WT_WEBTREES_URL, $color);
	}

	// here we cannot send image to browser ('header already sent')
	// and we dont want to use a tmp file

	// step 1. save image data in a session variable
	ob_start();
	ImagePng($image);
	$image_data = ob_get_contents();
	ob_end_clean();
	$image_data = serialize($image_data);
	unset ($_SESSION['image_data']);
	$_SESSION['image_data']=$image_data;

	// step 2. call imageflush.php to read this session variable and display image
	// note: arg "image_name=" is to avoid image miscaching
	$image_name= "V".time();
	unset($_SESSION[$image_name]);		// statisticsplot.php uses this to hold a file name to send to browser
	$image_title=preg_replace("~<.*>~", "", $name) . " " . i18n::translate('Circle diagram');
	echo "<p align=\"center\" >";
	echo "<img src=\"imageflush.php?image_type=png&amp;image_name=$image_name&amp;height=$fanh&amp;width=$fanw\" width=\"$fanw\" height=\"$fanh\" border=\"0\" alt=\"$image_title\" title=\"$image_title\" usemap=\"#fanmap\" />";
	echo "</p>";
	ImageDestroy($image);
}

// Extract form parameters
$rootid   =safe_GET_xref('rootid');
$fan_style=safe_GET_integer('fan_style',  2,  4,  3);
$fan_width=safe_GET_integer('fan_width',  50, 300, 100);
$PEDIGREE_GENERATIONS=safe_GET_integer('PEDIGREE_GENERATIONS', 2, $MAX_PEDIGREE_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);

// Validate form parameters
$rootid = check_rootid($rootid);

$person =Person::getInstance($rootid);
$name   =$person->getFullName();
$addname=$person->getAddName();

// -- print html header information
print_header(PrintReady($name) . " " . i18n::translate('Circle diagram'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

if (strlen($name)<30) $cellwidth="420";
else $cellwidth=(strlen($name)*14);
echo "<table class=\"list_table $TEXT_DIRECTION\"><tr><td width=\"".$cellwidth."px\" valign=\"top\">";
if ($view == "preview") echo "<h2>" . i18n::translate('%s Generation Circle Diagram', $PEDIGREE_GENERATIONS) . ":";
else echo "<h2>" . i18n::translate('Circle diagram') . ":";
echo "<br />".PrintReady($name);
if ($addname != "") echo "<br />" . PrintReady($addname);
echo "</h2>";

// -- print the form to change the number of displayed generations
if ($view != "preview") {
	echo WT_JS_START;
	echo "var pastefield; function paste_id(value) { pastefield.value=value; }";
	echo WT_JS_END;
	echo "</td><td><form name=\"people\" method=\"get\" action=\"?\">";
	echo "<table class=\"list_table $TEXT_DIRECTION\"><tr>";

	// NOTE: rootid
	echo "<td class=\"descriptionbox\">";
	echo i18n::translate('Root Person ID'), help_link('rootid');
	echo "</td><td class=\"optionbox\">";
	echo "<input class=\"pedigree_form\" type=\"text\" name=\"rootid\" id=\"rootid\" size=\"3\" value=\"$rootid\" />";
	print_findindi_link("rootid","");
	echo "</td>";

	// NOTE: fan style
	echo "<td rowspan=\"3\" class=\"descriptionbox\">";
	echo i18n::translate('Circle diagram'), help_link('fan_style');
	echo "</td><td rowspan=\"3\" class=\"optionbox\">";
	echo "<input type=\"radio\" name=\"fan_style\" value=\"2\"";
	if ($fan_style==2) echo " checked=\"checked\"";
	echo " /> 1/2";
	echo "<br /><input type=\"radio\" name=\"fan_style\" value=\"3\"";
	if ($fan_style==3) echo " checked=\"checked\"";
	echo " /> 3/4";
	echo "<br /><input type=\"radio\" name=\"fan_style\" value=\"4\"";
	if ($fan_style==4) echo " checked=\"checked\"";
	echo " /> 4/4";

	// NOTE: submit
	echo "</td><td rowspan=\"3\" class=\"topbottombar vmiddle\">";
	echo "<input type=\"submit\" value=\"" . i18n::translate('View') . "\" />";
	echo "</td></tr>";

	// NOTE: generations
	echo "<tr><td class=\"descriptionbox\">";
	echo i18n::translate('Generations'), help_link('PEDIGREE_GENERATIONS');
	echo "</td><td class=\"optionbox\">";
	echo "<select name=\"PEDIGREE_GENERATIONS\">";
	// Can only show 9 generations (256 ancestors) as graphics library has integer degree resolution
	for ($i=2; $i<=min(9,$MAX_PEDIGREE_GENERATIONS); $i++) {
	echo "<option value=\"".$i."\"" ;
	if ($i == $PEDIGREE_GENERATIONS) echo "selected=\"selected\" ";
		echo ">".$i."</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr><tr>";
	// NOTE: fan width
	echo "<td class=\"descriptionbox\">";
	echo i18n::translate('Width'), help_link('fan_width');
	echo "</td><td class=\"optionbox\">";
	echo "<input type=\"text\" size=\"3\" name=\"fan_width\" value=\"$fan_width\" /> <b>%</b> ";
	echo "</td>";
	echo "</tr></table>";
	echo "</form><br />";
} else {
	echo "<script language='JavaScript' type='text/javascript'>";
	echo "if (IE) document.write('<span class=\"warning\">".str_replace("'", "\'", i18n::translate('This Fanchart image cannot be printed directly by your browser. Use right-click then save and print.'))."</span>');";
	echo "</script>";
}
echo "</td></tr></table>";

$treeid = ancestry_array($rootid);
print_fan_chart($treeid, 640*$fan_width/100, $fan_style*90);

print_footer();
?>
