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
 * Class AncestryController - Controller for the ancestry chart
 */
class AncestryController extends ChartController {

	/** @var integer Show boxes for cousins */
	public $show_cousins;

	/** @var integer Determines style of chart */
	public $chart_style;

	/** @var integer Number of generations to display	 */
	public $generations;

	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();

		// Extract form parameters
		$this->show_cousins = Filter::getInteger('show_cousins', 0, 1);
		$this->chart_style  = Filter::getInteger('chart_style', 0, 3);
		$this->generations  = Filter::getInteger('PEDIGREE_GENERATIONS', 2, $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS'), $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				I18N::translate('Ancestors of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(I18N::translate('Ancestors'));
		}
	}

	/**
	 * print a child ascendancy
	 *
	 * @param         $person
	 * @param integer $sosa  child sosa number
	 * @param integer $depth the ascendancy depth to show
	 */
	public function printChildAscendancy($person, $sosa, $depth) {

		$pidarr = array();
		if ($person) {
			$pid = $person->getXref();
			$label = I18N::translate('Ancestors of %s', $person->getFullName());
		} else {
			$pid = '';
			$label = '';
		}
		// child
		echo '<li>';
		echo '<table><tr><td>';
		if ($sosa == 1) {
			echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="3" width="', Theme::theme()->parameter('chart-descendancy-indent'), '"></td><td>';
		} else {
			echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="3" width="2" alt="">';
			echo '<img src="', Theme::theme()->parameter('image-hline'), '" height="3" width="', Theme::theme()->parameter('chart-descendancy-indent') - 2, '"></td><td>';
		}
		print_pedigree_person($person, $this->showFull());
		echo '</td>';
		echo '<td>';
		if ($sosa > 1) {
			print_url_arrow('?rootid=' . $pid . '&amp;PEDIGREE_GENERATIONS=' . $this->generations . '&amp;show_full=' . $this->showFull() . '&amp;chart_style=' . $this->chart_style . '&amp;ged=' . WT_GEDURL, $label, 3);
		}
		echo '</td>';
		echo '<td class="details1">&nbsp;<span dir="ltr" class="person_box' . (($sosa == 1) ? 'NN' : (($sosa % 2) ? 'F' : '')) . '">&nbsp;', $sosa, '&nbsp;</span>&nbsp;';
		echo '</td><td class="details1">';
		$relation = '';
		$new = ($pid == '' || !isset($pidarr[$pid]));
		if (!$new) {
			$relation = '<br>[=<a href="#sosa' . $pidarr[$pid] . '">' . $pidarr[$pid] . '</a> - ' . get_sosa_name($pidarr[$pid]) . ']';
		} else {
			$pidarr[$pid] = $sosa;
		}
		echo get_sosa_name($sosa) . $relation;
		echo '</td>';
		echo '</tr></table>';

		if (is_null($person)) {
			echo '</li>';
			return;
		}
		// parents
		$family = $person->getPrimaryChildFamily();

		if ($family && $new && $depth > 0) {
			// print marriage info
			echo '<span class="details1">';
			echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="2" width="', Theme::theme()->parameter('chart-descendancy-indent'), '" alt=""><a href="#" onclick="return expand_layer(\'sosa_', $sosa, '\');" class="top"><i id="sosa_', $sosa, '_img" class="icon-minus" title="', I18N::translate('View family'), '"></i></a>';
			echo '&nbsp;<span dir="ltr" class="person_box">&nbsp;', ($sosa * 2), '&nbsp;</span>&nbsp;', I18N::translate('and');
			echo '&nbsp;<span dir="ltr" class="person_boxF">&nbsp;', ($sosa * 2 + 1), '&nbsp;</span>&nbsp;';
			if ($family->canShow()) {
				foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
					echo ' <a href="', $family->getHtmlUrl(), '" class="details1">', $fact->summary(), '</a>';
				}
			}
			echo '</span>';
			// display parents recursively - or show empty boxes
			echo '<ul id="sosa_', $sosa, '" class="generation">';
			$this->printChildAscendancy($family->getHusband(), $sosa * 2, $depth - 1);
			$this->printChildAscendancy($family->getWife(), $sosa * 2 + 1, $depth - 1);
			echo '</ul>';
		}
		echo '</li>';
	}
}
