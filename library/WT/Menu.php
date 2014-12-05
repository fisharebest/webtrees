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
	/** @var string The text to be displayed in the mneu */
	var $label = ' ';

	/** @var string The target URL or href*/
	var $link = '#';

	/** @var string The CSS ID to be used for this menu item */
	var $id = null;

	/** @var string An onclick action, typically used with a link of "#" */
	var $onclick = null;

	/** @var WT_Menu[] */
	var $submenus;

	/** @var string Used internally to create javascript menus */
	var $parentmenu = null;

	/** @var string Used to format javascript menus */
	var $submenuclass = '';

	/** @var string Used to format javascript menus */
	var $iconclass = '';

	/** @var string Used to format javascript menus */
	var $class = '';

	/**
	 * Constructor for the menu class
	 *
	 * @param string    $label    The label for the menu item
	 * @param string    $link     The target URL
	 * @param string    $id       An CSS identifier
	 * @param string    $onclick  A javascript onclick handler
	 * @param WT_Menu[] $submenus Any submenus
	 */
	function __construct($label, $link = '#', $id = '', $onclick = '', $submenus = array()) {
		$this
			->setLabel($label)
			->setLink($link)
			->setId($id)
			->setOnclick($onclick)
			->setSubmenus($submenus);
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
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @return $this
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 *
	 * @return $this
	 */
	public function setLabel($label) {
		$this->label = $label;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @param string $link
	 *
	 * @return $this
	 */
	public function setLink($link) {
		$this->link = $link;

		return $this;
	}

	/**
	 * @return null
	 */
	public function getOnclick() {
		return $this->onclick;
	}

	/**
	 * @param string $onclick
	 *
	 * @return $this
	 */
	public function setOnclick($onclick) {
		$this->onclick = $onclick;

		return $this;
	}

	/**
	 * Add a submenu to this menu
	 *
	 * @param WT_Menu []
	 */
	function addSubmenu($menu) {
		$this->submenus[] = $menu;
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
			$link .= "show_submenu('menu{$id}_subs', 'menu{$id}');";
		}
		$link .= '" onmouseout="';
		if ($c >= 0) {
			$link .= "timeout_submenu('menu{$id}_subs');";
		}
		if ($this->onclick) {
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
	 * Render this menu as an HTML list
	 *
	 * @return string
	 */
	function getMenuAsList() {
		if ($this->iconclass) {
			$class = ' class="' . $this->iconclass . '"';
		} else {
			$class = '';
		}
		if ($this->onclick) {
			$onclick = ' onclick="' . $this->onclick . '"';
		} else {
			$onclick = '';
		}
		if ($this->link) {
			$link = ' href="' . $this->link . '"';
		} else {
			$link = '';
		}
		if ($this->id) {
			$id = ' id="' . $this->id . '"';
		} else {
			$id = '';
		}
		$html = '<a' . $link . $class . $onclick . '>' . $this->label . '</a>';
		if ($this->submenus) {
			$html .= '<ul>';
			foreach ($this->submenus as $submenu) {
				$html .= $submenu->getMenuAsList();
			}
			$html .= '</ul>';
		}

		return '<li' . $id . '>' . $html . '</li>';
	}

	/**
	 * @return WT_Menu[]
	 */
	public function getSubmenus() {
		return $this->submenus;
	}

	/**
	 * @param WT_Menu[] $submenus
	 *
	 * @return $this
	 */
	public function setSubmenus(array $submenus) {
		$this->submenus = $submenus;

		return $this;
	}
}
