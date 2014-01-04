<?php
// System for generating menus.
//
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Menu {
	var $label = ' ';
	var $labelpos = 'right';
	var $link = '#';
	var $onclick = null;
	var $flyout = 'down';
	var $class = '';
	var $id=null;
	var $submenuclass = '';
	var $iconclass = '';
	var $target = null;
	var $parentmenu = null;
	var $submenus;

	/**
	* Constructor for the menu class
	* @param string $label the label for the menu item (usually a wt_lang variable)
	* @param string $link The link that the user should be taken to when clicking on the menuitem
	* @param string $pos The position of the label relative to the icon (right, left, top, bottom)
	* @param string $flyout The direction where any submenus should appear relative to the menu item (right, down)
	*/
	function __construct($label=' ', $link='#', $id=null, $labelpos='right', $flyout='down')
	{
		$this->label   =$label;
		$this->labelpos=$labelpos;
		$this->link    =$link;
		$this->id      =$id;
		$this->flyout  =$flyout;
		$this->submenus=array();
	}

	function addLabel($label=' ', $pos='right')
	{
		if ($label) $this->label = $label;
		$this->labelpos = $pos;
	}

	function addLink($link='#')
	{
		$this->link = $link;
	}

	function addOnclick($onclick)
	{
		$this->onclick = $onclick;
	}

	function addFlyout($flyout='down')
	{
		$this->flyout = $flyout;
	}

	function addClass($class, $submenuclass='', $iconclass='icon_general')
	{
		$this->class = $class;
		$this->submenuclass = $submenuclass;
		$this->iconclass = $iconclass;
	}

	function addTarget($target)
	{
		$this->target = $target;
	}

	function addSubMenu($obj)
	{
		$this->submenus[] = $obj;
	}

	//
	public function __toString() {
		return $this->getMenuAsList();
	}

	// Get the menu as a simple list - for accessible interfaces, search engines and CSS menus
	function getMenuAsList() {
		$link = '';
		if ($this->link) {
			if ($this->target !== null) {
				$link .= ' target="'.$this->target.'"';
			}
			if ($this->link=='#') {
				if ($this->onclick !== null) {
					$link .= ' onclick="'.$this->onclick.'"';
				}
				$html='<a class="'.$this->iconclass.'" href="'.$this->link.'"'.$link.'>'.$this->label.'</a>';
			} else {
				$html='<a class="'.$this->iconclass.'" href="'.$this->link.'"'.$link.'>'.$this->label.'</a>';
			}
		} else {
			$html=$this->label;
		}
		if ($this->submenus) {
			$html.='<ul>';
			foreach ($this->submenus as $submenu) {
				if ($submenu) {
					if ($submenu->submenus) {
						$submenu->iconclass.=' icon_arrow';
					}
					$html.=$submenu->getMenuAsList();
				}
			}
			$html.='</ul>';
		}
		if ($this->id) {
			return '<li id="'.$this->id.'">'.$html.'</li>';
		} else {
			return '<li>'.$html.'</li>';
		}
	}

	function getMenu() {
		global $menucount, $TEXT_DIRECTION, $WT_IMAGES;

		if (!isset($menucount)) {
			$menucount = 0;
		} else {
			$menucount++;
		}
		$id = $menucount.rand();
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
		if ($this->target !== null) {
			$link .= '" target="'.$this->target;
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
	* @return int
	*/
	function subCount() {
		return count($this->submenus);
	}
}
