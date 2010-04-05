<?php
/**
* System for generating menus.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team. All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* @package webtrees
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_MENU_PHP', '');

class Menu {
	var $separator = false;
	var $label = ' ';
	var $labelpos = 'right';
	var $link = '#';
	var $onclick = null;
	var $icon = null;
	var $hovericon = null;
	var $flyout = 'down';
	var $class = '';
	var $hoverclass = '';
	var $submenuclass = '';
	var $iconclass = '';
	var $target = null;
	var $parentmenu = null;
	var $submenus;

	/**
	* Constructor for the menu class
	* @param string $label the label for the menu item (usually a pgv_lang variable)
	* @param string $link The link that the user should be taken to when clicking on the menuitem
	* @param string $pos The position of the label relative to the icon (right, left, top, bottom)
	* @param string $flyout The direction where any submenus should appear relative to the menu item (right, down)
	*/
	function __construct($label=' ', $link='#', $pos='right', $flyout='down')
	{
		$this->submenus = array();
		$this->addLink($link);
		$this->addLabel($label, $pos);
		$this->addFlyout($flyout);
	}

	function isSeparator()
	{
		$this->separator = true;
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

	function addIcon($icon, $hovericon=null)
	{
		if (file_exists($icon)) $this->icon = $icon;
		else $this->icon = null;
		if (file_exists($hovericon)) $this->hovericon = $hovericon;
		else $this->hovericon = null;
	}

	function addFlyout($flyout='down')
	{
		$this->flyout = $flyout;
	}

	function addClass($class, $hoverclass='', $submenuclass='', $iconclass='icon_general')
	{
		$this->class = $class;
		$this->hoverclass = $hoverclass;
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

	function addSeparator() {
		$submenu = new Menu();
		$submenu->isSeparator();
		$this->submenus[] = $submenu;
	}

	// Get the menu as a simple list - for accessible interfaces, search engines and CSS menus
	function getMenuAsList() {
		$link = '';
		if ($this->separator) {
			return "\t".'<li class="separator"><span></span></li>'."\n";
		}
		if ($this->link) {
			if ($this->target !== null)	{
				$link .= ' target="'.$this->target.'"';
			}
			if ($this->link=='#') {
				$this->link = "javascript:;";
				if ($this->onclick !== null) {
					$link .= ' onclick="'.$this->onclick.'"';
				}
					$html='<a class="'.$this->iconclass.'" href="'.$this->link.'"'.$link.'>'.$this->label.'</a>';
			} else {
					$html='<a class="'.$this->iconclass.'" href="'.$this->link.'">'.$this->label.'</a>';
			}
		} else {
			return '';
		}
		if ($this->submenus) {
			$html.="\n\t".'<ul>'."\n";
			foreach ($this->submenus as $submenu) {
				$html.="\t".$submenu->getMenuAsList();
			}
			$html.="\t".'</ul>';
			return '<li class="node">'.$html.'</li>'."\n";
		}

		return '<li>'.$html.'</li>'."\n";
	}

	// Get the menu as a dropdown form element
	function getMenuAsDropdown() {
		if ($this->separator || !$this->link && !$this->submenus) {
			return '';
		}
		if ($this->submenus) {
			$options='<option value="'.$this->link.'">'.$this->label.'</option>';
			foreach ($this->submenus as $submenu) {
				$options.=$submenu->getMenuAsDropdown();
			}
			return '<select onchange="document.location=this.value;">'.$options.'</select>';
		} else {
			return '<option value="'.$this->link.'">'.$this->label.'</option>';
		}
	}

	// Get the menu as a list of icons
	function getMenuAsIcons() {
		if ($this->separator || !$this->link && !$this->submenus) {
			return '';
		}
		$icons=array();
		if ($this->icon) {
			$icons[]='<a href="'.$this->link.'"><img onmouseover="this.className=\''.$this->hoverclass.'\'" onmouseout="this.className=\''.$this->class.'\'" class="'.$this->class.'" src="'.$this->icon.'" alt="'.$this->label.'" title="'.$this->label.'" /></a>';
		}
		if ($this->submenus) {
			foreach ($this->submenus as $submenu) {
				$icons[]=$submenu->getMenuAsIcons();
			}
		}
		return join(' ', $icons);
	}

	function getMenu()
	{
		global
			$menucount,
			$TEXT_DIRECTION,
			$WT_IMAGE_DIR,
			$WT_IMAGES
		;

		if (!isset($menucount))
		{
			$menucount = 0;
		}
		else
		{
			$menucount++;
		}
		$id = $menucount.rand();
		if ($this->separator)
		{
			$output = "<div id=\"menu{$id}\" class=\"menu_separator center\">"
			."<img src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['hline']['other']}\" alt=\"\" />"
			."</div>";
			return $output;
		}
		$c = count($this->submenus);
		$output = "<div id=\"menu{$id}\" class=\"{$this->class}\">\n";
		if ($this->link=="#") $this->link = "javascript:;";
		$link = "<a href=\"{$this->link}\" onmouseover=\"";
		if ($c >= 0)
		{
			$link .= "show_submenu('menu{$id}_subs', 'menu{$id}', '{$this->flyout}'); ";
		}
		if ($this->hoverclass !== null)
		{
			$link .= "change_class('menu{$id}', '{$this->hoverclass}'); ";
		}
		if ($this->hovericon !== null)
		{
			$link .= "change_icon('menu{$id}_icon', '{$this->hovericon}'); ";
		}
		$link .= '" onmouseout="';
		if ($c >= 0)
		{
			$link .= "timeout_submenu('menu{$id}_subs'); ";
		}
		if ($this->hoverclass !== null)
		{
			$link .= "change_class('menu{$id}', '{$this->class}'); ";
		}
		if ($this->hovericon !== null)
		{
			$link .= "change_icon('menu{$id}_icon', '{$this->icon}'); ";
		}
		if ($this->onclick !== null)
		{
			$link .= "\" onclick=\"{$this->onclick}";
		}
		if ($this->target !== null)
		{
			$link .= '" target="'.$this->target;
		}
		$link .= "\">";
		if ($this->icon !== null) {
			$tempTitle = str_replace("\"", '', $this->label);
			$MenuIcon = "<img id=\"menu{$id}_icon\" src=\"{$this->icon}\" class=\"icon\" alt=\"{$tempTitle}\" title=\"{$tempTitle}\" />";
			switch ($this->labelpos) {
			case "right":
				$output .= $link;
				$output .= $MenuIcon;
				$output .= $this->label;
				$output .= "</a>";
				break;
			case "left":
				$output .= $link;
				$output .= $this->label;
				$output .= $MenuIcon;
				$output .= "</a>";
				break;
			case "down":
				$output .= $link;
				$output .= $MenuIcon;
				$output .= "<br />";
				$output .= $this->label;
				$output .= "</a>";
				break;
			case "up":
				$output .= $link;
				$output .= $this->label;
				$output .= "<br />";
				$output .= $MenuIcon;
				$output .= "</a>";
				break;
			default:
				$output .= $link;
				$output .= $MenuIcon;
				$output .= "</a>";
			}
		}
		else
		{
			$output .= $link;
			$output .= $this->label;
			$output .= "</a>";
		}

		if ($c > 0)
		{
			$submenuid = "menu{$id}_subs";
			if ($TEXT_DIRECTION == 'ltr')
			{
				$output .= '<div style="text-align: left;">';
			}
			else
			{
				$output .= '<div style="text-align: right;">';
			}
			$output .= "<div id=\"menu{$id}_subs\" class=\"{$this->submenuclass}\" style=\"position: absolute; visibility: hidden; z-index: 100;";
			if ($this->flyout == 'right')
			{
				if ($TEXT_DIRECTION == 'ltr')
				{
					$output .= ' left: 80px;';
				}
				else
				{
					$output .= ' right: 50px;';
				}
			}
			$output .= "\" onmouseover=\"show_submenu('{$this->parentmenu}'); show_submenu('{$submenuid}');\" onmouseout=\"timeout_submenu('menu{$id}_subs');\">\n";
			foreach($this->submenus as $submenu)
			{
				$submenu->parentmenu = $submenuid;
				$output .= $submenu->getMenu();
			}
			$output .= "</div></div>\n";
		}
		$output .= "</div>\n";
		return $output;
	}

	function printMenu() {
		global $WT_MENUS_AS_LISTS;

		if ($WT_MENUS_AS_LISTS) {
			echo $this->getMenuAsList();
		} else {
			echo $this->getMenu();
		}
	}

	/**
	* returns the number of submenu's in this menu
	* @return int
	*/
	function subCount() {
		return count($this->submenus);
	}

	/**
	* convert an old array style menu to an object
	* @static
	*/
	static function convertMenu($menu) {
		$conv = array(
			'label'=>'label',
			'labelpos'=>'labelpos',
			'icon'=>'icon',
			'hovericon'=>'hovericon',
			'link'=>'link',
			'class'=>'class',
			'hoverclass'=>'hoverclass',
			'flyout'=>'flyout',
			'submenuclass'=>'submenuclass',
			'onclick'=>'onclick'
		);
		$obj = new Menu();
		if ($menu == 'separator') {
			$obj->isSeparator();
			$obj->printMenu();
			return;
		}
		$items = false;
		foreach ($menu as $k=>$v) {
			if ($k == 'items' && is_array($v) && count($v) > 0) $items = $v;
			else {
				if (isset($conv[$k])){
					if ($v != '') {
						$obj->$conv[$k] = $v;
					}
				}
			}
		}
		if ($items !== false) {
			foreach ($items as $sub) {
				$sobj = new Menu();
				if ($sub == 'separator') {
					$sobj->isSeparator();
					$obj->addSubmenu($sobj);
					continue;
				}
				foreach ($sub as $k2=>$v2) {
					if (isset($conv[$k2])) {
						if ($v2 != '') {
							$sobj->$conv[$k2] = $v2;
						}
					}
				}
				$obj->addSubmenu($sobj);
			}
		}
		return $obj;
	}
}

?>
