<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class WT_Menu - System for generating menus.
 */
class WT_Menu {
	var $label = ' ';
	var $labelpos = 'right';
	var $link = '#';
	var $onclick = null;
	var $flyout = 'down';
	var $class = '';
	var $id = null;
	var $submenuclass = '';
	var $iconclass = '';
	var $parentmenu = null;

	/** @var WT_Menu[] */
	var $submenus;

	/**
	 * Constructor for the menu class
	 *
	 * @param string $label    The label for the menu item (usually a wt_lang variable)
	 * @param string $link     The link that the user should be taken to when clicking on the menuitem
	 * @param string $id       An optional CSS ID
	 * @param string $labelpos The position of the label relative to the icon (right, left, top, bottom)
	 * @param string $flyout   The direction where any submenus should appear relative to the menu item (right, down)
	 */
	function __construct($label = ' ', $link = '#', $id = '', $labelpos = 'right', $flyout = 'down') {
		$this->label = $label;
		$this->labelpos = $labelpos;
		$this->link = $link;
		$this->id = $id;
		$this->flyout = $flyout;
		$this->submenus = array();
	}

	/**
	 * Add a label to this menu.
	 *
	 * @param string $label
	 * @param string $pos
	 */
	function addLabel($label = ' ', $pos = 'right') {
		if ($label) {
			$this->label = $label;
		}
		$this->labelpos = $pos;
	}

	/**
	 * Add a URL/link to this menu.
	 *
	 * @param string $link
	 */
	function addLink($link = '#') {
		$this->link = $link;
	}

	/**
	 * Add an onclick event to this menu.
	 *
	 * @param $onclick
	 */
	function addOnclick($onclick) {
		$this->onclick = $onclick;
	}

	/**
	 * Set the submenu direction for this menu.
	 *
	 * @param string $flyout
	 */
	function addFlyout($flyout = 'down') {
		$this->flyout = $flyout;
	}

	/**
	 * Set the CSS classes for this menu
	 *
	 * @param string $class
	 * @param string $submenuclass
	 * @param string $iconclass
	 */
	function addClass($class, $submenuclass = '', $iconclass = 'icon_general') {
		$this->class = $class;
		$this->submenuclass = $submenuclass;
		$this->iconclass = $iconclass;
	}

	/**
	 * Add a submenu to this menu
	 *
	 * @param WT_Menu []
	 */
	function addSubMenu($obj) {
		$this->submenus[] = $obj;
	}

	/**
	 * Convert this menu to an HTML list, for easy rendering of
	 * lists of menus/nulls.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->getMenuAsList();
	}

	/**
	 * Render this menu as an HTML list - for accessible interfaces, search engines and CSS menus
	 *
	 * @return string
	 */
	function getMenuAsList() {
		$link = '';
		if ($this->link) {
			if ($this->link == '#') {
				if ($this->onclick !== null) {
					$link .= ' onclick="' . $this->onclick . '"';
				}
				$html = '<a class="' . $this->iconclass . '" href="' . $this->link . '"' . $link . '>' . $this->label . '</a>';
			} else {
				$html = '<a class="' . $this->iconclass . '" href="' . $this->link . '"' . $link . '>' . $this->label . '</a>';
			}
		} else {
			$html = $this->label;
		}
		if ($this->submenus) {
			$html .= '<ul>';
			foreach ($this->submenus as $submenu) {
				if ($submenu) {
					if ($submenu->submenus) {
						$submenu->iconclass .= ' icon_arrow';
					}
					$html .= $submenu->getMenuAsList();
				}
			}
			$html .= '</ul>';
		}
		if ($this->id) {
			return '<li id="' . $this->id . '">' . $html . '</li>';
		} else {
			return '<li>' . $html . '</li>';
		}
	}

	/**
	 * Render this menu using javascript popups..
	 *
	 * @return string
	 */
	function getMenu() {
		global $menucount, $TEXT_DIRECTION;

		if (!isset($menucount)) {
			$menucount = 0;
		} else {
			$menucount++;
		}
		$id = $menucount . rand();
		$c = count($this->submenus);
		$output = "<div id=\"menu{$id}\" class=\"{$this->class}\">";
		$link = "<a href=\"{$this->link}\" onmouseover=\"";
		if ($c >= 0) {
			$link .= "show_submenu('menu{$id}_subs', 'menu{$id}', '{$this->flyout}');";
		}
		$link .= '" onmouseout="';
		if ($c >= 0) {
			$link .= "timeout_submenu('menu{$id}_subs');";
		}
		if ($this->onclick !== null) {
			$link .= "\" onclick=\"{$this->onclick}";
		}
		$link .= "\">";
		$output .= $link;
		$output .= $this->label;
		$output .= "</a>";

		if ($c > 0) {
			$submenuid = "menu{$id}_subs";
			if ($TEXT_DIRECTION == 'ltr') {
				$output .= '<div style="text-align: left;">';
			} else {
				$output .= '<div style="text-align: right;">';
			}
			$output .= "<div id=\"menu{$id}_subs\" class=\"{$this->submenuclass}\" style=\"position: absolute; visibility: hidden; z-index: 100;";
			if ($this->flyout == 'right') {
				if ($TEXT_DIRECTION == 'ltr') {
					$output .= ' left: 80px;';
				} else {
					$output .= ' right: 50px;';
				}
			}
			$output .= "\" onmouseover=\"show_submenu('{$this->parentmenu}'); show_submenu('{$submenuid}');\" onmouseout=\"timeout_submenu('menu{$id}_subs');\">";
			foreach ($this->submenus as $submenu) {
				$submenu->parentmenu = $submenuid;
				$output .= $submenu->getMenu();
			}
			$output .= "</div></div>";
		}
		$output .= "</div>";

		return $output;
	}

	/**
	 * returns the number of submenus in this menu
	 *
	 * @return integer
	 */
	function subCount() {
		return count($this->submenus);
	}
}
