<?php
// Class file for the tree navigator
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

class TreeView {
	private $name;
	private $allPartners;

	/**
	 * Treeview Constructor
	 *
	 * @param string $name the name of the TreeView object’s instance
	 */
	function __construct($name = 'tree') {
		$this->name = $name;
		$this->allPartners = WT_Filter::cookie('allPartners', 'true|false', 'true');
	}

	/**
	 * Draw the viewport which creates the draggable/zoomable framework
	 * Size is set by the container, as the viewport can scale itself automatically
	 *
	 * @param WT_Individual $rootPerson  the id of the root person
	 * @param int           $generations number of generations to draw
	 *
	 * @return string
	 */
	public function drawViewport(WT_Individual $rootPerson, $generations) {
		if (WT_SCRIPT_NAME == 'individual.php') {
			$path = $rootPerson->getHtmlUrl();
		} else {
			$path = 'module.php?mod=tree&amp;mod_action=treeview&amp;rootid=' . $rootPerson->getXref();
		}
		$r = '<a name="tv_content"></a><div id="' . $this->name . '_out" class="tv_out">';

		// Add the toolbar
		$r .= '<div id="tv_tools" class="noprint"><ul>' .
			'<li id="tvbCompact" class="tv_button"><img src="' . WT_STATIC_URL . WT_MODULES_DIR . 'tree/images/compact.png" alt="' . WT_I18N::translate('Use compact layout') . '" title="' . WT_I18N::translate('Use compact layout') . '"></li>' .
			'<li id="tvbAllPartners" class="tv_button' . ($this->allPartners === 'true' ? ' tvPressed' : '') . '"><a class="icon-sfamily" href="' . $path . '" title="' . WT_I18N::translate('Show all spouses and ancestors') . '"></a></li>';
		// Hidden loading image
		$r .= '<li class="tv_button" id="' . $this->name . '_loading"><i class="icon-loading-small"></i></li></ul>';
		$r .= '</div><h2 id="tree-title">' .
			WT_I18N::translate('Interactive tree of %s', $rootPerson->getFullName()) .
			'</h2><div id="' . $this->name . '_in" class="tv_in" dir="ltr">';
		$r .= $this->drawPerson($rootPerson, $generations, 0, null, null, true);
		$r .= '</div></div>'; // Close the tv_in and the tv_out div

		return array($r, 'var ' . $this->name . 'Handler = new TreeViewHandler("' . $this->name . '");');
	}

	/**
	 * Return a JSON structure to a JSON request
	 *
	 * @param string $list list of JSON requests
	 *
	 * @return string
	 */
	public function getPersons($list) {
		$list = explode(';', $list);
		$r = array();
		foreach ($list as $jsonRequest) {
			$firstLetter = substr($jsonRequest, 0, 1);
			$jsonRequest = substr($jsonRequest, 1);
			switch ($firstLetter) {
			case 'c':
				$fidlist = explode(',', $jsonRequest);
				$flist = array();
				foreach ($fidlist as $fid) {
					$flist[] = WT_Family::getInstance($fid);
				}
				$r[] = $this->drawChildren($flist, 1, true);
				break;
			case 'p':
				$params = explode('@', $jsonRequest);
				$fid = $params[0];
				$order = $params[1];
				$f = WT_Family::getInstance($fid);
				$r[] = $this->drawPerson($f->getHusband(), 0, 1, $f, $order);
				break;
			}
		}
		return json_encode($r);
	}

	/**
	 * Get the details for a person and their life partner(s)
	 *
	 * @param WT_Individual $individual the individual to return the details for
	 *
	 * @return string
	 */
	public function getDetails(WT_Individual $individual) {
		$r = $this->getPersonDetails($individual, null);
		foreach ($individual->getSpouseFamilies() as $family) {
			$spouse = $family->getSpouse($individual);
			if ($spouse) {
				$r .= $this->getPersonDetails($spouse, $family);
			}
		}

		return $r;
	}

	/**
	 * Return the details for a person
	 */
	private function getPersonDetails(WT_Individual $individual, WT_Family $family = null) {
		$r = $this->getThumbnail($individual);
		$r .= '<a class="tv_link" href="' . $individual->getHtmlUrl() . '">' . $individual->getFullName() . '</a> <a href="module.php?mod=tree&amp;mod_action=treeview&amp;rootid=' . $individual->getXref() . '" title="' . WT_I18N::translate('Interactive tree of %s', strip_tags($individual->getFullName())) . '" class="icon-button_indi tv_link tv_treelink"></a>';
		foreach ($individual->getFacts(WT_EVENTS_BIRT, true) as $fact) {
			$r .= $fact->summary();
		}
		if ($family) {
			foreach ($family->getFacts(WT_EVENTS_MARR, true) as $fact) {
				$r .= $fact->summary();
			}
		}
		foreach ($individual->getFacts(WT_EVENTS_DEAT, true) as $fact) {
			$r .= $fact->summary();
		}
		return '<div class="tv' . $individual->getSex() . ' tv_person_expanded">' . $r . '</div>';
	}

