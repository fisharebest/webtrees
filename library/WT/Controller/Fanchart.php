<?php
//	Controller for the fan chart
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

require_once WT_ROOT.'includes/functions/functions_charts.php';

class WT_Controller_Fanchart extends WT_Controller_Chart {
	// Variables for the view
	public $fan_style      =null;
	public $fan_width      =null;
	public $generations    =null;
	public $max_generations=null;
	public $chart_html     =null;

	public function __construct() {
		parent::__construct();
		
		$default_generations=get_gedcom_setting(WT_GED_ID, 'DEFAULT_PEDIGREE_GENERATIONS');

		// Extract the request parameters
		$this->fan_style  =safe_GET_integer('fan_style',   2,  4,  3);
		$this->fan_width  =safe_GET_integer('fan_width',   50, 300, 100);
		$this->generations=safe_GET_integer('generations', 2, 9, $default_generations);

		if ($this->root && $this->root->canDisplayName()) {
			$this->setPageTitle(
				/* I18N: http://en.wikipedia.org/wiki/Family_tree#Fan_chart - %s is a person's name */
				WT_I18N::translate('Fan chart of %s', $this->root->getFullName())
			);
			// Generate the chart.  We need to store the image in the session, so
			// need to do this before we display the page header.
			$this->chart_html=$this->generate_fan_chart();
		} else {
			$this->setPageTitle(WT_I18N::translate('Fan chart'));
		}
	}

	public function getFanStyles() {
		return array(
			2=>/* I18N: layout option for the fan chart */ WT_I18N::translate('half circle'),
			3=>/* I18N: layout option for the fan chart */ WT_I18N::translate('three-quarter circle'),
			4=>/* I18N: layout option for the fan chart */ WT_I18N::translate('full circle'),
		);
	}

