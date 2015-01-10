<?php
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

/**
 * Class WT_Controller_Pedigree - Controller for the pedigree chart
 */
class WT_Controller_Pedigree extends WT_Controller_Chart {
	var $rootid;
	var $name;
	var $addname;
	var $show_full;
	var $talloffset;
	var $PEDIGREE_GENERATIONS;
	var $pbwidth;
	var $pbheight;
	var $ancestors;
	var $treesize;
	var $curgen;
	var $yoffset;
	var $xoffset;
	var $prevyoffset;
	var $offsetarray;
	var $minyoffset;

	/**
	 * Create a pedigree controller
	 */
	public function __construct() {
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT, $MAX_PEDIGREE_GENERATIONS;
		global $DEFAULT_PEDIGREE_GENERATIONS;
		global $bwidth, $bheight, $cbwidth, $cbheight, $baseyoffset, $basexoffset, $byspacing, $bxspacing;
		global $linewidth, $shadowcolor, $shadowblur, $shadowoffsetX, $shadowoffsetY;

		global $show_full, $talloffset;

		parent::__construct();

		$this->linewidth = $linewidth;
		$this->shadowcolor = $shadowcolor;
		$this->shadowblur = $shadowblur;
		$this->shadowoffsetX = $shadowoffsetX;
		$this->shadowoffsetY = $shadowoffsetY;

		$this->show_full            = WT_Filter::getInteger('show_full', 0, 1, $PEDIGREE_FULL_DETAILS);
		$this->talloffset           = WT_Filter::getInteger('talloffset', 0, 3, $PEDIGREE_LAYOUT);
		$this->box_width            = WT_Filter::getInteger('box_width', 50, 300, 100);
		$this->PEDIGREE_GENERATIONS = WT_Filter::getInteger('PEDIGREE_GENERATIONS', 2, $MAX_PEDIGREE_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);

		if ($this->talloffset==1) $this->talloffset=1; // Make SURE this is an integer
		if ($this->talloffset>1 && $this->PEDIGREE_GENERATIONS>8) $this->PEDIGREE_GENERATIONS=8;

		// TODO: some library functions expect this as a global.
		// Passing a function parameter would be much better.
		global $PEDIGREE_GENERATIONS;
		$PEDIGREE_GENERATIONS=$this->PEDIGREE_GENERATIONS;

		// This is passed as a global.  A parameter would be better...
		$this->show_full = ($this->show_full) ? 1 : 0; // Make SURE this is an integer
		if ($this->talloffset>3) {
			$this->talloffset=3;
		} elseif ($this->talloffset<0) {
			$this->talloffset=0;
		}
		$show_full = $this->show_full;
		$talloffset = $this->talloffset;

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				WT_I18N::translate('Pedigree tree of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(WT_I18N::translate('Pedigree'));
		}

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $cbwidth;
			$bheight = $cbheight;
		}
		//-- adjustments for portrait mode
		if ($this->talloffset==0) {
			$bxspacing+=12;
			$baseyoffset -= 20*($this->PEDIGREE_GENERATIONS-1);
		}

		$this->pbwidth = $bwidth+6;
		$this->pbheight = $bheight+5;

		$this->ancestors = $this->sosaAncestors($PEDIGREE_GENERATIONS);
		$this->treesize = pow(2, (int)($this->PEDIGREE_GENERATIONS))-1;

		// sosaAncestors() puts everyone at $i+1
		for ($i=0; $i<$this->treesize; $i++) {
			$this->ancestors[$i] = $this->ancestors[$i+1];
		}