	/**
	 * Draw the children for some families
	 *
	 * @param Array   $familyList array of families to draw the children for
	 * @param int     $gen        number of generations to draw
	 * @param boolean $ajax       setted to true for an ajax call
	 *
	 * @return string
	 */
	private function drawChildren($familyList, $gen = 1, $ajax = false) {
		$r = '';
		$children2draw = array();
		$f2load = array();

		foreach ($familyList as $f) {
			if (empty($f)) {
				continue;
			}
			$children = $f->getChildren();
			if (count($children) > 0) {
				$f2load[] = $f->getXref();
				foreach ($children as $ch) {
					// Eliminate duplicates - e.g. when adopted by a step-parent
					$children2draw[$ch->getXref()] = $ch;
				}
			}
		}
		$tc = count($children2draw);
		if ($tc) {
			$f2load = implode(',', $f2load);
			$nbc = 0;
			foreach ($children2draw as $child) {
				$nbc++;
				if ($tc == 1) {
					$co = 'c'; // unique
				} elseif ($nbc == 1) {
					$co = 't'; // first
				} elseif ($nbc == $tc) {
					$co = 'b'; //last
				} else {
					$co = 'h';
				}
				$r .= $this->drawPerson($child, $gen - 1, -1, null, $co);
			}
			if (!$ajax) {
				$r = '<td align="right"' . ($gen == 0 ? ' abbr="c' . $f2load . '"' : '') . '>' . $r . '</td>' . $this->drawHorizontalLine();
			}
		}
		return $r;
	}

	/**
	 * Draw a person in the tree
	 *
	 * @param WT_Individual $person The Person object to draw the box for
	 * @param int           $gen    The number of generations up or down to print
	 * @param int           $state  Whether we are going up or down the tree, -1 for descendents +1 for ancestors
	 * @param WT_Family     $pfamily
	 * @param string        $order  first (1), last(2), unique(0), or empty. Required for drawing lines between boxes
	 * @param boolean       $isRoot
	 *
	 * @return string
	 *
	 * Notes : "spouse" means explicitely married partners. Thus, the word "partner"
	 * (for "life partner") here fits much better than "spouse" or "mate"
	 * to translate properly the modern french meaning of "conjoint"
	 */
	private function drawPerson(WT_Individual $person, $gen, $state, WT_Family $pfamily = null, $order = null, $isRoot = false) {
		global $TEXT_DIRECTION;

		if ($gen < 0) {
			return '';
		}
		if (!empty($pfamily)) {
			$partner = $pfamily->getSpouse($person);
		} else {
			$partner = $person->getCurrentSpouse();
		}
		if ($isRoot) {
			$r = '<table id="tvTreeBorder" class="tv_tree"><tbody><tr><td id="tv_tree_topleft"></td><td id="tv_tree_top"></td><td id="tv_tree_topright"></td></tr><tr><td id="tv_tree_left"></td><td>';
		} else {
			$r = '';
		}
		/* height 1% : this hack enable the div auto-dimensionning in td for FF & Chrome */
		$r .= '<table class="tv_tree"' . ($isRoot ? ' id="tv_tree"' : '') . ' style="height: 1%"><tbody><tr>';

		if ($state <= 0) {
			// draw children
			$r .= $this->drawChildren($person->getSpouseFamilies(), $gen);
		} else {
			// draw the parent’s lines
			$r .= $this->drawVerticalLine($order) . $this->drawHorizontalLine();
		}

		/* draw the person. Do NOT add person or family id as an id, since a same person could appear more than once in the tree !!! */
		// Fixing the width for td to the box initial width when the person is the root person fix a rare bug that happen when a person without child and without known parents is the root person : an unwanted white rectangle appear at the right of the person’s boxes, otherwise.
		$r .= '<td' . ($isRoot ? ' style="width:1px"' : '') . '><div class="tv_box' . ($isRoot ? ' rootPerson' : '') . '" dir="' . $TEXT_DIRECTION . '" style="text-align: ' . ($TEXT_DIRECTION == "rtl" ? "right" : "left") . '; direction: ' . $TEXT_DIRECTION . '" abbr="' . $person->getXref() . '" onclick="' . $this->name . 'Handler.expandBox(this, event);">';
		$r .= $this->drawPersonName($person);
		$fop = Array(); // $fop is fathers of partners
		if (!is_null($partner)) {
			$sfams = $person->getSpouseFamilies();
			$dashed = '';
			foreach ($sfams as $family) {
				$p = $family->getSpouse($person);
				if ($p) {
					if (($p === $partner) || $this->allPartners === 'true') {
						$pf = $p->getPrimaryChildFamily();
						if (!empty($pf)) {
							$fop[] = Array($pf->getHusband(), $pf);
						}
						$r .= $this->drawPersonName($p, $dashed);
						if ($this->allPartners !== 'true') {
							break; // we can stop here the foreach loop
						}
						$dashed = 'dashed';
					}
				}
			}
		}
		$r .= '</div></td>';

		$primaryChildFamily = $person->getPrimaryChildFamily();
		if (!empty($primaryChildFamily)) {
			$parent = $primaryChildFamily->getHusband();
			if (empty($parent)) {
				$parent = $primaryChildFamily->getWife();
			}
		}
		if (!empty($parent) || count($fop) || ($state < 0)) {
			$r .= $this->drawHorizontalLine();
		}
		/* draw the parents */
		if ($state >= 0 && (!empty($parent) || count($fop))) {
			$unique = (empty($parent) || count($fop) == 0);
			$r .= '<td align="left"><table class="tv_tree"><tbody>';
			if (!empty($parent)) {
				$u = $unique ? 'c' : 't';
				$r .= '<tr><td ' . ($gen == 0 ? ' abbr="p' . $primaryChildFamily->getXref() . '@' . $u . '"' : '') . '>';
				$r .= $this->drawPerson($parent, $gen - 1, 1, $primaryChildFamily, $u);
				$r .= '</td></tr>';
			}
			if (count($fop)) {
				$n = 0;
				$nb = count($fop);
				foreach ($fop as $p) {
					$n++;
					$u = $unique ? 'c' : ($n == $nb || empty($p[1]) ? 'b' : 'h');
					$r .= '<tr><td ' . ($gen == 0 ? ' abbr="p' . $p[1]->getXref() . '@' . $u . '"' : '') . '>' . $this->drawPerson($p[0], $gen - 1, 1, $p[1], $u) . '</td></tr>';
				}
			}
			$r .= '</tbody></table></td>';
		}
		if ($state < 0) {
			$r .= $this->drawVerticalLine($order);
		}
		$r .= '</tr></tbody></table>';
		if ($isRoot) {
			$r .= '</td><td id="tv_tree_right"></td></tr><tr><td id="tv_tree_bottomleft"></td><td id="tv_tree_bottom"></td><td id="tv_tree_bottomright"></td></tr></tbody></table>';
		}
		return $r;
	}

