<?php
/**
 * Xenea theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Themes
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$theme_name = "xenea"; // need double quotes, as file is scanned/parsed by script
$stylesheet       = WT_THEME_DIR.'style.css';
$rtl_stylesheet   = WT_THEME_DIR.'style_rtl.css';
$print_stylesheet = WT_THEME_DIR.'print.css';
$headerfile       = WT_THEME_DIR.'header.php';
$footerfile       = WT_THEME_DIR.'footer.php';
$WT_USE_HELPIMG   = true;

//-- variables for image names
//- PGV main icons
$WT_IMAGES['admin'] = WT_THEME_DIR.'images/admin.png';
$WT_IMAGES['ancestry'] = WT_THEME_DIR.'images/ancestry.png';
$WT_IMAGES['calendar'] = WT_THEME_DIR.'images/calendar.png';
$WT_IMAGES['cfamily'] = WT_THEME_DIR.'images/cfamily.png';
$WT_IMAGES['charts'] = WT_THEME_DIR.'images/pedigree.png';
$WT_IMAGES['childless'] = WT_THEME_DIR.'images/childless.png';
$WT_IMAGES['clippings'] = WT_THEME_DIR.'images/clippings.png';
$WT_IMAGES['descendant'] = WT_THEME_DIR.'images/descendancy.png';
$WT_IMAGES['edit_fam'] = WT_THEME_DIR.'images/edit_fam.png';
$WT_IMAGES['edit_indi'] = WT_THEME_DIR.'images/edit_indi.png';
$WT_IMAGES['edit_media'] = WT_THEME_DIR.'images/edit_indi.png';
$WT_IMAGES['edit_note'] = WT_THEME_DIR.'images/edit_indi.png';
$WT_IMAGES['edit_repo'] = WT_THEME_DIR.'images/edit_repo.png';
$WT_IMAGES['edit_sour'] = WT_THEME_DIR.'images/edit_sour.png';
$WT_IMAGES['fambook'] = WT_THEME_DIR.'images/fambook.png';
$WT_IMAGES['fanchart'] = WT_THEME_DIR.'images/fanchart.png';
$WT_IMAGES['favorites'] = WT_THEME_DIR.'images/gedcom.png';
$WT_IMAGES['gedcom'] = WT_THEME_DIR.'images/gedcom.png';
$WT_IMAGES['help'] = WT_THEME_DIR.'images/help.png';
$WT_IMAGES['home'] = WT_THEME_DIR.'images/home.png';
$WT_IMAGES['hourglass'] = WT_THEME_DIR.'images/hourglass.png';
$WT_IMAGES['indis'] = WT_THEME_DIR.'images/indis.png';
$WT_IMAGES['lists'] = WT_THEME_DIR.'images/lists.png';
$WT_IMAGES['media'] = WT_THEME_DIR.'images/media.png';
$WT_IMAGES['menu_help'] = WT_THEME_DIR.'images/menu_help.png';
$WT_IMAGES['menu_media'] = WT_THEME_DIR.'images/menu_media.png';
$WT_IMAGES['menu_note'] = WT_THEME_DIR.'images/menu_note.png';
$WT_IMAGES['menu_repository'] = WT_THEME_DIR.'images/menu_repository.png';
$WT_IMAGES['menu_source'] = WT_THEME_DIR.'images/menu_source.png';
$WT_IMAGES['mypage'] = WT_THEME_DIR.'images/mypage.png';
$WT_IMAGES['notes'] = WT_THEME_DIR.'images/notes.png';
$WT_IMAGES['patriarch'] = WT_THEME_DIR.'images/patriarch.png';
$WT_IMAGES['pedigree'] = WT_THEME_DIR.'images/pedigree.png';
$WT_IMAGES['place'] = WT_THEME_DIR.'images/place.png';
$WT_IMAGES['relationship'] = WT_THEME_DIR.'images/relationship.png';
$WT_IMAGES['reports'] = WT_THEME_DIR.'images/report.png';
$WT_IMAGES['repository'] = WT_THEME_DIR.'images/repository.png';
$WT_IMAGES['rings'] = WT_THEME_DIR.'images/rings.png';
$WT_IMAGES['search'] = WT_THEME_DIR.'images/search.png';
$WT_IMAGES['selected'] = WT_THEME_DIR.'images/selected.png';
$WT_IMAGES['sex_m_9x9'] = WT_THEME_DIR.'images/sex_m_9x9.png';
$WT_IMAGES['sex_f_9x9'] = WT_THEME_DIR.'images/sex_f_9x9.png';
$WT_IMAGES['sex_u_9x9'] = WT_THEME_DIR.'images/sex_u_9x9.png';
$WT_IMAGES['sex_m_15x15'] = WT_THEME_DIR.'images/sex_m_15x15.png';
$WT_IMAGES['sex_f_15x15'] = WT_THEME_DIR.'images/sex_f_15x15.png';
$WT_IMAGES['sex_u_15x15'] = WT_THEME_DIR.'images/sex_u_15x15.png';
$WT_IMAGES['sfamily'] = WT_THEME_DIR.'images/sfamily.png';
$WT_IMAGES['source'] = WT_THEME_DIR.'images/source.png';
$WT_IMAGES['statistic'] = WT_THEME_DIR.'images/statistic.png';
$WT_IMAGES['target'] = WT_THEME_DIR.'images/buttons/target.png';
$WT_IMAGES['timeline'] = WT_THEME_DIR.'images/timeline.png';
$WT_IMAGES['tree'] = WT_THEME_DIR.'images/tree.png';
$WT_IMAGES['warning'] = WT_THEME_DIR.'images/warning.png';
$WT_IMAGES['wiki'] = WT_THEME_DIR.'images/w_22.png';

//- PGV buttons for data entry pages
$WT_IMAGES['button_addmedia'] = WT_THEME_DIR.'images/buttons/addmedia.png';
$WT_IMAGES['button_addrepository'] = WT_THEME_DIR.'images/buttons/addrepository.png';
$WT_IMAGES['button_addsource'] = WT_THEME_DIR.'images/buttons/addsource.png';
$WT_IMAGES['button_addnote'] = WT_THEME_DIR.'images/buttons/addnote.png';
$WT_IMAGES['button_calendar'] = WT_THEME_DIR.'images/buttons/calendar.png';
$WT_IMAGES['button_family'] = WT_THEME_DIR.'images/buttons/family.png';
$WT_IMAGES['button_indi'] = WT_THEME_DIR.'images/buttons/indi.png';
$WT_IMAGES['button_keyboard'] = WT_THEME_DIR.'images/buttons/keyboard.png';
$WT_IMAGES['button_media'] = WT_THEME_DIR.'images/buttons/media.png';
$WT_IMAGES['button_place'] = WT_THEME_DIR.'images/buttons/place.png';
$WT_IMAGES['button_repository'] = WT_THEME_DIR.'images/buttons/repository.png';
$WT_IMAGES['button_source'] = WT_THEME_DIR.'images/buttons/source.png';
$WT_IMAGES['button_note'] = WT_THEME_DIR.'images/buttons/note.png';
$WT_IMAGES['button_head'] = WT_THEME_DIR.'images/buttons/head.png';
$WT_IMAGES['button_find_facts'] = WT_THEME_DIR.'images/buttons/find_facts.png';

// Media images
$WT_IMAGES['media_audio'] = WT_THEME_DIR.'images/media/audio.png';
$WT_IMAGES['media_doc'] = WT_THEME_DIR.'images/media/doc.png';
$WT_IMAGES['media_flash'] = WT_THEME_DIR.'images/media/flash.png';
$WT_IMAGES['media_flashrem'] = WT_THEME_DIR.'images/media/flashrem.png';
$WT_IMAGES['media_ged'] = WT_THEME_DIR.'images/media/ged.png';
$WT_IMAGES['media_globe'] = WT_THEME_DIR.'images/media/globe.png';
$WT_IMAGES['media_html'] = WT_THEME_DIR.'images/media/html.png';
$WT_IMAGES['media_picasa'] = WT_THEME_DIR.'images/media/picasa.png';
$WT_IMAGES['media_pdf'] = WT_THEME_DIR.'images/media/pdf.png';
$WT_IMAGES['media_tex'] = WT_THEME_DIR.'images/media/tex.png';
$WT_IMAGES['media_wmv'] = WT_THEME_DIR.'images/media/wmv.png';
$WT_IMAGES['media_wmvrem'] = WT_THEME_DIR.'images/media/wmvrem.png';

//- other images
$WT_IMAGES['add'] = WT_THEME_DIR.'images/add.png';
$WT_IMAGES['darrow'] = WT_THEME_DIR.'images/darrow.png';
$WT_IMAGES['darrow2'] = WT_THEME_DIR.'images/darrow2.png';
$WT_IMAGES['ddarrow'] = WT_THEME_DIR.'images/ddarrow.png';
$WT_IMAGES['dline'] = WT_THEME_DIR.'images/dline.png';
$WT_IMAGES['dline2'] = WT_THEME_DIR.'images/dline2.png';
$WT_IMAGES['webtrees'] = WT_THEME_DIR.'images/webtrees.png';
$WT_IMAGES['hline'] = WT_THEME_DIR.'images/hline.png';
$WT_IMAGES['larrow'] = WT_THEME_DIR.'images/larrow.png';
$WT_IMAGES['larrow2'] = WT_THEME_DIR.'images/larrow2.png';
$WT_IMAGES['ldarrow'] = WT_THEME_DIR.'images/ldarrow.png';
$WT_IMAGES['minus'] = WT_THEME_DIR.'images/minus.png';
$WT_IMAGES['note'] = WT_THEME_DIR.'images/notes.png';
$WT_IMAGES['plus'] = WT_THEME_DIR.'images/plus.png';
$WT_IMAGES['rarrow'] = WT_THEME_DIR.'images/rarrow.png';
$WT_IMAGES['rarrow2'] = WT_THEME_DIR.'images/rarrow2.png';
$WT_IMAGES['rdarrow'] = WT_THEME_DIR.'images/rdarrow.png';
$WT_IMAGES['remove'] = WT_THEME_DIR.'images/delete.png';
$WT_IMAGES['spacer'] = WT_THEME_DIR.'images/spacer.png';
$WT_IMAGES['uarrow'] = WT_THEME_DIR.'images/uarrow.png';
$WT_IMAGES['uarrow2'] = WT_THEME_DIR.'images/uarrow2.png';
$WT_IMAGES['uarrow3'] = WT_THEME_DIR.'images/uarrow3.png';
$WT_IMAGES['udarrow'] = WT_THEME_DIR.'images/udarrow.png';
$WT_IMAGES['vline'] = WT_THEME_DIR.'images/vline.png';
$WT_IMAGES['zoomin'] = WT_THEME_DIR.'images/zoomin.png';
$WT_IMAGES['zoomout'] = WT_THEME_DIR.'images/zoomout.png';
$WT_IMAGES['stop'] = WT_THEME_DIR.'images/stop.png';
$WT_IMAGES['pin-out'] = WT_THEME_DIR.'images/pin-out.png';
$WT_IMAGES['pin-in'] = WT_THEME_DIR.'images/pin-in.png';
$WT_IMAGES['default_image_M'] = WT_THEME_DIR.'images/silhouette_male.png';
$WT_IMAGES['default_image_F'] = WT_THEME_DIR.'images/silhouette_female.png';
$WT_IMAGES['default_image_U'] = WT_THEME_DIR.'images/silhouette_unknown.png';
$WT_IMAGES['slide_open'] = WT_THEME_DIR.'images/open.png';
$WT_IMAGES['slide_close'] = WT_THEME_DIR.'images/close.png';
$WT_IMAGES['reminder'] = WT_THEME_DIR.'images/reminder.png';
$WT_IMAGES['children'] = WT_THEME_DIR.'images/children.png';

// - lifespan chart arrows
$WT_IMAGES['lsltarrow'] = WT_THEME_DIR.'images/lsltarrow.png';
$WT_IMAGES['lsrtarrow'] = WT_THEME_DIR.'images/lsrtarrow.png';
$WT_IMAGES['lsdnarrow'] = WT_THEME_DIR.'images/lsdnarrow.png';
$WT_IMAGES['lsuparrow'] = WT_THEME_DIR.'images/lsuparrow.png';

//-- Variables for the Fan chart
$fanChart = array(
	'font' => WT_ROOT.'includes/fonts/DejaVuSans.ttf',
	'size' => '7px',
	'color' => '#000000',
	'bgColor' => '#eeeeee',
	'bgMColor' => '#b1cff0',
	'bgFColor' => '#e9daf1'
);

//-- This section defines variables for the pedigree chart
$bwidth = 220; // -- width of boxes on pedigree chart
$bheight = 80; // -- height of boxes on pedigree chart
$baseyoffset = 10; // -- position the entire pedigree tree relative to the top of the page
$basexoffset = 10; // -- position the entire pedigree tree relative to the left of the page
$bxspacing = 1; // -- horizontal spacing between boxes on the pedigree chart
$byspacing = 5; // -- vertical spacing between boxes on the pedigree chart
$brborder = 1; // -- box right border thickness

// -- global variables for the descendancy chart
$Dbaseyoffset = 0; // -- position the entire descendancy tree relative to the top of the page
$Dbasexoffset = 0; // -- position the entire descendancy tree relative to the left of the page
$Dbxspacing = 1; // -- horizontal spacing between boxes
$Dbyspacing = 2; // -- vertical spacing between boxes
$Dbwidth = 270; // -- width of DIV layer boxes
$Dbheight = 80; // -- height of DIV layer boxes
$Dindent = 15; // -- width to indent descendancy boxes
$Darrowwidth = 15; // -- additional width to include for the up arrows

$CHARTS_CLOSE_HTML = true; //-- should the charts, pedigree, descendacy, etc close the HTML on the page

// --  The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = "440";
$WT_STATS_S_CHART_Y = "125";
$WT_STATS_L_CHART_X = "900";
// --  For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = "440";
$WT_STATS_MAP_Y = "220";

$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "84beff";
$WT_STATS_CHART_COLOR3 = "c3dfff";

// Arrow symbol or icon for up-page links on Help pages
$UpArrow = "<img src=\"".WT_THEME_DIR."images/uarrow3.png\" class=\"icon\" border=\"0\" alt=\"^\" />";
