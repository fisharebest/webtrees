<?php
// Clouds theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView Cloudy theme
// Original author w.a. bastein http://genealogy.bastein.biz
// Copyright (C) 2010 PGV Development Team.
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

// Convert a menu into our theme-specific format
function getMenuAsCustomList($menu) {
		// Create an inert menu - to use as a label
		$tmp=new WT_Menu(strip_tags($menu->label), '');
		// Insert the label into the submenu
		if (is_array($menu->submenus)) {
			array_unshift($menu->submenus, $tmp);
		} else {
			$menu->addSubmenu($tmp);
		}
		// Neutralise the top-level menu
		$menu->label='';
		$menu->onclick='';
		$menu->iconclass='';
		return $menu->getMenuAsList();
}

// Theme name - this needs double quotes, as file is scanned/parsed by script
$theme_name = "clouds"; /* I18N: Name of a theme. */ WT_I18N::translate('clouds');

// A version number in the path prevents browser-cache problems during upgrade
define('WT_CSS_URL', WT_THEME_URL . 'css-1.6.0/');

$headerfile = WT_THEME_DIR . 'header.php';
$footerfile = WT_THEME_DIR . 'footer.php';

$WT_IMAGES=array(
	// used to draw charts
	'dline'           => WT_CSS_URL .'images/dline.png',
	'dline2'          => WT_CSS_URL .'images/dline2.png',
	'hline'           => WT_CSS_URL .'images/hline.png',
	'spacer'          => WT_CSS_URL .'images/spacer.png',
	'vline'           => WT_CSS_URL .'images/vline.png',

	// used in button images and javascript
	'add'             => WT_CSS_URL .'images/add.png',
	'button_family'   => WT_CSS_URL .'images/buttons/family.png',
	'minus'           => WT_CSS_URL .'images/minus.png',
	'plus'            => WT_CSS_URL .'images/plus.png',
	'remove'          => WT_CSS_URL .'images/delete.png',
	'search'          => WT_CSS_URL .'images/go.png',

	// need different sizes before moving to CSS
	'default_image_F' => WT_CSS_URL .'images/silhouette_female.png',
	'default_image_M' => WT_CSS_URL .'images/silhouette_male.png',
	'default_image_U' => WT_CSS_URL .'images/silhouette_unknown.png',
);

// Variables for the Fan chart
$fanChart = array(
	'font'     => WT_ROOT . 'includes/fonts/DejaVuSans.ttf',
	'size'     => 7,
	'color'    => '#000000',
	'bgColor'  => '#eeeeee',
	'bgMColor' => '#b1cff0',
	'bgFColor' => '#e9daf1'
);

// This section defines variables for the charts
$bwidth        = 250; // width of boxes on all person-box based charts
$bheight       = 80;  // height of boxes on all person-box based chart
$baseyoffset   = 10;  // position the timeline chart relative to the top of the page
$basexoffset   = 10;  // position the pedigree and timeline charts relative to the left of the page
$bxspacing     = 4;   // horizontal spacing between boxes on the pedigree chart
$byspacing     = 5;   // vertical spacing between boxes on the pedigree chart
$brborder      = 1;   // pedigree chart box right border thickness
$linewidth     = 1.5; // width of joining lines
$shadowcolor   = '';  // shadow color for joining lines
$shadowblur    = 0;   // shadow blur for joining lines
$shadowoffsetX = 0;   // shadowOffsetX for joining lines
$shadowoffsetY = 0;   // shadowOffsetY for joining lines

// Other settings that should not be touched
$Dbxspacing    = 5;   // position vertical line between boxes in relationship chart
$Dbyspacing    = 10;  // position vertical spacing between boxes in relationship chart
$Dbwidth       = 250; // horizontal spacing between boxes in all charts
$Dbheight      = 80;  // horizontal spacing between boxes in all charts
$Dindent       = 15;  // width to indent ancestry and descendancy charts boxes
$Darrowwidth   = 300; // not used that I can see ***

// -- Dimensions for compact version of chart displays
$cbwidth  = 240;
$cbheight = 50;

// The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = 440;
$WT_STATS_S_CHART_Y = 125;
$WT_STATS_L_CHART_X = 900;
// For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = 440;
$WT_STATS_MAP_Y = 220;
$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "95b8e0";
$WT_STATS_CHART_COLOR3 = "c8e7ff";