	/**
	 * split and center text by lines
	 *
	 * @param string $data input string
	 * @param int $maxlen max length of each line
	 * @return string $text output string
	 */
	public function split_align_text($data, $maxlen) {
		global $RTLOrd;

		$lines = explode("\n", $data);
		// more than 1 line : recursive calls
		if (count($lines)>1) {
			$text = "";
			foreach ($lines as $indexval => $line) $text .= $this->split_align_text($line, $maxlen)."\n";
			return $text;
		}
		// process current line word by word
		$split = explode(" ", $data);
		$text = "";
		$line = "";
		// do not split hebrew line

		$found = false;
		foreach ($RTLOrd as $indexval => $ord) {
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
	 */
	public function generate_fan_chart() {
		global $GEDCOM, $fanChart;

		$treeid=ancestry_array($this->root->getXref(), $this->generations);
		$fanw  =640*$this->fan_width/100;
		$fandeg=90*$this->fan_style;
		$html  ='';

		// check for GD 2.x library
		if (!defined("IMG_ARC_PIE")) {
			return false;
		}
		if (!function_exists("ImageTtfBbox")) {
			return false;
		}

		// Validate
		if (!file_exists($fanChart['font'])) {
			$html.= '<p class="ui-state-error">'.WT_I18N::translate('The file “%s” does not exist.', $fanChart['font']).'</p>';
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
				if ($pid) {
					$person =WT_Person::getInstance($pid);
					$name   =$person->getFullName();
					$addname=$person->getAddName();

					switch($person->getSex()) {
					case 'M':
						$bg=$bgcolorM;
						break;
					case 'F':
						$bg=$bgcolorF;
						break;
					case 'U':
						$bg=$bgcolor;
						break;
					}

					ImageFilledArc($image, $cx, $cy, $rx, $rx, $deg1, $deg2, $bg, IMG_ARC_PIE);

	//$name = str_replace(array('<span class="starredname">', '</span>'), '', $name);
	//$addname = str_replace(array('<span class="starredname">', '</span>'), '', $addname);
	//$name = str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $name); //@@
	//$addname = str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $addname); //@@
	// ToDo - print starred names underlined - 1985154
	// Todo - print Arabic letters combined - 1360209

					$text = reverseText($name) . "\n";
					if (!empty($addname)) $text .= reverseText($addname). "\n";

					if ($person->canDisplayDetails()) {
						$birthrec = get_sub_record(1, "1 BIRT", $person->getGedcomRecord());
						$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $birthrec, $match);
						if ($ct>0) $text.= trim($match[1]);
						$deathrec = get_sub_record(1, "1 DEAT", $person->getGedcomRecord());
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
					$text = $this->split_align_text($text, $wmax);

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

					$imagemap .= '<area shape="poly" coords="';
					// plot upper points
					$mr=$rx/2;
					$deg=$deg1;
					while ($deg<=$deg2) {
						$rad=deg2rad($deg);
						$tx=round($cx + ($mr) * cos($rad));
						$ty=round($cy - $mr * -sin($rad));
						$imagemap .= "$tx,$ty,";
						$deg+=($deg2-$deg1)/6;
					}
					// plot lower points
					$mr=($rx-$rw)/2;
					$deg=$deg2;
					while ($deg>=$deg1) {
						$rad=deg2rad($deg);
						$tx=round($cx + ($mr) * cos($rad));
						$ty=round($cy - $mr * -sin($rad));
						$imagemap .= "$tx,$ty,";
						$deg-=($deg2-$deg1)/6;
					}
					// join first point
					$mr=$rx/2;
					$deg=$deg1;
					$rad=deg2rad($deg);
					$tx=round($cx + ($mr) * cos($rad));
					$ty=round($cy - $mr * -sin($rad));
					$imagemap .= "$tx,$ty";
					// add action url
					$imagemap .= '" href="'.$person->getHtmlUrl().'"';
					$tempURL = 'fanchart.php?rootid='.$pid.'&amp;generations='.$this->generations.'&amp;fan_width='.$this->fan_width.'&amp;fan_style='.$this->fan_style.'&amp;ged='.WT_GEDURL;
					$count=0;
					$lbwidth=200;
					$html.= "<div id=\"I".$pid.".".$count."links\" style=\"position:absolute; >";
					$html.= "left:".$tx."px; top:".$ty."px; width: ".($lbwidth)."px; visibility:hidden; z-index:'100';\">";
					$html.= "<table class=\"person_box\"><tr><td class=\"details1\">";
					$html.= "<a href=\"".$person->getHtmlUrl()."\" class=\"name1\">" . $name;
					if (!empty($addname)) $html.= "<br>" . $addname;
					$html.= "</a>";
					$html.= "<br><a href=\"pedigree.php?rootid=$pid&amp;amp;ged=".WT_GEDURL."\" >".WT_I18N::translate('Pedigree')."</a>";
					if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
						$html.= "<br><a href=\"module.php?mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=".$pid."&amp;ged=".WT_GEDURL."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Pedigree map')."</a>";
					}
					if (WT_USER_GEDCOM_ID && WT_USER_GEDCOM_ID!=$pid) {
						$html.= "<br><a href=\"relationship.php?pid1=".WT_USER_GEDCOM_ID."&amp;pid2={$pid}&amp;ged=".WT_GEDURL."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Relationship to me')."</a>";
					}
					$html.= "<br><a href=\"descendancy.php?rootid=$pid&amp;ged=".WT_GEDURL."\" >".WT_I18N::translate('Descendants')."</a>";
					$html.= "<br><a href=\"ancestry.php?rootid=$pid&amp;ged=".WT_GEDURL."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Ancestors')."</a>";
					$html.= "<br><a href=\"compact.php?rootid=$pid&amp;ged=".WT_GEDURL."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Compact tree')."</a>";
					$html.= "<br><a href=\"".$tempURL."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Fan chart')."</a>";
					$html.= "<br><a href=\"hourglass.php?rootid=$pid&amp;ged=".WT_GEDURL."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Hourglass chart')."</a>";
					if (array_key_exists('tree', WT_Module::getActiveModules())) {
						$html.= '<br><a href="module.php?mod=tree&amp;mod_action=treeview&amp;ged='.WT_GEDURL.'&amp;rootid='.$pid."\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">".WT_I18N::translate('Interactive tree')."</a>";
					}
					// spouse(s) and children
					foreach ($person->getSpouseFamilies() as $family) {
						$spouse=$family->getSpouse($person);
						if ($spouse) {
							$html.= '<br><a href="'.$spouse->getHtmlUrl().'" class="name1">'.$spouse->getFullName().'</a>';
						}
						foreach ($family->getChildren() as $child) {
							$html.= '<br>&nbsp;&nbsp;<a href="'.$child->getHtmlUrl().'" class="name1">&lt; '.$child->getFullName().'</a>';
						}
					}
					// siblings
					foreach ($person->getChildFamilies() as $family) {
						$children=$family->getChildren();
						if (count($children)>2) {
							$html.= '<br><span class="name1">'.WT_I18N::translate('Siblings').'</span>';
						} elseif (count($children)==2) {
							$html.= '<br><span class="name1">'.WT_I18N::translate('Sibling').'</span>';
						}
						foreach ($children as $sibling) {
							if (!$sibling->equals($person)) {
								$html.= '<br>&nbsp;&nbsp;<a href="'.$sibling->getHtmlUrl().'" class="name1"> '.$sibling->getFullName().'</a>';
							}
						}
					}
					$html.= '</td></tr></table>';
					$html.= '</div>';
					$imagemap .= " onclick=\"show_family_box('".$pid.".".$count."', 'relatives'); return false;\"";
					$imagemap .= " onmouseout=\"family_box_timeout('".$pid.".".$count."'); return false;\"";
					$imagemap .= " alt=\"".htmlspecialchars(strip_tags($name))."\" title=\"".htmlspecialchars(strip_tags($name))."\">";
				}
				$deg1-=$angle;
				$deg2-=$angle;
				$sosa--;
			}
			$rx-=$rw;
			$gen--;
		}

		$imagemap .= '</map>';
		$html.= $imagemap;

		ImageStringUp($image, 1, $fanw-10, $fanh/3, WT_SERVER_NAME.WT_SCRIPT_PATH, $color);

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
		unset($_SESSION[$image_name]); // statisticsplot.php uses this to hold a filename to send to browser
		$image_title=WT_I18N::translate('Fan chart of %s', strip_tags($name));
		$html.= "<p align=\"center\" >";
		$html.= "<img src=\"imageflush.php?image_type=png&amp;image_name=$image_name&amp;height=$fanh&amp;width=$fanw\" width=\"$fanw\" height=\"$fanh\" alt=\"$image_title\" title=\"$image_title\" usemap=\"#fanmap\">";
		$html.= "</p>";
		ImageDestroy($image);
		return $html;
	}
}
