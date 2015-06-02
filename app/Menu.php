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

use Rhumsaa\Uuid\Uuid;

/**
 * Class Menu - System for generating menus.
 */
class Menu {
	/** @var string The text to be displayed in the mneu */
	private $label;

	/** @var string The target URL or href*/
	private $link;

	/** @var string The CSS class used to style this menu item */
	private $class;

	/** @var string An onclick action, typically used with a link of "#" */
	private $onclick;

	/** @var Menu[] */
	private $submenus;

	/** @var string Used internally to create javascript menus */
	private $parentmenu;

	/** @var string Used to format javascript menus */
	private $submenuclass;

	/** @var string Used to format javascript menus */
	private $iconclass;

	/** @var string Used to format javascript menus */
	private $menuclass;

	/**
	 * Constructor for the menu class
	 *
	 * @param string    $label    The label for the menu item
	 * @param string    $link     The target URL
	 * @param string    $class    An CSS class
	 * @param string    $onclick  A javascript onclick handler
	 * @param Menu[] $submenus Any submenus
	 */
	public function __construct($label, $link = '#', $class = '', $onclick = '', $submenus = array()) {
		$this
			->setLabel($label)
			->setLink($link)
			->setClass($class)
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
	 * Render this menu using Bootstrap markup
	 *
	 * @return string
	 */
	public function bootstrap() {
		if ($this->submenus) {
			$submenus = '';
			foreach ($this->submenus as $submenu) {
				$submenus .= $submenu->bootstrap();
			}

			return
				'<li class="' . $this->class . ' dropdown">' .
				'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">' .
				$this->label .
				' <span class="caret"></span></a>' .
				'<ul class="dropdown-menu" role="menu">' .
				$submenus .
				'</ul>' .
				'</li>';
		} else {
			if ($this->onclick) {
				$onclick = ' onclick="' . $this->onclick . '"';
			} else {
				$onclick = '';
			}

			return '<li class="' . $this->class . '"><a href="' . $this->link . '"' . $onclick . '>' . $this->label . '</a></li>';
		}
	}

	/**
	 * Set the CSS classes for the (legacy) javascript menus
	 *
	 * @param string $menuclass
	 * @param string $submenuclass
	 * @param string $iconclass
	 */
	public function addClass($menuclass, $submenuclass = '', $iconclass = '') {
		$this->menuclass    = $menuclass;
		$this->submenuclass = $submenuclass;
		$this->iconclass    = $iconclass;
	}

	/**
	 * @return string
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * @param string $class
	 *
	 * @return $this
	 */
	public function setClass($class) {
		$this->class = $class;

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
	 * @return string
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
	 * @param Menu $menu
	 *
	 * @return $this
	 */
	public function addSubmenu($menu) {
		$this->submenus[] = $menu;

		return $this;
	}

	/**
	 * Render this menu using javascript popups..
	 *
	 * @return string
	 */
	public function getMenu() {
		$menu_id     = 'menu-' . Uuid::uuid4();
		$sub_menu_id = 'sub-' . $menu_id;

		$html = '<a href="' . $this->link . '"';
		if ($this->onclick) {
			$html .= ' onclick="' . $this->onclick . '"';
		}
		if (!empty($this->submenus)) {
			$html .= ' onmouseover="show_submenu(\'' . $sub_menu_id . '\', \'' . $menu_id . '\');"';
			$html .= ' onmouseout="timeout_submenu(\'' . $sub_menu_id . '\');"';
		}
		$html .= '>' . $this->label . '</a>';

		if (!empty($this->submenus)) {
			$html .= '<div id="' . $sub_menu_id . '" class="' . $this->submenuclass . '"';
			$html .= ' style="position: absolute; visibility: hidden; z-index: 100; text-align: ' . (I18N::direction() === 'ltr' ? 'left' : 'right') . '"';
			$html .= ' onmouseover="show_submenu(\'' . $this->parentmenu . '\'); show_submenu(\'' . $sub_menu_id . '\');"';
			$html .= ' onmouseout="timeout_submenu(\'' . $sub_menu_id . '\');">';
			foreach ($this->submenus as $submenu) {
				$submenu->parentmenu = $sub_menu_id;
				$html .= $submenu->getMenu();
			}
			$html .= '</div></div>';
		}

		return '<div id="' . $menu_id . '" class="' . $this->menuclass . '">' . $html . '</div>';
	}

	/**
	 * Render this menu as an HTML list
	 *
	 * @return string
	 */
	public function getMenuAsList() {
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
		$html = '<a' . $link . $onclick . '>' . $this->label . '</a>';
		if ($this->submenus) {
			$html .= '<ul>';
			foreach ($this->submenus as $submenu) {
				$html .= $submenu->getMenuAsList();
			}
			$html .= '</ul>';
		}

		return '<li class="' . $this->class . '">' . $html . '</li>';
	}

	/**
	 * @return Menu[]
	 */
	public function getSubmenus() {
		return $this->submenus;
	}

	/**
	 * @param Menu[] $submenus
	 *
	 * @return $this
	 */
	public function setSubmenus(array $submenus) {
		$this->submenus = $submenus;

		return $this;
	}
}
