<?php
// Controller for the Hourglass Page
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_HOURGLASS_CTRL_PHP', '');

require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/classes/class_person.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts[] = "FAMS";
$nonfacts[] = "FAMC";
$nonfacts[] = "MAY";
$nonfacts[] = "BLOB";
$nonfacts[] = "CHIL";
$nonfacts[] = "HUSB";
$nonfacts[] = "WIFE";
$nonfacts[] = "RFN";
$nonfacts[] = "";
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";

class HourglassController extends BaseController {
	var $pid = "";

	var $accept_success = false;
	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $show_full = 0;
	var $show_spouse = 0;
	var $generations;
	var $dgenerations;
	var $box_width;
	var $name;
	//  the following are ajax variables  //
	var $ARID;
	var $arrwidth;
	var $arrheight;
	///////////////////////////////////////

	/**
	 * Initialization function
	 */
	function init($rootid='', $show_full=1, $generations=3) {
		global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $bheight, $bwidth, $bhalfheight, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;
		global $WT_IMAGES, $TEXT_DIRECTION, $show_full;

		// Extract parameters from from
		$this->pid        =safe_GET_xref('pid');
		$this->show_full  =safe_GET('show_full',   array('0', '1'), $PEDIGREE_FULL_DETAILS);
		$this->show_spouse=safe_GET('show_spouse', array('0', '1'), '0');
		$this->generations=safe_GET_integer('generations', 2, $MAX_DESCENDANCY_GENERATIONS, 3);
		$this->box_width  =safe_GET_integer('box_width',   50, 300, 100);

		// This is passed as a global.  A parameter would be better...
		$show_full=$this->show_full;

		if (!empty($_REQUEST["action"])) $this->action = $_REQUEST["action"];
		if (!empty($rootid)) $this->pid = $rootid;

		//-- flip the arrows for RTL languages
		if ($TEXT_DIRECTION=="rtl") {
			$temp = $WT_IMAGES['larrow'];
			$WT_IMAGES['larrow'] = $WT_IMAGES['rarrow'];
			$WT_IMAGES['rarrow'] = $temp;
		}
		//-- get the width and height of the arrow images for adjusting spacing
		if (file_exists($WT_IMAGES['larrow'])) {
			$temp = getimagesize($WT_IMAGES['larrow']);
			$this->arrwidth = $temp[0];
			$this->arrheight= $temp[1];
		}

		// -- Sets the sizes of the boxes
		if (!$this->show_full) $bwidth *= $this->box_width / 150;
		else $bwidth*=$this->box_width/100;

		if (!$this->show_full) $bheight = (int)($bheight / 2);
		$bhalfheight = (int)($bheight / 2);

		// Validate parameters
		$this->pid=check_rootid($this->pid);

		$this->hourPerson = Person::getInstance($this->pid);
		$this->name=$this->hourPerson->getFullName();

		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->max_descendency_generations($this->pid, 0);
		if ($this->dgenerations<1) $this->dgenerations=1;
	}

