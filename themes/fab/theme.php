<?php
// FAB theme
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
//
// Derived from PhpGedView
// Copyright (c) 2010 Greg Roach
//
// Based on standard theme, which is Copyright (C) 2002 to 2010  PGV Development Team.
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
// @package webtrees
// @subpackage Themes
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Remove non-standard tags.
$date_and_time=array();
unset ($level2_tags['CEME']);

$theme_name="F.A.B."; // need double quotes, as file is scanned/parsed by script
$stylesheet=WT_THEME_DIR.'style.css';
$headerfile=WT_THEME_DIR.'header.php';
$footerfile=WT_THEME_DIR.'footer.php';
$WT_USE_HELPIMG=false;
$WT_MENU_LOCATION='top';

$WT_IMAGES=array(
	// use the common images for sex icons
	'sex_f_15x15'=>'images/sex_f_15x15.gif',
	'sex_f_9x9'=>'images/sex_f_9x9.gif',
	'sex_m_15x15'=>'images/sex_m_15x15.gif',
	'sex_m_9x9'=>'images/sex_m_9x9.gif',
	'sex_u_15x15'=>'images/sex_u_15x15.gif',
	'sex_u_9x9'=>'images/sex_u_9x9.gif',
	// main icons
	'admin'=>WT_THEME_DIR.'images/admin.gif',
	'ancestry'=>WT_THEME_DIR.'images/ancestry.gif',
	'calendar'=>WT_THEME_DIR.'images/calendar.gif',
	'cfamily'=>WT_THEME_DIR.'images/cfamily.gif',
	'clippings'=>WT_THEME_DIR.'images/clippings.gif',
	'descendant'=>WT_THEME_DIR.'images/descendancy.gif',
	'edit_fam'=>WT_THEME_DIR.'images/edit_fam.gif',
	'edit_indi'=>WT_THEME_DIR.'images/edit_indi.gif',
	'edit_media'=>WT_THEME_DIR.'images/edit_indi.gif',
	'edit_note'=>WT_THEME_DIR.'images/edit_indi.gif',
	'edit_repo'=>WT_THEME_DIR.'images/edit_repo.gif',
	'edit_sour'=>WT_THEME_DIR.'images/edit_sour.gif',
	'fambook'=>WT_THEME_DIR.'images/fambook.gif',
	'fanchart'=>WT_THEME_DIR.'images/fanchart.gif',
	'favorites'=>WT_THEME_DIR.'images/favorites.gif',
	'gedcom'=>WT_THEME_DIR.'images/gedcom.gif',
	'help'=>WT_THEME_DIR.'images/help.gif',
	'hourglass'=>WT_THEME_DIR.'images/hourglass.gif',
	'indis'=>WT_THEME_DIR.'images/indis.gif',
	'media'=>WT_THEME_DIR.'images/media.gif',
	'menu_gallery'=>WT_THEME_DIR.'images/menu_gallery.gif',
	'menu_help'=>WT_THEME_DIR.'images/help.gif',
	'menu_media'=>WT_THEME_DIR.'images/media.gif',
	'menu_punbb'=>WT_THEME_DIR.'images/menu_punbb.gif',
	'menu_repository'=>WT_THEME_DIR.'images/repository.gif',
	'menu_research'=>WT_THEME_DIR.'images/menu_research.gif',
	'menu_source'=>WT_THEME_DIR.'images/source.gif',
	'mypage'=>WT_THEME_DIR.'images/mypage.gif',
	'notes'=>WT_THEME_DIR.'images/notes.png',
	'patriarch'=>WT_THEME_DIR.'images/patriarch.gif',
	'pedigree'=>WT_THEME_DIR.'images/pedigree.gif',
	'place'=>WT_THEME_DIR.'images/place.gif',
	'relationship'=>WT_THEME_DIR.'images/relationship.gif',
	'reports'=>WT_THEME_DIR.'images/reports.gif',
	'repository'=>WT_THEME_DIR.'images/repository.gif',
	'search'=>WT_THEME_DIR.'images/search.gif',
	'sfamily'=>WT_THEME_DIR.'images/sfamily.gif',
	'source'=>WT_THEME_DIR.'images/source.gif',
	'statistic'=>WT_THEME_DIR.'images/statistic.gif',
	'target'=>'images/buttons/target.gif',
	'timeline'=>WT_THEME_DIR.'images/timeline.gif',
	'tree'=>WT_THEME_DIR.'images/gedcom.gif',
	// buttons for data entry pages
	'button_addmedia'=>WT_THEME_DIR.'images/buttons/addmedia.gif',
	'button_addrepository'=>WT_THEME_DIR.'images/buttons/addrepository.gif',
	'button_addsource'=>WT_THEME_DIR.'images/buttons/addsource.gif',
	'button_addnote'=>WT_THEME_DIR.'images/buttons/addnote.gif',
	'button_calendar'=>WT_THEME_DIR.'images/buttons/calendar.gif',
	'button_family'=>WT_THEME_DIR.'images/buttons/family.gif',
	'button_indi'=>WT_THEME_DIR.'images/buttons/indi.gif',
	'button_keyboard'=>WT_THEME_DIR.'images/buttons/keyboard.gif',
	'button_media'=>WT_THEME_DIR.'images/buttons/media.gif',
	'button_place'=>WT_THEME_DIR.'images/buttons/place.gif',
	'button_repository'=>WT_THEME_DIR.'images/buttons/repository.gif',
	'button_source'=>WT_THEME_DIR.'images/buttons/source.gif',
	'button_note'=>WT_THEME_DIR.'images/buttons/note.gif',
	'button_head'=>WT_THEME_DIR.'images/buttons/head.gif',
	'button_find_facts'=>WT_THEME_DIR.'images/buttons/find_facts.png',
	// media images
	'media_audio'=>WT_THEME_DIR.'images/media/audio.png',
	'media_doc'=>WT_THEME_DIR.'images/media/doc.gif',
	'media_flash'=>WT_THEME_DIR.'images/media/flash.png',
	'media_flashrem'=>WT_THEME_DIR.'images/media/flashrem.png',
	'media_ged'=>WT_THEME_DIR.'images/media/ged.gif',
	'media_globe'=>WT_THEME_DIR.'images/media/globe.png',
	'media_html'=>WT_THEME_DIR.'images/media/html.gif',
	'media_picasa'=>WT_THEME_DIR.'images/media/picasa.png',
	'media_pdf'=>WT_THEME_DIR.'images/media/pdf.gif',
	'media_tex'=>WT_THEME_DIR.'images/media/tex.gif',
	'media_wmv'=>WT_THEME_DIR.'images/media/wmv.png',
	'media_wmvrem'=>WT_THEME_DIR.'images/media/wmvrem.png',
	// other images
	'add'=>WT_THEME_DIR.'images/add.gif',
	'children'=>WT_THEME_DIR.'images/children.gif',
	'darrow'=>WT_THEME_DIR.'images/darrow.gif',
	'darrow2'=>WT_THEME_DIR.'images/darrow2.gif',
	'ddarrow'=>WT_THEME_DIR.'images/ddarrow.gif',
	'default_image_F'=>WT_THEME_DIR.'images/silhouette_female.gif',
	'default_image_M'=>WT_THEME_DIR.'images/silhouette_male.gif',
	'default_image_U'=>WT_THEME_DIR.'images/silhouette_unknown.gif',
	'dline'=>WT_THEME_DIR.'images/dline.gif',
	'dline2'=>WT_THEME_DIR.'images/dline2.gif',
	'hline'=>WT_THEME_DIR.'images/hline.gif',
	'larrow'=>WT_THEME_DIR.'images/larrow.gif',
	'larrow2'=>WT_THEME_DIR.'images/larrow2.gif',
	'ldarrow'=>WT_THEME_DIR.'images/ldarrow.gif',
	'lsltarrow'=>WT_THEME_DIR.'images/lsltarrow.gif',
	'lsrtarrow'=>WT_THEME_DIR.'images/lsrtarrow.gif',
	'lsdnarrow'=>WT_THEME_DIR.'images/lsdnarrow.gif',
	'lsuparrow'=>WT_THEME_DIR.'images/lsuparrow.gif',
	'minus'=>WT_THEME_DIR.'images/minus.gif',
	'note'=>WT_THEME_DIR.'images/notes.png',
	'pin-in'=>WT_THEME_DIR.'images/pin-in.png',
	'pin-out'=>WT_THEME_DIR.'images/pin-out.png',
	'plus'=>WT_THEME_DIR.'images/plus.gif',
	'rarrow'=>WT_THEME_DIR.'images/rarrow.gif',
	'rarrow2'=>WT_THEME_DIR.'images/rarrow2.gif',
	'rdarrow'=>WT_THEME_DIR.'images/rdarrow.gif',
	'reminder'=>WT_THEME_DIR.'images/reminder.gif',
	'remove'=>WT_THEME_DIR.'images/remove.gif',
	'selected'=>WT_THEME_DIR.'images/selected.png',
	'slide_close'=>WT_THEME_DIR.'images/close.png',
	'slide_open'=>WT_THEME_DIR.'images/open.png',
	'spacer'=>WT_THEME_DIR.'images/spacer.gif',
	'stop'=>WT_THEME_DIR.'images/stop.gif',
	'uarrow'=>WT_THEME_DIR.'images/uarrow.gif',
	'uarrow2'=>WT_THEME_DIR.'images/uarrow2.gif',
	'uarrow3'=>WT_THEME_DIR.'images/uarrow3.gif',
	'udarrow'=>WT_THEME_DIR.'images/udarrow.gif',
	'vline'=>WT_THEME_DIR.'images/vline.gif',
	'webtrees'=>WT_THEME_DIR.'images/webtrees.png',
	'zoomin'=>WT_THEME_DIR.'images/zoomin.gif',
	'zoomout'=>WT_THEME_DIR.'images/zoomout.gif',
);

