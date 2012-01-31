<?php
// Colors theme
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// PNG Icons By: Alessandro Rei; License:  GPL; www.deviantdark.com
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
// Convert a menu into our theme-specific format
function getMenuAsCustomList($menu) {
		// Create a inert menu - to use as a label
		$tmp=new WT_Menu(strip_tags($menu->label), '');
		// Insert the label into the submenu
		array_unshift($menu->submenus, $tmp);
		// Neutralise the top-level menu
		$menu->label='';
		$menu->onclick='';
		$menu->iconclass='';
		return $menu->getMenuAsList();
}

//-- print color theme sub type change dropdown box
function color_theme_dropdown() {
	global $COLOR_THEME_LIST;
	
	$menu=new WT_Menu(/* I18N: A colour scheme */ WT_I18N::translate('Palette'), '#', 'menu-color');
	$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', 'icon_small_theme');
	uasort($COLOR_THEME_LIST, 'utf8_strcasecmp');
	foreach ($COLOR_THEME_LIST as $colorChoice=>$colorName) {
		$submenu=new WT_Menu($colorName, get_query_url(array('themecolor'=>$colorChoice)), 'menu-color-'.$colorChoice);
		$menu->addSubMenu($submenu);
	}
	return $menu->getMenuAsList();
}

/**
 *  Define the default palette to be used.  Set $subColor
 *  to one of the collowing values to determine the default:
 *
 */

$COLOR_THEME_LIST=array(
	'aquamarine'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Aqua Marine'),
	'ash'             => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Ash'),
	'belgianchocolate'=> /* I18N: The name of a colour-scheme */ WT_I18N::translate('Belgian Chocolate'),
	'bluelagoon'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Blue Lagoon'),
	'bluemarine'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Blue Marine'),
	'coffeeandcream'  => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Coffee and Cream'),
	'coldday'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Cold Day'),
	'greenbeam'       => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Green Beam'),
	'mediterranio'    => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Mediterranio'),
	'mercury'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Mercury'),
	'nocturnal'       => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Nocturnal'),
	'olivia'          => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Olivia'),
	'pinkplastic'     => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Pink Plastic'),
	'shinytomato'     => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Shiny Tomato'),
	'tealtop'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Teal Top'),
);

// If we've selected a new palette, and we are logged in, set this value as a default.
if (isset($_GET['themecolor']) && array_key_exists($_GET['themecolor'], $COLOR_THEME_LIST)) {
	// Request to change color
	$subColor=$_GET['themecolor'];
	if (WT_USER_ID) {
		set_user_setting(WT_USER_ID, 'themecolor', $subColor);
		if (WT_USER_IS_ADMIN) {
			set_site_setting('DEFAULT_COLOR_PALETTE', $subColor);
		}
	}
	unset($_GET['themecolor']);
	// Rember that we have selected a value
	$WT_SESSION->subColor=$subColor;
}
// If we are logged in, use our preference
$subColor=null;
if (WT_USER_ID) {
	$subColor=get_user_setting(WT_USER_ID, 'themecolor');
}
// If not logged in or no preference, use one we selected earlier in the session?
if (!$subColor) {
	$subColor=$WT_SESSION->subColor;
}
// We haven't selected one this session?  Use the site default
if (!$subColor) {
	$subColor=get_site_setting('DEFAULT_COLOR_PALETTE','ash');
}
// Make sure our selected palette actually exists
if (!array_key_exists($subColor, $COLOR_THEME_LIST)) {
	$subColor='ash';
}

$theme_name       = "colors"; // need double quotes, as file is scanned/parsed by script
$footerfile       = WT_THEME_DIR . 'footer.php';
$headerfile       = WT_THEME_DIR . 'header.php';
$modules          = WT_THEME_URL . 'modules.css';
$print_stylesheet = WT_THEME_URL . 'print.css';
$stylesheet       = WT_THEME_URL . 'css/' . $subColor . '.css';
$WT_MENU_LOCATION = 'top';
$WT_USE_HELPIMG   = true;