	/**
	 * Prints pedigree of the person passed in. Which is the descendancy
	 *
	 * @param mixed $pid ID of person to print the pedigree for
	 * @param mixed $count generation count, so it recursively calls itself
	 * @access public
	 * @return void
	 */
	function print_person_pedigree($pid, $count) {
		global $SHOW_EMPTY_BOXES, $WT_IMAGES, $bhalfheight;

		if ($count>=$this->generations) return;
		$person = Person::getInstance($pid);
		if (is_null($person)) return;
		$families = $person->getChildFamilies();
		//-- calculate how tall the lines should be
		$lh = ($bhalfheight+3) * pow(2, ($this->generations-$count-1));
		foreach ($families as $famid => $family) {
			echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">";
			$parents = find_parents($famid);
			$height="100%";
			echo "<tr>";
			echo "<td valign=\"bottom\"><img name=\"pvline\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$lh\" alt=\"\" /></td>";
			echo "<td><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>";
			echo "<td>";
			//-- print the father box
			print_pedigree_person($parents["HUSB"]);
			echo "</td>";
			$ARID = $parents["HUSB"];
			echo "<td id=\"td_".$ARID."\">";

			//-- print an Ajax arrow on the last generation of the adult male
			if ($count==$this->generations-1 && (count(find_family_ids($ARID))>0) && !is_null (find_family_ids($ARID))) {
				echo "<a href=\"#\" onclick=\"return ChangeDiv('td_".$ARID."','".$ARID."','".$this->show_full."','".$this->show_spouse."','".$this->box_width."')\"><img src=\"".$WT_IMAGES["rarrow"]."\" border=\"0\" alt=\"\" /></a> ";
			}
			//-- recursively get the father's family
			$this->print_person_pedigree($parents["HUSB"], $count+1);
			echo "</td>";
			echo "</tr><tr>";
			echo "<td valign=\"top\"><img name=\"pvline\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$lh\" alt=\"\" /></td>";
			echo "<td><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>";
			echo "<td>";
			//-- print the mother box
			print_pedigree_person($parents["WIFE"]);
			echo "</td>";
			$ARID = $parents["WIFE"];
			echo "<td id=\"td_".$ARID."\">";


			//-- print an ajax arrow on the last generation of the adult female
			if ($count==$this->generations-1 && (count(find_family_ids($ARID))>0) && !is_null (find_family_ids($ARID))) {
				echo "<a href=\"#\" onclick=\"ChangeDiv('td_".$ARID."','".$ARID."','".$this->show_full."','".$this->show_spouse."','".$this->box_width."'); return false;\"><img src=\"".$WT_IMAGES["rarrow"]."\" border=\"0\" alt=\"\" /></a> ";
			}

			//-- recursively print the mother's family
			$this->print_person_pedigree($parents["WIFE"], $count+1);
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			break;
		}
	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param mixed $pid ID of person to print descendency for
	 * @param mixed $count count of generations to print
	 * @access public
	 * @return void
	 */
	function print_descendency($pid, $count, $showNav=true) {
		global $TEXT_DIRECTION, $WT_IMAGES, $bheight, $bwidth, $bhalfheight, $lastGenSecondFam;

		if ($count>$this->dgenerations) return 0;
		$person = Person::getInstance($pid);
		if (is_null($person)) return;

		$tablealign = "right";
		$otablealign = "left";
		if ($TEXT_DIRECTION=="rtl") {
			$tablealign = "left";
			$otablealign = "right";
		}
		echo "<!-- print_descendency for $pid -->";
		//-- put a space between families on the last generation
		if ($count==$this->dgenerations-1) {
			if (isset($lastGenSecondFam)) echo "<br />";
			$lastGenSecondFam = true;
		}

		echo "<table id=\"table_$pid\" align=\"".$tablealign."\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
		echo "<tr>";
		echo "<td align=\"$tablealign\" width=\"100%\">";
		$numkids = 0;
		$families = $person->getSpouseFamilies();
		$famcount = count($families);
		$famNum = 0;
		$kidNum = 0;
		$children = array();
		if ($count < $this->dgenerations) {
			//-- put all of the children in a common array
			foreach ($families as $famid => $family) {
				$famNum ++;
				$chs = $family->getChildren();
				foreach ($chs as $c=>$child) $children[] = $child;
			}

			$ct = count($children);
			if ($ct>0) {
				echo "<table style=\"position: relative; top: auto; text-align: $tablealign;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
				for ($i=0; $i<$ct; $i++) {
					if (($i>0)&&($i<$ct-1)) $rowspan=1;
					/* @var $person2 Person */
					$person2 = $children[$i];
					$chil = $person2->getXref();
					echo "<tr>";
					echo "<td id=\"td_$chil\" class=\"$TEXT_DIRECTION\" align=\"$tablealign\">";
					$kids = $this->print_descendency($chil, $count+1);
					$numkids += $kids;
					echo "</td>";

					//-- print the lines
					$twidth = 7;
					if ($ct==1) $twidth+=3;
					if ($ct>1) {
						if ($i==0) {
							//-- adjust for the number of kids
							$h = ($bhalfheight+3)*$numkids;
							echo "<td valign=\"bottom\"><img name=\"tvertline\" id=\"vline_$chil\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$h\" alt=\"\" /></td>";
						} else if ($i==$ct-1) {
							$h = ($bhalfheight+3)*$kids;
							if ($count<$this->dgenerations-1) {
								if ($this->show_spouse) $h-=15;
								else $h += 15;
							}
							echo "<td valign=\"top\"><img name=\"bvertline\" id=\"vline_$chil\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"".$h."\" alt=\"\" /></td>";
						} else {
							echo "<td style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td>";
						}
					}
					echo "</tr>";

				}
				echo "</table>";

			}
			echo "</td>";
			echo "<td width=\"$bwidth\">";
		}

		// Print the descendency expansion arrow
		if ($count==$this->dgenerations) {
			$numkids = 1;
			$tbwidth = $bwidth+16;
			for ($j=$count; $j<$this->dgenerations; $j++) {
				echo "<div style=\"width: ".($tbwidth)."px;\"><br /></div></td><td width=\"$bwidth\">";
			}
			$kcount = 0;
			foreach ($families as $famid=>$family) $kcount+=$family->getNumberOfChildren();
			if ($kcount==0) {
				echo "<div style=\"width: ".($this->arrwidth)."px;\"><br /></div></td><td width=\"$bwidth\">";
			} else {
				echo "<div style=\"width: ".($this->arrwidth)."px;\"><a href=\"$pid\" onclick=\"return ChangeDis('td_".$pid."','".$pid."','".$this->show_full."','".$this->show_spouse."','".$this->box_width."')\"><img src=\"".$WT_IMAGES["larrow"]."\" border=\"0\" alt=\"\" /></a></div>";
				//-- move the arrow up to line up with the correct box
				if ($this->show_spouse) {
					foreach ($families as $famid => $family) {
						/* @var $family Family */
						if (!is_null($family)) {
							$spouse = $family->getSpouse($person);
							if ($spouse!=null) {
								echo "<br /><br /><br />";
							}
						}
					}
				}
				echo "</td><td width=\"$bwidth\">";
			}
		}

		echo "<table id=\"table2_$pid\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td>";
		print_pedigree_person($pid);
		echo "</td><td><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\" />";

		//----- Print the spouse
		if ($this->show_spouse) {
			foreach ($families as $famid => $family) {
				/* @var $family Family */
				if (!is_null($family)) {
					$spouse = $family->getSpouse($person);
					if ($spouse!=null) {
						echo "</td></tr><tr><td align=\"$otablealign\">";
						//-- shrink the box for the spouses
						$tempw = $bwidth;
						$temph = $bheight;
						$bwidth -= 10;
						$bheight -= 10;
						print_pedigree_person($spouse->getXref());
						$bwidth = $tempw;
						$bheight = $temph;
						$numkids += 0.95;
						echo "</td><td></td>";
					}
				}
			}
			//-- add offset divs to make things line up better
			if ($count==$this->dgenerations) echo "<tr><td colspan\"2\"><div style=\"height: ".($bhalfheight/2)."px; width: ".$bwidth."px;\"><br /></div>";
		}
		echo "</td></tr></table>";

		// For the root person, print a down arrow that allows changing the root of tree
		if ($showNav && $count==1) {
			// NOTE: If statement OK
			if ($person->canDisplayName()) {
				// -- print left arrow for decendants so that we can move down the tree
				$famids = $person->getSpouseFamilies();
				//-- make sure there is more than 1 child in the family with parents
				$cfamids = $person->getChildFamilies();
				$num=0;
				foreach ($cfamids as $famid=>$family) {
					if (!is_null($family)) {
						$num += $family->getNumberOfChildren();
					}
				}
				// NOTE: If statement OK
				if ($num>0) {
					echo "<div class=\"center\" id=\"childarrow\" dir=\"".$TEXT_DIRECTION."\"";
					echo " style=\"position:absolute; width:".$bwidth."px; \">";
					echo "<a href=\"javascript: ".i18n::translate('Show')."\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow',3);\" onmouseout=\"swap_image('larrow',3);\">";
					echo "<img id=\"larrow\" src=\"".$WT_IMAGES["darrow"]."\" border=\"0\" alt=\"\" />";
					echo "</a><br />";
					echo "<div id=\"childbox\" dir=\"".$TEXT_DIRECTION."\" style=\"width:".$bwidth."px; height:".$bheight."px; visibility: hidden;\">";
					echo "<table class=\"person_box\"><tr><td>";

					foreach ($famids as $famid=>$family) {
						if (!is_null($family)) {
							$spouse = $family->getSpouse($person);
							if (!empty($spouse)) {
								$spid = $spouse->getXref();
								echo "<a href=\"hourglass.php?pid={$spid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\"><span ";
								$name = $spouse->getFullName();
								$name = rtrim($name);
								if (hasRTLText($name))
								echo "class=\"name2\">";
								else echo "class=\"name1\">";
								echo PrintReady($name);
								echo "<br /></span></a>";

							}

							$children = $family->getChildren();
							foreach ($children as $id=>$child) {
								$cid = $child->getXref();
								echo "&nbsp;&nbsp;<a href=\"hourglass.php?pid={$cid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\"><span ";
								$name = $child->getFullName();
								$name = rtrim($name);
								if (hasRTLText($name))
								echo "class=\"name2\">&lt; ";
								else echo "class=\"name1\">&lt; ";
								echo PrintReady($name);

								echo "<br /></span></a>";

							}
						}
					}
					//-- do we need to print this arrow?
					echo "<img src=\"".$WT_IMAGES["rarrow"]."\" border=\"0\" alt=\"\" /> ";

					//-- print the siblings
					foreach ($cfamids as $famid=>$family) {
						if (!is_null($family)) {
							if (!is_null($family->getHusband()) || !is_null($family->getWife())) {
								echo "<span class=\"name1\"><br />".i18n::translate('Parents')."<br /></span>";
								$husb = $family->getHusband();
								if (!empty($husb)) {
									$spid = $husb->getXref();
									echo "&nbsp;&nbsp;<a href=\"hourglass.php?pid={$spid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\"><span ";
									$name = $husb->getFullName();
									$name = rtrim($name);
									if (hasRTLText($name))
									echo "class=\"name2\">";
									else echo "class=\"name1\">";
									echo PrintReady($name);
									echo "<br /></span></a>";
								}
								$husb = $family->getWife();
								if (!empty($husb)) {
									$spid = $husb->getXref();
									echo "&nbsp;&nbsp;<a href=\"hourglass.php?pid={$spid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\"><span ";
									$name = $husb->getFullName();
									$name = rtrim($name);
									if (hasRTLText($name))
									echo "class=\"name2\">";
									else echo "class=\"name1\">";
									echo PrintReady($name);
									echo "<br /></span></a>";
								}
							}
							$children = $family->getChildren();
							$num = $family->getNumberOfChildren();
							if ($num>2) echo "<span class=\"name1\"><br />".i18n::translate('Siblings')."<br /></span>";
							if ($num==2) echo "<span class=\"name1\"><br />".i18n::translate('Sibling')."<br /></span>";
							foreach ($children as $id=>$child) {
								$cid = $child->getXref();
								if ($cid!=$pid) {
									echo "&nbsp;&nbsp;<a href=\"hourglass.php?pid={$cid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\"><span ";
									$name = $child->getFullName();
									$name = rtrim($name);
									if (hasRTLText($name))
									echo "class=\"name2\"> ";
									else echo "class=\"name1\"> ";
									echo PrintReady($name);
									echo "<br /></span></a>";

								}
							}
						}
					}
					echo "</td></tr></table>";
					echo "</div>";
					echo "</div>";
				}
			}
		}
		echo "</td></tr>";
		echo "</table>";
		return $numkids;
	}

	/**
	 * Calculates number of generations a person has
	 *
	 * @param mixed $pid ID of person to see how far down the descendency goes
	 * @param mixed $depth Pass in 0 and it calculates how far down descendency goes
	 * @access public
	 * @return maxdc Amount of generations the descendency actually goes
	 */
	function max_descendency_generations($pid, $depth) {
		if ($depth > $this->generations) return $depth;
		$person = Person::getInstance($pid);
		if (is_null($person)) return $depth;
		$famids = $person->getSpouseFamilies();
		if ($person->getNumberOfChildren()==0) return $depth-1;
		$maxdc = $depth;
		foreach ($famids as $famid => $family) {
			$ct = preg_match_all("/1 CHIL @(.*)@/", $family->getGedcomRecord(), $match, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				$chil = trim($match[$i][1]);
				$dc = $this->max_descendency_generations($chil, $depth+1);
				if ($dc >= $this->generations) return $dc;
				if ($dc > $maxdc) $maxdc = $dc;
			}
		}

		$maxdc++;
		if ($maxdc==1) $maxdc++;
		return $maxdc;
	}

	/**
	 * setup all of the javascript that is needed for the hourglass chart
	 *
	 */
	function setupJavascript() {
		global $bhalfheight;
?>
<script language="JavaScript" type="text/javascript">
<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}

	// Hourglass control..... Ajax arrows at the end of chart
	function ChangeDiv(div_id, ARID, full, spouse, width) {
		var divelement = document.getElementById(div_id);
		var oXmlHttp = createXMLHttp();
		oXmlHttp.open("get", "hourglass_ajax.php?show_full="+full+"&pid="+ ARID + "&generations=1&box_width="+width+"&show_spouse="+spouse, true);
		oXmlHttp.onreadystatechange=function()
		{
			if (oXmlHttp.readyState==4)
			{
				divelement.innerHTML = oXmlHttp.responseText;
				sizeLines();
			}
		};
		oXmlHttp.send(null);
		return false;
	}

	// Hourglass control..... Ajax arrows at the end of descendants chart
	function ChangeDis(div_id, ARID, full, spouse, width) {
		var divelement = document.getElementById(div_id);
		var oXmlHttp = createXMLHttp();
		oXmlHttp.open("get", "hourglass_ajax.php?type=desc&show_full="+full+"&pid="+ ARID + "&generations=1&box_width="+width+"&show_spouse="+spouse, true);
		oXmlHttp.onreadystatechange=function()
		{
			if (oXmlHttp.readyState==4)
			{
				divelement.innerHTML = oXmlHttp.responseText;
				sizeLines();
			}
		};
		oXmlHttp.send(null);
		return false;
	}

	function sizeLines() {
		var vlines;
		vlines = document.getElementsByName("tvertline");
		for (i=0; i < vlines.length; i++) {
			var pid = vlines[i].id.substr(vlines[i].id.indexOf("_")+1);
			var hline = document.getElementById("table_"+pid);
			var hline2 = document.getElementById("table2_"+pid);
			var newHeight = Math.abs(hline.offsetHeight - (hline2.offsetTop + <?php echo $bhalfheight+2; ?>));
			vlines[i].style.height=newHeight+'px';
		}

		vlines = document.getElementsByName("bvertline");
		for (i=0; i < vlines.length; i++) {
			var pid = vlines[i].id.substr(vlines[i].id.indexOf("_")+1);
			var hline = document.getElementById("table_"+pid);
			var hline2 = document.getElementById("table2_"+pid);
			vlines[i].style.height=(hline.offsetTop+hline2.offsetTop + <?php echo $bhalfheight+2; ?>)+'px';
		}

		vlines = document.getElementsByName("pvline");
		//alert(vlines[0].parentNode.parentNode.parentNode);
		for (i=0; i < vlines.length; i++) {
			//vlines[i].parentNode.style.height="50%";
			vlines[i].style.height=(vlines[i].parentNode.offsetHeight/2)+'px';
		}
	}
//-->
</script>
<?php
}
}