		if (!$this->show_full) {
			if ($this->talloffset==0) {
				$baseyoffset = 160+$bheight*2;
			} elseif ($this->talloffset==1) {
				$baseyoffset = 180+$bheight*2;
			} elseif ($this->talloffset>1) {
				if ($this->PEDIGREE_GENERATIONS==3) {
					$baseyoffset = 30;
				} else {
					$baseyoffset = -85;
				}
			}
		} else {
			if ($this->talloffset==0) {
				$baseyoffset = 100+$bheight/2;
			} elseif ($this->talloffset==1) {
				$baseyoffset = 160+$bheight/2;
			} elseif ($this->talloffset>1) {
				if ($this->PEDIGREE_GENERATIONS==3) {
					$baseyoffset = 30;
				} else {
					$baseyoffset = -85;
				}
			}
		}
		// -- this next section will create and position the DIV layers for the pedigree tree
		$this->curgen = 1;    // -- variable to track which generation the algorithm is currently working on
		$this->yoffset=0;     // -- used to offset the position of each box as it is generated
		$this->xoffset=0;
		$this->prevyoffset=0; // -- used to track the y position of the previous box
		$this->offsetarray = array();
		$this->minyoffset = 0;
		if ($this->treesize<3) $this->treesize=3;
		// -- loop through all of IDs in the array starting at the last and working to the first
		//-- calculation the box positions
		for ($i=($this->treesize-1); $i>=0; $i--) {
			// -- check to see if we have moved to the next generation
			if ($i < (int)($this->treesize / (pow(2, $this->curgen)))) {
				$this->curgen++;
			}
			//-- box position in current generation
			$boxpos = $i-pow(2, $this->PEDIGREE_GENERATIONS-$this->curgen);
			//-- offset multiple for current generation
			if ($this->talloffset < 2) {
				$genoffset = pow(2, $this->curgen-$this->talloffset);
				$boxspacing = $this->pbheight+$byspacing;
			}
			else {
				$genoffset = pow(2, $this->curgen-1);
				$boxspacing = $this->pbwidth+$byspacing;
			}
			// -- calculate the yoffset Position in the generation Spacing between boxes put child between parents
			$this->yoffset = $baseyoffset+($boxpos * ($boxspacing * $genoffset))+(($boxspacing/2)*$genoffset)+($boxspacing * $genoffset);
			// -- calculate the xoffset
			if ($this->talloffset==0) {
				if ($this->PEDIGREE_GENERATIONS<6) {
					$addxoffset = $basexoffset+(10+60*(5-$this->PEDIGREE_GENERATIONS));
					$this->xoffset = ($this->PEDIGREE_GENERATIONS - $this->curgen) * (($this->pbwidth+$bxspacing) / 2)+$addxoffset;
				}
				else {
					$addxoffset = $basexoffset+10;
					$this->xoffset = ($this->PEDIGREE_GENERATIONS - $this->curgen) * (($this->pbwidth+$bxspacing) / 2)+$addxoffset;
				}
				//-- compact the tree
				if ($this->curgen<$this->PEDIGREE_GENERATIONS) {
					$parent = (int)(($i-1)/2);
					if ($i%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * ($this->curgen-1));
					else $this->yoffset=$this->yoffset + (($boxspacing/2) * ($this->curgen-1));
					$pgen = $this->curgen;
					while ($parent>0) {
						if ($parent%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * $pgen);
						else $this->yoffset=$this->yoffset + (($boxspacing/2) * $pgen);
						$pgen++;
						if ($pgen>3) {
							$temp=0;
							for ($j=1; $j<($pgen-2); $j++) $temp += (pow(2, $j)-1);
							if ($parent%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * $temp);
							else $this->yoffset=$this->yoffset + (($boxspacing/2) * $temp);
						}
						$parent = (int)(($parent-1)/2);
					}
					if ($this->curgen>3) {
						$temp=0;
							for ($j=1; $j<($this->curgen-2); $j++) $temp += (pow(2, $j)-1);
						if ($i%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * $temp);
						else $this->yoffset=$this->yoffset + (($boxspacing/2) * $temp);
					}

				}
				$this->yoffset-=(($boxspacing/2)*pow(2,($this->PEDIGREE_GENERATIONS-2))-($boxspacing/2));
			}
			else if ($this->talloffset==1) {
				$this->xoffset = 22 + $basexoffset + (($this->PEDIGREE_GENERATIONS - $this->curgen) * ($this->pbwidth+$bxspacing));
				if ($this->curgen == $this->PEDIGREE_GENERATIONS) $this->xoffset;
				if ($this->PEDIGREE_GENERATIONS<4) $this->xoffset += 60;
			}
			else if ($this->talloffset==2) {
				if ($this->show_full) $this->xoffset = ($this->curgen) * (($this->pbwidth+$bxspacing) / 2)+($this->curgen)*10+136.5;
				else $this->xoffset = ($this->curgen) * (($this->pbwidth+$bxspacing) / 4)+($this->curgen)*10+215.75;
			}
			else {
				if ($this->show_full) $this->xoffset = ($this->PEDIGREE_GENERATIONS - $this->curgen) * (($this->pbwidth+$bxspacing) / 2)+260;
				else $this->xoffset = ($this->PEDIGREE_GENERATIONS - $this->curgen) * (($this->pbwidth+$bxspacing) / 4)+270;
			}
			if ($this->curgen == 1 && $this->talloffset==1) $this->xoffset += 10;
			$this->offsetarray[$i]["x"]=$this->xoffset;
			$this->offsetarray[$i]["y"]=$this->yoffset;
		}

		//-- calculate the smallest yoffset and adjust the tree to that offset
		$minyoffset = 0;
		for ($i=0; $i<count($this->ancestors); $i++) {
			if (!empty($offsetarray[$i])) {
				if (($minyoffset==0)||($minyoffset>$this->offsetarray[$i]["y"]))  $minyoffset = $this->offsetarray[$i]["y"];
			}
		}
	}

	/**
	 * Get the name of the person at the root of the tree.
	 *
	 * @return string
	 */
	function getPersonName() {
		if (is_null($this->root)) {
			return WT_I18N::translate('unknown');
		} else {
			return $this->root->getFullName();
		}
	}
}
