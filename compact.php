<?php
// Compact pedigree tree
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'compact.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

// Extract form variables
$rootid    =safe_GET_xref('rootid');
$showthumbs=safe_GET('showthumbs', '1', '0');

// Validate form variables
$rootid=check_rootid($rootid);

$person =WT_Person::getInstance($rootid);
$name   =$person->getFullName();
$addname=$person->getAddName();

// -- print html header information
print_header(PrintReady($name) . " " . WT_I18N::translate('Compact chart'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

// LBox =====================================================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/lb_defaultconfig.php';
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}
// ==========================================================================================

if (strlen($name)<30) $cellwidth="420";
else $cellwidth=(strlen($name)*14);
echo "<table class=\"list_table $TEXT_DIRECTION\"><tr><td width=\"{$cellwidth}px\" valign=\"top\">";
echo "<h2>" . WT_I18N::translate('Compact chart') . ":";
echo "<br />".PrintReady($name) ;
if ($addname != "") echo "<br />" . PrintReady($addname);
echo "</h2>";

// -- print the form
?>
<script type="text/javascript">
<!--
var pastefield;
function paste_id(value) {
	pastefield.value=value;
}
//-->
</script>
<?php
echo "</td><td><form name=\"people\" id=\"people\" method=\"get\" action=\"?\">";
echo "<table class=\"list_table $TEXT_DIRECTION\">";
echo "<tr>";

// NOTE: Root ID
echo "<td class=\"descriptionbox\">";
echo WT_I18N::translate('Root Person ID'), help_link('rootid'), "</td>";
echo "<td class=\"optionbox vmiddle\">";
echo "<input class=\"pedigree_form\" type=\"text\" name=\"rootid\" id=\"rootid\" size=\"3\" value=\"$rootid\" />";
print_findindi_link("rootid","");
echo "</td>";

// NOTE: submit
echo "<td class=\"facts_label03\" rowspan=\"3\">";
echo "<input type=\"submit\" value=\"".WT_I18N::translate('View')."\" />";
echo "</td></tr>";

if ($SHOW_HIGHLIGHT_IMAGES) {
	echo "<tr>";
	echo "<td class=\"descriptionbox\">";
	echo WT_I18N::translate('Show highlight images in people boxes'), help_link('SHOW_HIGHLIGHT_IMAGES');
	echo "</td>";
	echo "<td class=\"optionbox\">";
	echo "<input name=\"showthumbs\" type=\"checkbox\" value=\"1\"";
	if ($showthumbs) echo " checked=\"checked\"";
	echo " /></td></tr>";
}

echo "</table>";
echo "</form>";
echo "</td></tr></table>";

// process the tree
$treeid = ancestry_array($rootid, 5);
echo "<br />";
echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";

// 1
echo "<tr>";
print_td_person(16);
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
print_td_person(18);
echo "<td></td>";
print_td_person(24);
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
print_td_person(26);
echo "</tr>";

// 2
echo "<tr>";
echo "<td style='text-align:center;'>"; print_arrow_person(16, "up"); echo "</td>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(18, "up"); echo "</td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(24, "up"); echo "</td>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(26, "up"); echo "</td>";
echo "</tr>";

// 3
echo "<tr>";
print_td_person(8);
echo "<td style='text-align:center;'>"; print_arrow_person(8, "left"); echo "</td>";
print_td_person(4);
echo "<td style='text-align:center;'>"; print_arrow_person(9, "right"); echo "</td>";
print_td_person(9);
echo "<td></td>";
print_td_person(12);
echo "<td style='text-align:center;'>"; print_arrow_person(12, "left"); echo "</td>";
print_td_person(6);
echo "<td style='text-align:center;'>"; print_arrow_person(13, "right"); echo "</td>";
print_td_person(13);
echo "</tr>";

// 4
echo "<tr>";
echo "<td style='text-align:center;'>"; print_arrow_person(17, "down"); echo "</td>";
echo "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(4, "up"); echo "</td>";
echo "<td style='text-align:center;'>"; print_arrow_person(19, "down"); echo "</td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(25, "down"); echo "</td>";
echo "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(6, "up"); echo "</td>";
echo "<td style='text-align:center;'>"; print_arrow_person(27, "down"); echo "</td>";
echo "</tr>";

// 5
echo "<tr>";
print_td_person(17);
print_td_person(19);
echo "<td></td>";
print_td_person(25);
print_td_person(27);
echo "</tr>";

// 6
echo "<tr>";
echo "<td></td>";
echo "<td></td>";
echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
echo "<td></td>";
echo "<td></td>";
echo "</tr>";

// 7
echo "<tr>";
echo "<td></td>";
echo "<td></td>";
print_td_person(2);
echo "<td>";
echo "</td>";

echo "<td colspan='3'>";
echo "<table width='100%'><tr>";
echo "<td style='text-align:center;' width='25%'>"; print_arrow_person(2, "left"); echo "</td>";
print_td_person(1);
echo "<td style='text-align:center;' width='25%'>"; print_arrow_person(3, "right"); echo "</td>";
echo "</tr></table>";
echo "</td>";

echo "<td>";
echo "</td>";
print_td_person(3);
echo "<td></td>";
echo "<td></td>";
echo "</tr>";

// 8
echo "<tr>";
echo "<td>&nbsp;</td>";
echo "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(5, "down"); echo "</td>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(7, "down"); echo "</td>";
echo "<td></td>";
echo "</tr>";

// 9
echo "<tr>";
print_td_person(20);
print_td_person(22);
echo "<td></td>";
print_td_person(28);
print_td_person(30);
echo "</tr>";

// 10
echo "<tr>";
echo "<td style='text-align:center;'>"; print_arrow_person(20, "up"); echo "</td>";
echo "<td style='text-align:center;'>"; print_arrow_person(22, "up"); echo "</td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(28, "up"); echo "</td>";
echo "<td style='text-align:center;'>"; print_arrow_person(30, "up"); echo "</td>";
echo "</tr>";

// 11
echo "<tr>";
print_td_person(10);
echo "<td style='text-align:center;'>"; print_arrow_person(10, "left"); echo "</td>";
print_td_person(5);
echo "<td style='text-align:center;'>"; print_arrow_person(11, "right"); echo "</td>";
print_td_person(11);
echo "<td></td>";
print_td_person(14);
echo "<td style='text-align:center;'>"; print_arrow_person(14, "left"); echo "</td>";
print_td_person(7);
echo "<td style='text-align:center;'>"; print_arrow_person(15, "right"); echo "</td>";
print_td_person(15);
echo "</tr>";

// 12
echo "<tr>";
echo "<td style='text-align:center;'>"; print_arrow_person(21, "down"); echo "</td>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(23, "down"); echo "</td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(29, "down"); echo "</td>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td style='text-align:center;'>"; print_arrow_person(31, "down"); echo "</td>";
echo "</tr>";

// 13
echo "<tr>";
print_td_person(21);
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
print_td_person(23);
echo "<td></td>";
print_td_person(29);
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
print_td_person(31);
echo "</tr>";

echo "</table>";
echo "<br />";

print_footer();

function print_td_person($n) {
	global $treeid, $WT_IMAGES;
	global $TEXT_DIRECTION, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $USE_SILHOUETTE, $WT_IMAGES;
	global $showthumbs;

	$text = "";
	$pid = $treeid[$n];

	if ($TEXT_DIRECTION=="ltr") {
		$title = WT_I18N::translate('Individual information').": ".$pid;
	} else {
		$title = $pid." :".WT_I18N::translate('Individual information');
	}

	if ($pid) {
		$indi=WT_Person::getInstance($pid);
		$name=$indi->getFullName();
		$addname=$indi->getAddName();

		if ($showthumbs && $MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES) {
			$object = find_highlighted_object($pid, WT_GED_ID, $indi->getGedcomRecord());
			if (!empty($object)) {
				$whichFile = thumb_or_main($object); // Do we send the main image or a thumbnail?
				$size = findImageSize($whichFile);
				$class = "pedigree_image_portrait";
				if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
				if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
				// NOTE: IMG ID
				$imgsize = findImageSize($object["file"]);
				$imgwidth = $imgsize[0]+50;
				$imgheight = $imgsize[1]+150;
				if (WT_USE_LIGHTBOX) {
					$text .= "<a href=\"" . $object["file"] . "\" rel=\"clearbox[general]\" rev=\"" . $object['mid'] . "::" . WT_GEDCOM . "::" . PrintReady(htmlspecialchars($name)) . "\">";
				} else {
					$text .= "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($object["file"])."',$imgwidth, $imgheight);\">";
				}
				$birth_date=$indi->getBirthDate();
				$death_date=$indi->getDeathDate();
				$text .= "<img id=\"box-$pid\" src=\"".$whichFile."\"vspace=\"0\" hspace=\"0\" class=\"$class\" alt =\"\" title=\"".PrintReady(htmlspecialchars(strip_tags($name)))." - ".strip_tags(html_entity_decode($birth_date->Display(false)." - ".$death_date->Display(false)))."\"";
				if ($imgsize) $text .= " /></a>";
				else $text .= " />";
			} else if ($USE_SILHOUETTE && isset($WT_IMAGES["default_image_U"])) {
				$class = "pedigree_image_portrait";
				if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
				$sex = $indi->getSex();
				$text = "<img src=\"";
				if ($sex == 'F') {
					$text .= $WT_IMAGES["default_image_F"];
				} else if ($sex == 'M') {
					$text .= $WT_IMAGES["default_image_M"];
				} else {
					$text .= $WT_IMAGES["default_image_U"];
				}
				$text .="\" class=\"".$class."\" border=\"none\" alt=\"\" />";
			}
		}

		$text .= "<a class=\"name1\" href=\"".$indi->getHtmlUrl()."\" title=\"$title\"> ";
		$text .= PrintReady(htmlspecialchars(strip_tags($name)));
		if ($addname) $text .= "<br />" . PrintReady($addname);
		$text .= "</a>";
		$text .= "<br />";
		if ($indi->canDisplayDetails()) {
			$text.="<span class='details1'>";
			$text.=$indi->getBirthYear().'-'.$indi->getDeathYear();
			$age=WT_Date::GetAgeYears($indi->getBirthDate(), $indi->getDeathDate());
			if ($age) {
				$text.=" <span class=\"age\">".PrintReady("({$age})")."</span>";
			}
			$text.="</span>";
		}
	}

	//Removed by BH causing problems with nicknames not printing
	//$text = unhtmlentities($text);

	// -- empty box
	if (empty($text)) {
		$text = "&nbsp;<br />&nbsp;<br />";
	}
	// -- box color
	$isF="";
	if ($n==1) {
		if ($indi->getSex()=='F') {
			$isF="F";
		}
	} elseif ($n%2) {
		$isF="F";
	}
	// -- box size
	if ($n==1) {
		echo "<td";
	} else {
		echo "<td width='15%'";
	}
	// -- print box content
	echo " class=\"person_box", $isF, "\" style=\"text-align:center; vertical-align:top;\" >";
	echo $text;
	echo "</td>";
}

function print_arrow_person($n, $arrow_dir) {
	global $treeid, $showthumbs, $TEXT_DIRECTION, $WT_IMAGES;

	$pid = $treeid[$n];

	$arrow_swap = array("l"=>"0", "r"=>"1", "u"=>"2", "d"=>"3");

	$arrow_dir = substr($arrow_dir,0,1);
	if ($TEXT_DIRECTION=="rtl") {
		if ($arrow_dir=="l") {
			$arrow_dir="r";
		} elseif ($arrow_dir=="r") {
			$arrow_dir="l";
		}
	}

	$text = "";
	if ($pid) {
		$indi=WT_Person::getInstance($pid);
		$name=$indi->getFullName();
		if ($TEXT_DIRECTION=="ltr") {
			$title = WT_I18N::translate('Compact chart').": ".$name;
		} else {
			$title = $name." :".WT_I18N::translate('Compact chart');
		}
		$arrow_img = "<img id='arrow$n' src='".$WT_IMAGES[$arrow_dir."arrow"]."' border='0' align='middle' alt='$title' title='$title' />";
		$text .= "<a href=\"?rootid=".$pid;
		if ($showthumbs) $text .= "&amp;showthumbs=".$showthumbs;
		$text .= "\" onmouseover=\"swap_image('arrow$n',".$arrow_swap[$arrow_dir].");\" onmouseout=\"swap_image('arrow$n',".$arrow_swap[$arrow_dir].");\" >";
		$text .= $arrow_img."</a>";
	}
	// -- arrow to empty box does not have a url attached.
	else $text = "<img id='arrow$n' src='".$WT_IMAGES[$arrow_dir."arrow"]."' border='0' align='middle' alt='".WT_I18N::translate('Compact chart')."' title='".WT_I18N::translate('Compact chart')."' style='visibility:hidden;' />";
	echo $text;
}