// Fan chart
$fanChart=array(
	'font'=>WT_ROOT.'includes/fonts/DejaVuSans.ttf',
	'size'=>'7px',
	'color'=>'#000000',
	'bgColor'=>'#eeeeee',
	'bgMColor'=>'#b1cff0',
	'bgFColor'=>'#e9daf1'
);

// variables for the pedigree chart
$bwidth=225;     // width of boxes on pedigree chart
$bheight=80;     // height of boxes on pedigree chart
$baseyoffset=10; // position the entire pedigree tree relative to the top of the page
$basexoffset=10; // position the entire pedigree tree relative to the left of the page
$bxspacing=0;    // horizontal spacing between boxes on the pedigree chart
$byspacing=5;    // vertical spacing between boxes on the pedigree chart
$brborder=1;     // box right border thickness

// variables for the descendancy chart
$Dbaseyoffset=0; // position the entire descendancy tree relative to the top of the page
$Dbasexoffset=0; // position the entire descendancy tree relative to the left of the page
$Dbxspacing=0;   // horizontal spacing between boxes
$Dbyspacing=1;   // vertical spacing between boxes
$Dbwidth=270;    // width of DIV layer boxes
$Dbheight=80;    // height of DIV layer boxes
$Dindent=15;     // width to indent descendancy boxes
$Darrowwidth=15; // additional width to include for the up arrows

$CHARTS_CLOSE_HTML=true; // should the charts, pedigree, descendacy, etc close the HTML on the page

// The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X=440;
$WT_STATS_S_CHART_Y=125;
$WT_STATS_L_CHART_X=900;

// For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X=440;
$WT_STATS_MAP_Y=220;

$WT_STATS_CHART_COLOR1='ffffff';
$WT_STATS_CHART_COLOR2='9ca3d4';
$WT_STATS_CHART_COLOR3='e5e6ef';

// Arrow symbol or icon for up-page links on Help pages
$UpArrow='<img src="'.WT_THEME_DIR.'images/uarrow3.gif" class="icon" border="0" alt="^" />';
