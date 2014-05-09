<?php
// Controller for the hourglass chart
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_Hourglass extends WT_Controller_Chart {
	var $pid = "";

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
	// Left and right get reversed on RTL pages
	private $left_arrow;
	private $right_arrow;
	//  the following are ajax variables  //
	var $ARID;

	function __construct($rootid='', $show_full=1, $generations=3) {
		global $bheight, $bwidth, $cbwidth, $cbheight, $bhalfheight, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;
		global $TEXT_DIRECTION, $show_full;

		parent::__construct();

		// Extract parameters from from
		$this->pid         = WT_Filter::get('rootid', WT_REGEX_XREF);
		$this->show_full   = WT_Filter::getInteger('show_full',   0, 1, $PEDIGREE_FULL_DETAILS);
		$this->show_spouse = WT_Filter::getInteger('show_spouse', 0, 1, 0);
		$this->generations = WT_Filter::getInteger('generations', 2, $MAX_DESCENDANCY_GENERATIONS, 3);
		$this->box_width   = WT_Filter::getInteger('box_width',   50, 300, 100);

		// This is passed as a global.  A parameter would be better...
		$show_full=$this->show_full;

		if (!empty($rootid)) $this->pid = $rootid;

		//-- flip the arrows for RTL languages
		if ($TEXT_DIRECTION=='ltr') {
			$this->left_arrow='icon-larrow';
			$this->right_arrow='icon-rarrow';
		} else {
			$this->left_arrow='icon-larrow';
			$this->right_arrow='icon-larrow';
		}

		// -- size of the detailed boxes based upon optional width parameter
		$Dbwidth =$this->box_width * $bwidth  / 100;
		$Dbheight=$this->box_width * $bheight / 100;
		$bwidth  =$Dbwidth;
		$bheight =$Dbheight;

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $this->box_width * $cbwidth  / 100;
			$bheight = $cbheight;
		}

		$bhalfheight = (int)($bheight / 2);

		// Validate parameters
		$this->hourPerson = WT_Individual::getInstance($this->pid);
		if (!$this->hourPerson) {
			$this->hourPerson=$this->getSignificantIndividual();
			$this->pid=$this->hourPerson->getXref();
		}

		$this->name=$this->hourPerson->getFullName();

		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->max_descendency_generations($this->pid, 0);
		if ($this->dgenerations<1) $this->dgenerations=1;

		$this->setPageTitle(/* I18N: %s is an individual’s name */ WT_I18N::translate('Hourglass chart of %s', $this->name));
	}

	/**
	 * Prints pedigree of the person passed in. Which is the descendancy
	 *
	 * @param string $person ID of person to print the pedigree for
	 * @param int    $count  generation count, so it recursively calls itself
	 */
	function print_person_pedigree($person, $count) {
		global $WT_IMAGES, $bheight, $bwidth, $bhalfheight;

		if ($count>=$this->generations) return;
		//if (!$person) return;
		$genoffset = $this->generations;  // handle pedigree n generations lines
		//-- calculate how tall the lines should be
		$lh = ($bhalfheight+4) * pow(2, ($genoffset-$count-1));
		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		//
		if (count($person->getChildFamilies())==0) {
			echo '<table>',
				 '<tr>',
				 '<td>',
				 '<div style="width:',$bwidth,'px; height:',$bheight,'px;"></div>';
			echo '</td>';
			echo '<td>';

				//-- recursively get the father’s family
				$this->print_person_pedigree($person, $count+1);
				echo '</td>';
				echo '<td>';
			echo '</tr><tr>',
				 '<td>',
				 '<div style="width:',$bwidth,'px; height:',$bheight,'px;"></div>';
				 echo '</td>';
			echo '<td>';
				//-- recursively get the father’s family
				$this->print_person_pedigree($person, $count+1);
				echo '</td>';
				echo '<td>';
			echo '</tr></table>';
		}
		foreach ($person->getChildFamilies() as $family) {
			echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">";
			$height="100%";
			echo "<tr>";
			echo "<td valign=\"bottom\"><img class=\"line3\" name=\"pvline\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$lh\" alt=\"\"></td>";
			echo "<td><img class=\"line4\" src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\"></td>";
			echo "<td>";
			//-- print the father box
			print_pedigree_person($family->getHusband());
			echo "</td>";
			if ($family->getHusband()) {
				$ARID = $family->getHusband()->getXref();
				echo "<td id=\"td_".$ARID."\">";

				//-- print an Ajax arrow on the last generation of the adult male
				if ($count==$this->generations-1 && $family->getHusband()->getChildFamilies()) {
					echo "<a href=\"#\" onclick=\"return changeDiv('td_".$ARID."','".$ARID."','".$this->show_full."','".$this->show_spouse."','".$this->box_width."')\" class=\"".$this->right_arrow."\"></a> ";
				}
				//-- recursively get the father’s family
				$this->print_person_pedigree($family->getHusband(), $count+1);
				echo "</td>";
			}
			echo "</tr><tr>";
			echo "<td valign=\"top\"><img name=\"pvline\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$lh\" alt=\"\"></td>";
			echo "<td><img class=\"line4\" src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\"></td>";
			echo "<td>";
			//-- print the mother box
			print_pedigree_person($family->getWife());
			echo "</td>";
			if ($family->getWife()) {
				$ARID = $family->getWife()->getXref();
				echo "<td id=\"td_".$ARID."\">";

				//-- print an ajax arrow on the last generation of the adult female
				if ($count==$this->generations-1 && $family->getWife()->getChildFamilies()) {
					echo "<a href=\"#\" onclick=\"changeDiv('td_".$ARID."','".$ARID."','".$this->show_full."','".$this->show_spouse."','".$this->box_width."'); return false;\" class=\"".$this->right_arrow."\"></a> ";
				}

				//-- recursively print the mother’s family
				$this->print_person_pedigree($family->getWife(), $count+1);
				echo "</td>";
			}
			echo "</tr>";
			echo "</table>";
			break;
		}
	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param       $person person to print descendency for
	 * @param mixed $count  count of generations to print
	 * @param bool  $showNav
	 *
	 * @return int
	 */
	function print_descendency($person, $count, $showNav=true) {
		global $TEXT_DIRECTION, $WT_IMAGES, $bheight, $bwidth, $bhalfheight, $lastGenSecondFam;

		if ($count>$this->dgenerations) return;
		if (!$person) return;
		$pid=$person->getXref();
		$tablealign = "right";
		$otablealign = "left";
		if ($TEXT_DIRECTION=="rtl") {
			$tablealign = "left";
			$otablealign = "right";
		}
		
		//-- put a space between families on the last generation
		if ($count==$this->dgenerations-1) {
			if (isset($lastGenSecondFam)) echo "<br>";
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
			foreach ($families as $family) {
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
					echo "<td id=\"td_$chil\" class=\"$TEXT_DIRECTION\" align=\"$otablealign\">";
					$kids = $this->print_descendency($person2, $count+1);
					$numkids += $kids;
					echo "</td>";

					//-- print the lines
					$twidth = 7;
					if ($ct==1) $twidth+=3;

					if ($ct>1) {
						if ($i==0) {
							//-- adjust for the number of kids
							$h = ($bhalfheight+3)*$numkids;
							echo "<td valign=\"bottom\"><img class=\"line1\" name=\"tvertline\" id=\"vline_$chil\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\"  alt=\"\"></td>";
						} else if ($i==$ct-1) {
							$h = ($bhalfheight+3)*$kids;
							if ($count<$this->dgenerations-1) {
								if ($this->show_spouse) $h-=15;
								else $h += 15;
							}
							echo "<td valign=\"top\"><img name=\"bvertline\" id=\"vline_$chil\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" alt=\"\"></td>";
						} else {
							echo "<td style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
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
				echo "<div style=\"width: ".($tbwidth)."px;\"><br></div></td><td width=\"$bwidth\">";
			}
			$kcount = 0;
			foreach ($families as $family) {
				$kcount+=$family->getNumberOfChildren();
			}
			if ($kcount==0) {
				echo "&nbsp;</td><td width=\"$bwidth\">";
			} else {
				echo "<a href=\"$pid\" onclick=\"return changeDis('td_".$pid."','".$pid."','".$this->show_full."','".$this->show_spouse."','".$this->box_width."')\" class=\"".$this->left_arrow."\"></a>";
				//-- move the arrow up to line up with the correct box
				if ($this->show_spouse) {
					foreach ($families as $family) {
						echo '<br><br><br>';
					}
				}
				echo "</td><td width=\"$bwidth\">";
			}
		}

		echo "<table id=\"table2_$pid\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td>";
		print_pedigree_person($person);
		echo "</td><td><img class=\"line2\" src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\">";

		//----- Print the spouse
		if ($this->show_spouse) {
			foreach ($families as $family) {
				echo "</td></tr><tr><td align=\"$otablealign\">";
				//-- shrink the box for the spouses
				$tempw = $bwidth;
				$temph = $bheight;
				$bwidth -= 10;
				$bheight -= 10;
				print_pedigree_person($family->getSpouse($person));
				$bwidth = $tempw;
				$bheight = $temph;
				$numkids += 0.95;
				echo "</td><td></td>";
			}
			//-- add offset divs to make things line up better
			if ($count==$this->dgenerations) echo "<tr><td colspan\"2\"><div style=\"height: ".($bhalfheight/2)."px; width: ".$bwidth."px;\"><br></div>";
		}
		echo "</td></tr></table>";

		// For the root person, print a down arrow that allows changing the root of tree
		if ($showNav && $count==1) {
			// NOTE: If statement OK
			if ($person->canShowName()) {
				// -- print left arrow for decendants so that we can move down the tree
				$famids = $person->getSpouseFamilies();
				//-- make sure there is more than 1 child in the family with parents
				$cfamids = $person->getChildFamilies();
				$num=0;
				foreach ($cfamids as $family) {
					$num += $family->getNumberOfChildren();
				}
				// NOTE: If statement OK
				if ($num>0) {
					echo '<div class="center" id="childarrow" style="position:absolute; width:', $bwidth, 'px;">';
					echo '<a href="#" onclick="togglechildrenbox(); return false;" class="icon-darrow"></a><br>';
					echo '<div id="childbox" style="width:', $bwidth, 'px; height:', $bheight, 'px; visibility: hidden;">';
					echo '<table class="person_box"><tr><td>';

					foreach ($famids as $family) {
						$spouse = $family->getSpouse($person);
						if ($spouse) {
							$spid = $spouse->getXref();
							echo "<a href=\"hourglass.php?rootid={$spid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\" class=\"name1\">";
							echo $spouse->getFullName();
							echo '</a><br>';
						}

						foreach ($family->getChildren() as $child) {
							$cid = $child->getXref();
							echo "&nbsp;&nbsp;<a href=\"hourglass.php?rootid={$cid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\" class=\"name1\">";
							echo $child->getFullName();
							echo '</a><br>';
						}
					}

					//-- print the siblings
					foreach ($cfamids as $family) {
						if ($family->getHusband() || $family->getWife()) {
							echo "<span class=\"name1\"><br>".WT_I18N::translate('Parents')."<br></span>";
							$husb = $family->getHusband();
							if ($husb) {
								$spid = $husb->getXref();
								echo "&nbsp;&nbsp;<a href=\"hourglass.php?rootid={$spid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\" class=\"name1\">";
								echo $husb->getFullName();
								echo '</a><br>';
							}
							$wife = $family->getWife();
							if ($wife) {
								$spid = $wife->getXref();
								echo "&nbsp;&nbsp;<a href=\"hourglass.php?rootid={$spid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\" class=\"name1\">";
								echo $wife->getFullName();
								echo '</a><br>';
							}
						}
						$num = $family->getNumberOfChildren();
						if ($num>2) echo "<span class=\"name1\"><br>".WT_I18N::translate('Siblings')."<br></span>";
						if ($num==2) echo "<span class=\"name1\"><br>".WT_I18N::translate('Sibling')."<br></span>";
						foreach ($family->getChildren() as $child) {
							$cid = $child->getXref();
							if ($cid!=$pid) {
								echo "&nbsp;&nbsp;<a href=\"hourglass.php?rootid={$cid}&amp;show_spouse={$this->show_spouse}&amp;show_full={$this->show_full}&amp;generations={$this->generations}&amp;box_width={$this->box_width}\" class=\"name1\">";
								echo $child->getFullName();
								echo '</a><br>';
							}
						}
					}
					echo '</td></tr></table>';
					echo '</div>';
					echo '</div>';
				}
			}
		}
		echo '</td></tr>';
		echo '</table>';
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
		$person = WT_Individual::getInstance($pid);
		if (is_null($person)) return $depth;
		$maxdc = $depth;
		foreach ($person->getSpouseFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$dc = $this->max_descendency_generations($child->getXref(), $depth+1);
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

<script>
		// code to fix chart lines in block
		var vlines;
		vlines = document.getElementsByName("tvertline");
		for (i=0; i < vlines.length; i++) {
			var pid = vlines[i].id.substr(vlines[i].id.indexOf("_")+1);
			var hline = document.getElementById("table_"+pid);
			var hline2 = document.getElementById("table2_"+pid);
			var newHeight = Math.abs(hline.offsetHeight - (hline2.offsetTop + <?php echo $bhalfheight+9; ?>));
			vlines[i].style.height=newHeight+'px';
		}

		vlines = document.getElementsByName("bvertline");
		for (i=0; i < vlines.length; i++) {
			var pid = vlines[i].id.substr(vlines[i].id.indexOf("_")+1);
			var hline = document.getElementById("table_"+pid);
			var hline2 = document.getElementById("table2_"+pid);
			vlines[i].style.height=(hline.offsetTop+hline2.offsetTop + <?php echo $bhalfheight+9; ?>)+'px';
		}

		vlines = document.getElementsByName("pvline");
		for (i=0; i < vlines.length; i++) {
			vlines[i].style.height=(vlines[i].parentNode.offsetHeight/2)+'px';
		}

	// Hourglass control..... Ajax arrows at the end of chart
	function changeDiv(div_id, ARID, full, spouse, width) {
		var divelement = document.getElementById(div_id);
		var oXmlHttp = createXMLHttp();
		oXmlHttp.open("get", "hourglass_ajax.php?show_full="+full+"&rootid="+ ARID + "&generations=1&box_width="+width+"&show_spouse="+spouse, true);
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
	function changeDis(div_id, ARID, full, spouse, width) {
		var divelement = document.getElementById(div_id);
		var oXmlHttp = createXMLHttp();
		oXmlHttp.open("get", "hourglass_ajax.php?type=desc&show_full="+full+"&rootid="+ ARID + "&generations=1&box_width="+width+"&show_spouse="+spouse, true);
		oXmlHttp.onreadystatechange=function() {
			if (oXmlHttp.readyState === 4) {
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
			var newHeight = Math.abs(hline.offsetHeight - (hline2.offsetTop + <?php echo $bhalfheight+5; ?>));
			vlines[i].style.height=newHeight+'px';
		}

		vlines = document.getElementsByName("bvertline");
		for (i=0; i < vlines.length; i++) {
			var pid = vlines[i].id.substr(vlines[i].id.indexOf("_")+1);
			var hline = document.getElementById("table_"+pid);
			var hline2 = document.getElementById("table2_"+pid);
			vlines[i].style.height=(hline.offsetTop+hline2.offsetTop + <?php echo $bhalfheight+5; ?>)+'px';
		}

		vlines = document.getElementsByName("pvline");
		//alert(vlines[0].parentNode.parentNode.parentNode);
		for (i=0; i < vlines.length; i++) {
			//vlines[i].parentNode.style.height="50%";
			vlines[i].style.height=(vlines[i].parentNode.offsetHeight/2)+'px';
		}
	}
</script>
<?php
		return $this;
	}
}
