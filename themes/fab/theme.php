<?php
// FAB theme
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$theme_name="F.A.B."; // need double quotes, as file is scanned/parsed by script
$stylesheet=WT_THEME_URL.'style.css';
$headerfile=WT_THEME_DIR.'header.php';
$footerfile=WT_THEME_DIR.'footer.php';
$WT_USE_HELPIMG=false;
$WT_MENU_LOCATION='top';

$WT_IMAGES=array(
	'add'                 =>WT_THEME_URL.'images/add.gif',
	'admin'               =>WT_THEME_URL.'images/admin.gif',
	'ancestry'            =>WT_THEME_URL.'images/ancestry.gif',
	'button_addmedia'     =>WT_THEME_URL.'images/buttons/addmedia.gif',
	'button_addnote'      =>WT_THEME_URL.'images/buttons/addnote.gif',
	'button_addrepository'=>WT_THEME_URL.'images/buttons/addrepository.gif',
	'button_addsource'    =>WT_THEME_URL.'images/buttons/addsource.gif',
	'button_calendar'     =>WT_THEME_URL.'images/buttons/calendar.gif',
	'button_family'       =>WT_THEME_URL.'images/buttons/family.gif',
	'button_find_facts'   =>WT_THEME_URL.'images/buttons/find_facts.png',
	'button_head'         =>WT_THEME_URL.'images/buttons/head.gif',
	'button_indi'         =>WT_THEME_URL.'images/buttons/indi.gif',
	'button_keyboard'     =>WT_THEME_URL.'images/buttons/keyboard.gif',
	'button_media'        =>WT_THEME_URL.'images/buttons/media.gif',
	'button_note'         =>WT_THEME_URL.'images/buttons/note.gif',
	'button_place'        =>WT_THEME_URL.'images/buttons/place.gif',
	'button_repository'   =>WT_THEME_URL.'images/buttons/repository.gif',
	'button_source'       =>WT_THEME_URL.'images/buttons/source.gif',
	'calendar'            =>WT_THEME_URL.'images/calendar.gif',
	'center'              =>WT_THEME_URL.'images/center.gif',
	'cfamily'             =>WT_THEME_URL.'images/cfamily.gif',
	'childless'           =>WT_THEME_URL.'images/childless.gif',
	'children'            =>WT_THEME_URL.'images/children.gif',
	'clippings'           =>WT_THEME_URL.'images/clippings.gif',
	'darrow'              =>WT_THEME_URL.'images/darrow.gif',
	'darrow2'             =>WT_THEME_URL.'images/darrow2.gif',
	'ddarrow'             =>WT_THEME_URL.'images/ddarrow.gif',
	'default_image_F'     =>WT_THEME_URL.'images/silhouette_female.png',
	'default_image_M'     =>WT_THEME_URL.'images/silhouette_male.png',
	'default_image_U'     =>WT_THEME_URL.'images/silhouette_unknown.png',
	'descendant'          =>WT_THEME_URL.'images/descendancy.gif',
	'dline'               =>WT_THEME_URL.'images/dline.gif',
	'dline2'              =>WT_THEME_URL.'images/dline2.gif',
	'edit_fam'            =>WT_THEME_URL.'images/edit_fam.gif',
	'edit_indi'           =>WT_THEME_URL.'images/edit_indi.gif',
	'edit_media'          =>WT_THEME_URL.'images/edit_indi.gif',
	'edit_note'           =>WT_THEME_URL.'images/edit_indi.gif',
	'edit_repo'           =>WT_THEME_URL.'images/edit_repo.gif',
	'edit_sour'           =>WT_THEME_URL.'images/edit_sour.gif',
	'fambook'             =>WT_THEME_URL.'images/fambook.gif',
	'fanchart'            =>WT_THEME_URL.'images/fanchart.gif',
	'favorites'           =>WT_THEME_URL.'images/favorites.gif',
	'fscreen'             =>WT_THEME_URL.'images/fscreen.gif',
	'gedcom'              =>WT_THEME_URL.'images/gedcom.gif',
	'help'                =>WT_THEME_URL.'images/help.gif',
	'hline'               =>WT_THEME_URL.'images/hline.gif',
	'hourglass'           =>WT_THEME_URL.'images/hourglass.gif',
	'indis'               =>WT_THEME_URL.'images/indis.gif',
	'itree'               =>WT_THEME_URL.'images/itree.gif',
	'larrow'              =>WT_THEME_URL.'images/larrow.gif',
	'larrow2'             =>WT_THEME_URL.'images/larrow2.gif',
	'ldarrow'             =>WT_THEME_URL.'images/ldarrow.gif',
	'lsdnarrow'           =>WT_THEME_URL.'images/lifespan-down.png',
	'lsltarrow'           =>WT_THEME_URL.'images/lifespan-left.png',
	'lsrtarrow'           =>WT_THEME_URL.'images/lifespan-right.png',
	'lsuparrow'           =>WT_THEME_URL.'images/lifespan-up.png',
	'media'               =>WT_THEME_URL.'images/media.gif',
	'media_audio'         =>WT_THEME_URL.'images/media/audio.png',
	'media_doc'           =>WT_THEME_URL.'images/media/doc.gif',
	'media_flash'         =>WT_THEME_URL.'images/media/flash.png',
	'media_flashrem'      =>WT_THEME_URL.'images/media/flashrem.png',
	'media_ged'           =>WT_THEME_URL.'images/media/ged.gif',
	'media_globe'         =>WT_THEME_URL.'images/media/globe.png',
	'media_html'          =>WT_THEME_URL.'images/media/html.gif',
	'media_pdf'           =>WT_THEME_URL.'images/media/pdf.gif',
	'media_picasa'        =>WT_THEME_URL.'images/media/picasa.png',
	'media_tex'           =>WT_THEME_URL.'images/media/tex.gif',
	'media_wmv'           =>WT_THEME_URL.'images/media/wmv.png',
	'media_wmvrem'        =>WT_THEME_URL.'images/media/wmvrem.png',
	'menu_help'           =>WT_THEME_URL.'images/help.gif',
	'menu_media'          =>WT_THEME_URL.'images/media.gif',
	'menu_repository'     =>WT_THEME_URL.'images/repository.gif',
	'menu_source'         =>WT_THEME_URL.'images/source.gif',
	'minus'               =>WT_THEME_URL.'images/minus.gif',
	'mypage'              =>WT_THEME_URL.'images/mypage.gif',
	'note'                =>WT_THEME_URL.'images/notes.png',
	'notes'               =>WT_THEME_URL.'images/notes.png',
	'patriarch'           =>WT_THEME_URL.'images/patriarch.gif',
	'pedigree'            =>WT_THEME_URL.'images/pedigree.gif',
	'pin-in'              =>WT_THEME_URL.'images/pin-in.png',
	'pin-out'             =>WT_THEME_URL.'images/pin-out.png',
	'place'               =>WT_THEME_URL.'images/place.gif',
	'plus'                =>WT_THEME_URL.'images/plus.gif',
	'rarrow'              =>WT_THEME_URL.'images/rarrow.gif',
	'rarrow2'             =>WT_THEME_URL.'images/rarrow2.gif',
	'rdarrow'             =>WT_THEME_URL.'images/rdarrow.gif',
	'relationship'        =>WT_THEME_URL.'images/relationship.gif',
	'reminder'            =>WT_THEME_URL.'images/reminder.gif',
	'remove'              =>WT_THEME_URL.'images/remove.gif',
	'reports'             =>WT_THEME_URL.'images/reports.gif',
	'repository'          =>WT_THEME_URL.'images/repository.gif',
	'rings'               =>WT_THEME_URL.'images/rings.gif',
	'search'              =>WT_THEME_URL.'images/search.gif',
	'selected'            =>WT_THEME_URL.'images/selected.png',
	'sex_f_15x15'         =>WT_THEME_URL.'images/sex_f_15x15.gif',
	'sex_f_9x9'           =>WT_THEME_URL.'images/sex_f_9x9.gif',
	'sex_m_15x15'         =>WT_THEME_URL.'images/sex_m_15x15.gif',
	'sex_m_9x9'           =>WT_THEME_URL.'images/sex_m_9x9.gif',
	'sex_u_15x15'         =>WT_THEME_URL.'images/sex_u_15x15.gif',
	'sex_u_9x9'           =>WT_THEME_URL.'images/sex_u_9x9.gif',
	'sfamily'             =>WT_THEME_URL.'images/sfamily.gif',
	'slide_close'         =>WT_THEME_URL.'images/close.png',
	'slide_open'          =>WT_THEME_URL.'images/open.png',
	'source'              =>WT_THEME_URL.'images/source.gif',
	'spacer'              =>WT_THEME_URL.'images/spacer.gif',
	'statistic'           =>WT_THEME_URL.'images/statistic.gif',
	'stop'                =>WT_THEME_URL.'images/stop.gif',
	'target'              =>WT_THEME_URL.'images/buttons/target.gif',
	'timeline'            =>WT_THEME_URL.'images/timeline.gif',
	'tree'                =>WT_THEME_URL.'images/gedcom.gif',
	'uarrow'              =>WT_THEME_URL.'images/uarrow.gif',
	'uarrow2'             =>WT_THEME_URL.'images/uarrow2.gif',
	'udarrow'             =>WT_THEME_URL.'images/udarrow.gif',
	'vline'               =>WT_THEME_URL.'images/vline.gif',
	'warning'             =>WT_THEME_URL.'images/warning.gif',
	'webtrees'            =>WT_THEME_URL.'images/webtrees.png',
	'zoomin'              =>WT_THEME_URL.'images/zoomin.gif',
	'zoomout'             =>WT_THEME_URL.'images/zoomout.gif',
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
