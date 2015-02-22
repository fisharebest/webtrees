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
	 * Dont change them! the offset calculations rely on this order
	 */
	const PORTRAIT         = 0;
	const LANDSCAPE        = 1;
	const OLDEST_AT_TOP    = 2;
	const OLDEST_AT_BOTTOM = 3;

	/**
	 * Next and previous generation arrow size
	 */
	const ARROW_SIZE = 22; //pixels

	/** @var integer Selected chart layout */
	public $orientation;

	/** @var integer Number of generation to display */
	public $generations;

	/** @var array are there ancestors for people byond the extent of the chart */
	public $ancestors = array();

	/** @var integer Number of nodes in the chart */
	public $treesize;

	/** @var array Used to calculate personbox positions */
	public $offsetarray = array();

	/**
	 * Create a pedigree controller
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();
		$this->orientation = Filter::getInteger('orientation', 0, 3, $WT_TREE->getPreference('PEDIGREE_LAYOUT'));
		$this->generations = Filter::getInteger('PEDIGREE_GENERATIONS', 2, $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS'), $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));
		$bxspacing         = Theme::theme()->parameter('chart-spacing-x');
		$byspacing         = Theme::theme()->parameter('chart-spacing-y');

		// With more than 8 generations, we run out of pixels on the <canvas>
		if ($this->generations > 8) {
			$this->generations = 8;
		}

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				I18N::translate('Pedigree tree of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(I18N::translate('Pedigree'));
		}

		$this->treesize = pow(2, $this->generations) - 1;

		// sosaAncestors() starts array at index 1 we need it to start at 0
		$this->ancestors = array_values($this->sosaAncestors($this->generations));

		// -- this next section will create and position the DIV layers for the pedigree tree
		$curgen      = 1; // -- track which generation the algorithm is currently working on
		$xoffset     = 0;

		if ($this->treesize < 3) {
			$this->treesize = 3;
		}
		// -- loop through all of IDs in the array from last to first
		// -- calculating the box positions
		for ($i = ($this->treesize - 1); $i >= 0; $i--) {
			// -- check to see if we have moved to the next generation
			if ($i < intval($this->treesize / pow(2, $curgen))) {
				$curgen++;
			}
			// -- box position in current generation
			$boxpos = $i - pow(2, $this->generations - $curgen);
			// -- offset multiple for current generation
			if ($this->orientation < self::OLDEST_AT_TOP) {
				$genoffset = pow(2, $curgen - $this->orientation);
				$boxspacing = $this->getBoxDimensions()->height + $byspacing;
			} else {
				$genoffset = pow(2, $curgen - 1);
				$boxspacing = $this->getBoxDimensions()->width + $byspacing;
			}
			// -- calculate the yoffset position in the generation put child between parents
			$yoffset = ($boxpos * ($boxspacing * $genoffset)) + (($boxspacing / 2) * $genoffset) + ($boxspacing * $genoffset);

			// -- calculate the xoffset
			switch ($this->orientation) {
				case self::PORTRAIT:
					$xoffset = ($this->generations - $curgen) * (($this->getBoxDimensions()->width + ($bxspacing * 3)) / 2);
					// -- compact the tree
					if ($curgen < $this->generations) {
						if ($i % 2 == 0) {
							$yoffset = $yoffset - (($boxspacing / 2) * ($curgen - 1));
						} else {
							$yoffset = $yoffset + (($boxspacing / 2) * ($curgen - 1));
						}
						$parent = (int)(($i - 1) / 2);
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
					$yoffset -= (($boxspacing / 2) * pow(2, ($this->generations - 2)) - ($boxspacing / 2));
					break;
				case self::LANDSCAPE:
					$xoffset = ($this->generations - $curgen) * ($this->getBoxDimensions()->width + $bxspacing);
					if ($curgen == 1) {
						$xoffset += 10;
					}
					break;
				case self::OLDEST_AT_TOP: // x & y values are reversed in pedigree.php
					$divisor = $this->showFull() ? 2 : 3;
					$xoffset = ($curgen-1) * (($this->getBoxDimensions()->width + $bxspacing) / $divisor) + $curgen;
					break;
				case self::OLDEST_AT_BOTTOM: // x & y values are reversed in pedigree.php
					$divisor = $this->showFull() ? 2 : 3;
					$xoffset = ($this->generations - $curgen) * (($this->getBoxDimensions()->width + $bxspacing) / $divisor);
					break;
			}
			$this->offsetarray[$i]["x"] = intval($xoffset);
			$this->offsetarray[$i]["y"] = intval($yoffset);
		}

		// find the minimum x & y offsets and deduct that number from
		// each value in the array so that offsets start from zero
		// could use array_column but that only works with php >= 5.5
		$min_xoffset = min(array_map(function($item) {return $item['x'];}, $this->offsetarray)) - self::ARROW_SIZE;
		$min_yoffset = min(array_map(function($item) {return $item['y'];}, $this->offsetarray));
		array_walk($this->offsetarray, function(&$item) use ($min_xoffset, $min_yoffset) {
			$item['y'] -= $min_yoffset;
			$item['x'] -= $min_xoffset;
		});
	}
}