$WT_IMAGES=array(
	'add'=>WT_THEME_URL.'images/add.png',
	'admin'=>WT_THEME_URL.'images/admin.png',
	'ancestry'=>WT_THEME_URL.'images/ancestry.png',
	'calendar'=>WT_THEME_URL.'images/calendar.png',
	'center'=>WT_THEME_URL.'images/center.png',
	'cfamily'=>WT_THEME_URL.'images/cfamily.png',
	'charts'=>WT_THEME_URL.'images/charts.png',
	'childless'=>WT_THEME_URL.'images/childless.png',
	'children'=>WT_THEME_URL.'images/children.png',
	'clippings'=>WT_THEME_URL.'images/clippings.png',
	'darrow'=>WT_THEME_URL.'images/darrow.png',
	'darrow2'=>WT_THEME_URL.'images/darrow2.png',
	'ddarrow'=>WT_THEME_URL.'images/ddarrow.png',
	'default_image_F'=>WT_THEME_URL.'images/silhouette_female.png',
	'default_image_M'=>WT_THEME_URL.'images/silhouette_male.png',
	'default_image_U'=>WT_THEME_URL.'images/silhouette_unknown.png',
	'descendant'=>WT_THEME_URL.'images/descendancy.png',
	'dline'=>WT_THEME_URL.'images/dline.png',
	'dline2'=>WT_THEME_URL.'images/dline2.png',
	'edit_fam'=>WT_THEME_URL.'images/edit_fam.png',
	'edit_indi'=>WT_THEME_URL.'images/edit_indi.png',
	'edit_media'=>WT_THEME_URL.'images/edit_media.png',
	'edit_note'=>WT_THEME_URL.'images/edit_note.png',
	'edit_repo'=>WT_THEME_URL.'images/edit_repo.png',
	'edit_sour'=>WT_THEME_URL.'images/edit_sour.png',
	'fam-list'=>WT_THEME_URL.'images/sfamily.png',
	'fambook'=>WT_THEME_URL.'images/fambook.png',
	'fanchart'=>WT_THEME_URL.'images/fanchart.png',
	'favorites'=>WT_THEME_URL.'images/favorites.png',
	'gedcom'=>WT_THEME_URL.'images/gedcom.png',
	'help'=>WT_THEME_URL.'images/help.png',
	'hline'=>WT_THEME_URL.'images/hline.png',
	'home'=>WT_THEME_URL.'images/home.png',
	'hourglass'=>WT_THEME_URL.'images/hourglass.png',
	'indis'=>WT_THEME_URL.'images/indis.png',
	'indi-list'=>WT_THEME_URL.'images/indis.png',
	'itree'=>WT_THEME_URL.'images/itree.png',
	'larrow'=>WT_THEME_URL.'images/larrow.png',
	'larrow2'=>WT_THEME_URL.'images/larrow2.png',
	'ldarrow'=>WT_THEME_URL.'images/ldarrow.png',
	'lists'=>WT_THEME_URL.'images/lists.png',

	// - lifespan chart arrows
	'lsdnarrow'=>WT_THEME_URL.'images/lifespan-down.png',
	'lsltarrow'=>WT_THEME_URL.'images/lifespan-left.png',
	'lsrtarrow'=>WT_THEME_URL.'images/lifespan-right.png',
	'lsuparrow'=>WT_THEME_URL.'images/lifespan-up.png',

	'media'=>WT_THEME_URL.'images/media.png',
	'media-list'=>WT_THEME_URL.'images/media.png',
	'menu_help'=>WT_THEME_URL.'images/menu_help.png',
	'menu_media'=>WT_THEME_URL.'images/menu_media.png',
	'menu_note'=>WT_THEME_URL.'images/menu_note.png',
	'menu_repository'=>WT_THEME_URL.'images/menu_repository.png',
	'menu_source'=>WT_THEME_URL.'images/menu_source.png',
	'minus'=>WT_THEME_URL.'images/minus.png',
	'mypage'=>WT_THEME_URL.'images/mypage.png',
	'note'=>WT_THEME_URL.'images/notes.png',
	'note-list'=>WT_THEME_URL.'images/notes.png',
	'patriarch'=>WT_THEME_URL.'images/patriarch.png',
	'pedigree'=>WT_THEME_URL.'images/pedigree.png',
	'place'=>WT_THEME_URL.'images/place.png',
	'plus'=>WT_THEME_URL.'images/plus.png',
	'rarrow'=>WT_THEME_URL.'images/rarrow.png',
	'rarrow2'=>WT_THEME_URL.'images/rarrow2.png',
	'rdarrow'=>WT_THEME_URL.'images/rdarrow.png',
	'relationship'=>WT_THEME_URL.'images/relationship.png',
	'reminder'=>WT_THEME_URL.'images/reminder.png',
	'remove'=>WT_THEME_URL.'images/delete.png',
	'reorder'=>WT_THEME_URL.'images/reorder_images.png',
	'reports'=>WT_THEME_URL.'images/report.png',
	'repository'=>WT_THEME_URL.'images/repository.png',
	'repo-list'=>WT_THEME_URL.'images/repository.png',
	'rings'=>WT_THEME_URL.'images/rings.png',
	'search'=>WT_THEME_URL.'images/search.png',
	'selected'=>WT_THEME_URL.'images/selected.png',
	'sex_f_15x15'=>WT_THEME_URL.'images/sex_f_15x15.png',
	'sex_f_9x9'=>WT_THEME_URL.'images/sex_f_9x9.png',
	'sex_m_15x15'=>WT_THEME_URL.'images/sex_m_15x15.png',
	'sex_m_9x9'=>WT_THEME_URL.'images/sex_m_9x9.png',
	'sex_u_15x15'=>WT_THEME_URL.'images/sex_u_15x15.png',
	'sex_u_9x9'=>WT_THEME_URL.'images/sex_u_9x9.png',
	'sfamily'=>WT_THEME_URL.'images/sfamily.png',
	'source'=>WT_THEME_URL.'images/source.png',
	'source-list'=>WT_THEME_URL.'images/source.png',
	'spacer'=>WT_THEME_URL.'images/spacer.png',
	'statistic'=>WT_THEME_URL.'images/statistic.png',
	'stop'=>WT_THEME_URL.'images/stop.png',
	'target'=>WT_THEME_URL.'images/buttons/target.png',
	'timeline'=>WT_THEME_URL.'images/timeline.png',
	'tree'=>WT_THEME_URL.'images/gedcom.png',
	'uarrow'=>WT_THEME_URL.'images/uarrow.png',
	'uarrow2'=>WT_THEME_URL.'images/uarrow2.png',
	'udarrow'=>WT_THEME_URL.'images/udarrow.png',
	'vline'=>WT_THEME_URL.'images/vline.png',
	'warning'=>WT_THEME_URL.'images/warning.png',
	'webtrees'=>WT_THEME_URL.'images/webtrees.png',
	'wiki'=>WT_THEME_URL.'images/wiki.png',
	'zoomin'=>WT_THEME_URL.'images/zoomin.png',
	'zoomout'=>WT_THEME_URL.'images/zoomout.png',

	//- buttons for data entry pages
//- buttons for data entry pages
	'button_addmedia'=>WT_THEME_URL.'images/buttons/addmedia.png',
	'button_addnote'=>WT_THEME_URL.'images/buttons/addnote.png',
	'button_addrepository'=>WT_THEME_URL.'images/buttons/addrepository.png',
	'button_addsource'=>WT_THEME_URL.'images/buttons/addsource.png',
	'button_calendar'=>WT_THEME_URL.'images/buttons/calendar.png',
	'button_family'=>WT_THEME_URL.'images/buttons/family.png',
	'button_find_facts'=>WT_THEME_URL.'images/buttons/find_facts.png',
	'button_head'=>WT_THEME_URL.'images/buttons/head.png',
	'button_indi'=>WT_THEME_URL.'images/buttons/indi.png',
	'button_keyboard'=>WT_THEME_URL.'images/buttons/keyboard.png',
	'button_media'=>WT_THEME_URL.'images/buttons/media.png',
	'button_note'=>WT_THEME_URL.'images/buttons/note.png',
	'button_place'=>WT_THEME_URL.'images	/buttons/place.png',
	'button_repository'=>WT_THEME_URL.'images/buttons/repository.png',
	'button_source'=>WT_THEME_URL.'images/buttons/source.png', 

	// media images
	'media_audio'=>WT_THEME_URL.'images/media/audio.png',
	'media_doc'=>WT_THEME_URL.'images/media/doc.png',
	'media_flash'=>WT_THEME_URL.'images/media/flash.png',
	'media_flashrem'=>WT_THEME_URL.'images/media/flashrem.png',
	'media_ged'=>WT_THEME_URL.'images/media/ged.png',
	'media_globe'=>WT_THEME_URL.'images/media/globe.png',
	'media_html'=>WT_THEME_URL.'images/media/www.png',
	'media_picasa'=>WT_THEME_URL.'images/media/picasa.png',
	'media_pdf'=>WT_THEME_URL.'images/media/pdf.png',
	'media_tex'=>WT_THEME_URL.'images/media/tex.png',
	'media_wmv'=>WT_THEME_URL.'images/media/wmv.png',
	'media_wmvrem'=>WT_THEME_URL.'images/media/wmvrem.png', 
);

