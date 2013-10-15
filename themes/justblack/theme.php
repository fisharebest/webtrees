<?php
// JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
//
// $Id: theme.php 2013-09-15 JustCarmen $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$theme_name = "JustBlack"; // need double quotes, as file is scanned/parsed by script

// A version number in the path prevents browser-cache problems during upgrade
define('WT_CSS_URL', WT_THEME_URL . 'css-1.5.0/');

// theme specific files.
define('WT_THEME_JUSTBLACK', WT_THEME_URL . 'theme/'); 

require_once(WT_ROOT.WT_THEME_JUSTBLACK . 'functions.php');

$headerfile       	= WT_THEME_DIR.'header.php';
$footerfile       	= WT_THEME_DIR.'footer.php';

//-- variables for image names
$WT_IMAGES=array(
	// used to draw charts
	'dline'          =>WT_THEME_URL.'css/images/dline.png',
	'dline2'         =>WT_THEME_URL.'css/images/dline2.png',
	'hline'          =>WT_THEME_URL.'css/images/hline.png',
	'spacer'         =>WT_THEME_URL.'css/images/spacer.png',
	'vline'          =>WT_THEME_URL.'css/images/vline.png',

	// used in button images and javascript
	'add'            =>WT_THEME_URL.'css/images/icons/add.png',
	'button_family'  =>WT_THEME_URL.'css/images/buttons/sfamily.png',
	'minus'          =>WT_THEME_URL.'css/images/icons/minus.png',
	'plus'           =>WT_THEME_URL.'css/images/icons/plus.png',
	'remove'         =>WT_THEME_URL.'css/images/icons/remove.png',
	'search'         =>WT_THEME_URL.'css/images/buttons/search.png',

	// need different sizes before moving to CSS
	
	'default_image_M'=>WT_THEME_URL.'css/images/silhouette_male.png',
	'default_image_F'=>WT_THEME_URL.'css/images/silhouette_female.png',
	'default_image_U'=>WT_THEME_URL.'css/images/silhouette_unknown.png',

	// need to replace with a system based on mime-types
	'media'          =>WT_THEME_URL.'css/images/media/image-jpeg.png',
	'media_audio'    =>WT_THEME_URL.'css/images/media/audio-x-generic.png',
	'media_doc'      =>WT_THEME_URL.'css/images/media/gnome-mime-application-msword.png',
	'media_flash'    =>WT_THEME_URL.'css/images/media/gnome-mime-application-x-shockwave-flash.png',
	'media_flashrem' =>WT_THEME_URL.'css/images/media/gnome-mime-application-x-shockwave-flash.png',
	'media_ged'      =>WT_THEME_URL.'css/images/media/text-x-gedcom.png',
	'media_globe'    =>WT_THEME_URL.'css/images/media/text-html.png',
	'media_html'     =>WT_THEME_URL.'css/images/media/text-html.png',
	'media_pdf'      =>WT_THEME_URL.'css/images/media/gnome-mime-application-pdf.png',
	'media_picasa'   =>WT_THEME_URL.'css/images/media/image-x-generic.png',
	'media_tex'      =>WT_THEME_URL.'css/images/media/text-x-tex.png',
	'media_wmv'      =>WT_THEME_URL.'css/images/media/video-x-ms-wmv.png',
	'media_wmvrem'   =>WT_THEME_URL.'css/images/media/video-x-ms-wmv.png',
);

//-- Variables for the Fan chart
$fanChart = array(
	'font'			=> WT_ROOT.'includes/fonts/DejaVuSans.ttf',
	'size'				=> '9px',
	'color' => '#2e2e2e',
	'bgColor' => '#FFFFDE',
	'bgMColor'	=> '#FF8C00',
	'bgFColor'	=> '#FFEEB0'
);

//-- pedigree chart variables
$bwidth = 250;			// width of boxes on pedigree chart
$bheight = 80;			// height of boxes on pedigree chart
$baseyoffset = 10;		// position the entire pedigree tree relative to the top of the page
$basexoffset = 10;		// position the entire pedigree tree relative to the left of the page
$bxspacing = 5;			// horizontal spacing between boxes on the pedigree chart
$byspacing = 40;		// vertical spacing between boxes on the pedigree chart
$brborder = 1;			// box right border thickness
$linewidth=1;			// width of joining lines
$shadowcolor="#171717";	// shadow color for joining lines
$shadowblur=12;			// shadow blur for joining lines
$shadowoffsetX=2;		// shadowOffsetX for joining lines
$shadowoffsetY=2;		// shadowOffsetY for joining lines

// descendancy - relationship chart variables
$Dbaseyoffset = 20;		// position the entire descendancy tree relative to the top of the page
$Dbasexoffset = 20;		// position the entire descendancy tree relative to the left of the page
$Dbxspacing = 5; 		// horizontal spacing between boxes
$Dbyspacing = 10; 		// vertical spacing between boxes
$Dbwidth = 260; 		// width of DIV layer boxes
$Dbheight = 80; 		// height of DIV layer boxes
$Dindent = 15; 			// width to indent descendancy boxes
$Darrowwidth = 30; 		// additional width to include for the up arrows

// -- Dimensions for compact version of chart displays
$cbwidth=240;
$cbheight=55;

// --  The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = "440";
$WT_STATS_S_CHART_Y = "125";
$WT_STATS_L_CHART_X = "900";
// --  For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = "440";
$WT_STATS_MAP_Y = "220";

$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "ff8c00";
$WT_STATS_CHART_COLOR3 = "ffeeb0";

?>