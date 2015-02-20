<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class PedigreeController - Controller for the pedigree chart
 */
class PedigreeController extends ChartController {

	/**
	 * Chart orientation codes
	 */
	CONST PORTRAIT          = 0;
	CONST LANDSCAPE         = 1;
	CONST OLDEST_AT_TOP     = 2;
	CONST OLDEST_AT_BOTTOM  = 3;

	/**
	 * Next and previous generation arrow size
	 */
	CONST ARROW_SIZE = 22; //pixels

	/** @var int Selected chart layout */
	public $orientation;

	/** @var int Number of generation to display */
	public $PEDIGREE_GENERATIONS;

	/** @var array are there ancestors for people byond the extent of the chart */
	public $ancestors = array();

	/** @var int Number of nodes in the chart */
	public $treesize;

	/** @var array Used to calculate personbox positions */
	public $offsetarray = array();

	/** @var int Vertical start position of the chart */
	public $basexoffset;

	/**
	 * Create a pedigree controller
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();
		$this->orientation          = Filter::getInteger('orientation', 0, 3, $WT_TREE->getPreference('PEDIGREE_LAYOUT'));
		$this->PEDIGREE_GENERATIONS = Filter::getInteger('PEDIGREE_GENERATIONS', 2, $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS'), $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));

		$this->basexoffset = Theme::theme()->parameter('chart-offset-x');
		$bxspacing   = Theme::theme()->parameter('chart-spacing-x');
		$byspacing   = Theme::theme()->parameter('chart-spacing-y');

		// With more than 8 generations, we run out of pixels on the <canvas>
		if ($this->PEDIGREE_GENERATIONS > 8) {
			$this->PEDIGREE_GENERATIONS = 8;
		}

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				I18N::translate('Pedigree tree of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(I18N::translate('Pedigree'));
		}

		//-- adjustments for portrait mode
		if ($this->orientation == self::PORTRAIT) {
			$bxspacing += 12;
		}

		$this->treesize = pow(2, (int) ($this->PEDIGREE_GENERATIONS)) - 1;

		// sosaAncestors() starts array at index 1 we need it to start at 0
		$this->ancestors = array_values($this->sosaAncestors($this->PEDIGREE_GENERATIONS));

		// -- this next section will create and position the DIV layers for the pedigree tree
		$curgen      = 1; // -- publiciable to track which generation the algorithm is currently working on
		$xoffset     = 0;

		if ($this->treesize < 3) {
			$this->treesize = 3;
		}
		// -- loop through all of IDs in the array starting at the last and working to the first
		//-- calculation the box positions
		for ($i = ($this->treesize - 1); $i >= 0; $i--) {
			// -- check to see if we have moved to the next generation
			if ($i < (int) ($this->treesize / (pow(2, $curgen)))) {
				$curgen++;
			}
			//-- box position in current generation
			$boxpos = $i - pow(2, $this->PEDIGREE_GENERATIONS - $curgen);
			//-- offset multiple for current generation
			if ($this->orientation < self::OLDEST_AT_TOP) {
				$genoffset = pow(2, $curgen - $this->orientation);
				$boxspacing = $this->getBoxDimensions()->height + $byspacing;
			} else {
				$genoffset = pow(2, $curgen - 1);
				$boxspacing = $this->getBoxDimensions()->width + $byspacing;
			}
			// -- calculate the yoffset Position in the generation Spacing between boxes put child between parents
			$yoffset = ($boxpos * ($boxspacing * $genoffset)) + (($boxspacing / 2) * $genoffset) + ($boxspacing * $genoffset);

			// -- calculate the xoffset
			switch ($this->orientation) {
				case self::PORTRAIT:
					$yoffset -= $this->getBoxDimensions()->height;
					if ($this->PEDIGREE_GENERATIONS < 6) {
						$addxoffset    = $this->basexoffset + (10 + 60 * (5 - $this->PEDIGREE_GENERATIONS));
						$xoffset = ($this->PEDIGREE_GENERATIONS - $curgen) * (($this->getBoxDimensions()->width + $bxspacing) / 2) + $addxoffset;
					} else {
						$addxoffset    = $this->basexoffset + 10;
						$xoffset = ($this->PEDIGREE_GENERATIONS - $curgen) * (($this->getBoxDimensions()->width + $bxspacing) / 2) + $addxoffset;
					}
					//-- compact the tree
					if ($curgen < $this->PEDIGREE_GENERATIONS) {
						$parent = (int)(($i - 1) / 2);
						if ($i % 2 == 0) {
							$yoffset = $yoffset - (($boxspacing / 2) * ($curgen - 1));
						} else {
							$yoffset = $yoffset + (($boxspacing / 2) * ($curgen - 1));
						}
						$pgen = $curgen;
						while ($parent > 0) {
							if ($parent % 2 == 0) {
								$yoffset = $yoffset - (($boxspacing / 2) * $pgen);
							} else {
								$yoffset = $yoffset + (($boxspacing / 2) * $pgen);
							}
							$pgen++;
							if ($pgen > 3) {
								$temp = 0;
								for ($j = 1; $j < ($pgen - 2); $j++) {
									$temp += (pow(2, $j) - 1);
								}
								if ($parent % 2 == 0) {
									$yoffset = $yoffset - (($boxspacing / 2) * $temp);
								} else {
									$yoffset = $yoffset + (($boxspacing / 2) * $temp);
								}
							}
							$parent = (int)(($parent - 1) / 2);
						}
						if ($curgen > 3) {
							$temp = 0;
							for ($j = 1; $j < ($curgen - 2); $j++) {
								$temp += (pow(2, $j) - 1);
							}
							if ($i % 2 == 0) {
								$yoffset = $yoffset - (($boxspacing / 2) * $temp);
							} else {
								$yoffset = $yoffset + (($boxspacing / 2) * $temp);
							}
						}

					}
					$yoffset -= (($boxspacing / 2) * pow(2, ($this->PEDIGREE_GENERATIONS - 2)) - ($boxspacing / 2));
					break;
				case self::LANDSCAPE:
					$xoffset = self::ARROW_SIZE + $this->basexoffset + (($this->PEDIGREE_GENERATIONS - $curgen) * ($this->getBoxDimensions()->width + $bxspacing));
					if ($this->PEDIGREE_GENERATIONS < 4) {
						$xoffset += 60;
					}
					break;
				case self::OLDEST_AT_TOP: // x & y values are reversed in pedigree.php
					if ($this->PEDIGREE_GENERATIONS > 3) {
						$yoffset -= $this->getBoxDimensions()->height;
					}
					if ($this->showFull()) {
						$xoffset = (int) ($curgen-1) * (($this->getBoxDimensions()->width + $bxspacing) / 2) + $curgen + self::ARROW_SIZE;
					} else {
						$xoffset = (int) ($curgen-1) * (($this->getBoxDimensions()->width + $bxspacing) / 3) + $curgen + self::ARROW_SIZE;
					}
					break;
				case self::OLDEST_AT_BOTTOM: // x & y values are reversed in pedigree.php
					if ($this->PEDIGREE_GENERATIONS > 3) {
						$yoffset -= $this->getBoxDimensions()->height;
					}
					if ($this->showFull()) {
						$xoffset = (int) ($this->PEDIGREE_GENERATIONS - $curgen) * (($this->getBoxDimensions()->width + $bxspacing) / 2) + $this::ARROW_SIZE;
					} else {
						$xoffset = (int) ($this->PEDIGREE_GENERATIONS - $curgen) * (($this->getBoxDimensions()->width + $bxspacing) / 3) + $this::ARROW_SIZE;
					}
					break;
			}
			if ($curgen == 1 && $this->orientation == self::LANDSCAPE) {
				$xoffset += 10;
			}
			$this->offsetarray[$i]["x"] = (int) $xoffset;
			$this->offsetarray[$i]["y"] = (int) $yoffset;
		}
	}

	/**
	 * Get the name of the person at the root of the tree.
	 *
	 * @return string
	 */
	function getPersonName() {
		if (is_null($this->root)) {
			return I18N::translate('unknown');
		} else {
			return $this->root->getFullName();
		}
	}
}