//-- Variables for the Fan chart
$fanChart = array(
	'font' => WT_ROOT.'includes/fonts/DejaVuSans.ttf',
	'size' => '7px',
	'color' => '#000000',
	'bgColor' => '#eeeeee',
	'bgMColor' => '#b1cff0',
	'bgFColor' => '#e9daf1'
);

//-- This section defines variables for the charts
$bwidth = 250; // -- width of boxes on all person-box based charts
$bheight = 80; // -- height of boxes on all person-box based chart
$baseyoffset = 10; // -- position the timeline chart relative to the top of the page
$basexoffset = 10; // -- position the pedigree and timeline charts relative to the left of the page
$bxspacing = 4; // -- horizontal spacing between boxes on the pedigree chart
$byspacing = 5; // -- vertical spacing between boxes on the pedigree chart
$brborder = 1; // -- pedigree chart box right border thickness 

//-- Other settings that should not be touched
$Dbxspacing = 5; // -- position vertical line between boxes in relationship chart
$Dbyspacing = 10; // -- position vertical spacing between boxes in relationship chart
$Dbwidth = 240; // -- horizontal spacing between boxes in all charts
$Dbheight = 78; // -- horizontal spacing between boxes in all charts
$Dindent = 15; // -- width to indent ancestry and descendancy charts boxes
$Darrowwidth = 300; // -- not used that I can see ***

// -- Dimensions for compact version of chart displays
$cbwidth=225;
$cbheight=50;

// --  The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = "440";
$WT_STATS_S_CHART_Y = "125";
$WT_STATS_L_CHART_X = "900";
// --  For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = "440";
$WT_STATS_MAP_Y = "220";

$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "95b8e0";
$WT_STATS_CHART_COLOR3 = "c8e7ff";