	/**
	 * Draw a person name preceded by sex icon, with parents as tooltip
	 *
	 * @param WT_Individual $individual an individual
	 * @param string        $dashed     if = 'dashed' print dashed top border to separate multiple spuses
	 *
	 * @return string
	 */
	private function drawPersonName(WT_Individual $individual, $dashed = '') {
		if ($this->allPartners === 'true') {
			$f = $individual->getPrimaryChildFamily();
			if ($f) {
				switch ($individual->getSex()) {
				case 'M':
					$title = ' title="' . strip_tags(
						/* I18N: e.g. “Son of [father name & mother name]” */
						WT_I18N::translate('Son of %s', $f->getFullName())
					) . '"';
					break;
				case 'F':
					$title = ' title="' . strip_tags(
						/* I18N: e.g. “Daughter of [father name & mother name]” */
						WT_I18N::translate('Daughter of %s', $f->getFullName())
					) . '"';
					break;
				case 'U':
					$title = ' title="' . strip_tags(
						/* I18N: e.g. “Child of [father name & mother name]” */
						WT_I18N::translate('Child of %s', $f->getFullName())
					) . '"';
					break;
				}
			} else {
				$title = '';
			}
		} else {
			$title = '';
		}
		$sex = $individual->getSex();
		$r = '<div class="tv' . $sex . ' ' . $dashed . '"' . $title . '><a href="' . $individual->getHtmlUrl() . '"></a>' . $individual->getFullName() . ' <span class="dates">' . $individual->getLifeSpan() . '</span></div>';
		return $r;
	}

	/**
	 * Get the thumbnail image for the given person
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	private function getThumbnail(WT_Individual $individual) {
		global $SHOW_HIGHLIGHT_IMAGES;

		if ($SHOW_HIGHLIGHT_IMAGES) {
			return $individual->displayImage();
		} else {
			return '';
		}
	}

	/**
	 * Draw a vertical line
	 *
	 * @param string $order A parameter that set how to draw this line with auto-redimensionning capabilities
	 *
	 * @return string
	 * WARNING : some tricky hacks are required in CSS to ensure cross-browser compliance
	 * some browsers shows an image, which imply a size limit in height,
	 * and some other browsers (ex: firefox) shows a <div> tag, which have no size limit in height
	 * Therefore, Firefox is a good choice to print very big trees.
	 */
	private function drawVerticalLine($order) {
		return '<td class="tv_vline tv_vline_' . $order . '"><div class="tv_vline tv_vline_' . $order . '"></div></td>';
	}

	/**
	 * Draw an horizontal line
	 */
	private function drawHorizontalLine() {
		return '<td class="tv_hline"><div class="tv_hline"></div></td>';
	}
}
