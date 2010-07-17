<?php
/**
 * Minimal theme
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

$theme_name       = "minimal"; // need double quotes, as file is scanned/parsed by script
$stylesheet       = WT_THEME_DIR.'style.css';
$rtl_stylesheet   = WT_THEME_DIR.'style_rtl.css';
$print_stylesheet = WT_THEME_DIR.'print.css';
$toplinks         = WT_THEME_DIR.'toplinks.php';
$headerfile       = WT_THEME_DIR.'header.php';
$footerfile       = WT_THEME_DIR.'footer.php';
$FAVICON          = WT_THEME_DIR.'images/favicon.ico';
$WT_USE_HELPIMG   = false;

//-- variables for image names
$WT_IMAGES['sex_m_9x9'] = 'images/sex_m_9x9.gif';
$WT_IMAGES['sex_f_9x9'] = 'images/sex_f_9x9.gif';
$WT_IMAGES['sex_u_9x9'] = 'images/sex_u_9x9.gif';
$WT_IMAGES['sex_m_15x15'] = 'images/sex_m_15x15.gif';
$WT_IMAGES['sex_f_15x15'] = 'images/sex_f_15x15.gif';
$WT_IMAGES['sex_u_15x15'] = 'images/sex_u_15x15.gif';
//- PGV main icons
$WT_IMAGES['admin'] = WT_THEME_DIR.'images/admin.gif';
$WT_IMAGES['ancestry'] = WT_THEME_DIR.'images/ancestry.gif';
$WT_IMAGES['calendar'] = WT_THEME_DIR.'images/calendar.gif';
$WT_IMAGES['cfamily'] = WT_THEME_DIR.'images/cfamily.gif';
$WT_IMAGES['clippings'] = WT_THEME_DIR.'images/clippings.gif';
$WT_IMAGES['descendant'] = WT_THEME_DIR.'images/descendancy.gif';
$WT_IMAGES['fambook'] = WT_THEME_DIR.'images/fambook.gif';
$WT_IMAGES['fanchart'] = WT_THEME_DIR.'images/fanchart.gif';
$WT_IMAGES['gedcom'] = WT_THEME_DIR.'images/gedcom.gif';
$WT_IMAGES['help'] = WT_THEME_DIR.'images/help.gif';
$WT_IMAGES['indis'] = WT_THEME_DIR.'images/indis.gif';
$WT_IMAGES['media'] = WT_THEME_DIR.'images/media.gif';
$WT_IMAGES['mypage'] = WT_THEME_DIR.'images/mypage.gif';
$WT_IMAGES['notes'] = WT_THEME_DIR.'images/notes.gif';
$WT_IMAGES['patriarch'] = WT_THEME_DIR.'images/patriarch.gif';
$WT_IMAGES['pedigree'] = WT_THEME_DIR.'images/pedigree.gif';
$WT_IMAGES['place'] = WT_THEME_DIR.'images/place.gif';
$WT_IMAGES['reports'] = WT_THEME_DIR.'images/report.gif';
$WT_IMAGES['repository'] = WT_THEME_DIR.'images/repository.gif';
$WT_IMAGES['search'] = WT_THEME_DIR.'images/search.gif';
$WT_IMAGES['sfamily'] = WT_THEME_DIR.'images/sfamily.gif';
$WT_IMAGES['source'] = WT_THEME_DIR.'images/source.gif';
$WT_IMAGES['timeline'] = WT_THEME_DIR.'images/timeline.gif';
$WT_IMAGES['tree'] = WT_THEME_DIR.'images/gedcom.gif';

//- PGV buttons for data entry pages
$WT_IMAGES['addmedia_button'] = WT_THEME_DIR.'images/buttons/addmedia.gif';
$WT_IMAGES['addsource_button'] = WT_THEME_DIR.'images/buttons/addsource.gif';
$WT_IMAGES['addnote_button'] = WT_THEME_DIR.'images/buttons/addnote.gif';
$WT_IMAGES['keyboard_button'] = WT_THEME_DIR.'images/buttons/keyboard.gif';
$WT_IMAGES['media_button'] = WT_THEME_DIR.'images/buttons/media.gif';
$WT_IMAGES['source_button'] = WT_THEME_DIR.'images/buttons/source.gif';
$WT_IMAGES['note_button'] = WT_THEME_DIR.'images/buttons/note.gif';
$WT_IMAGES['head_button'] = WT_THEME_DIR.'images/buttons/head.gif';
$WT_IMAGES['find_facts_button'] = WT_THEME_DIR.'images/buttons/find_facts.png';

// Media images
$WT_IMAGES['media_audio'] = WT_THEME_DIR.'images/../../../images/media/audio.png';
$WT_IMAGES['media_doc'] = WT_THEME_DIR.'images/../../../images/media/doc.gif';
$WT_IMAGES['media_flash'] = WT_THEME_DIR.'images/../../../images/media/flash.png';
$WT_IMAGES['media_flashrem'] = WT_THEME_DIR.'images/../../../images/media/flashrem.png';
$WT_IMAGES['media_ged'] = WT_THEME_DIR.'images/../../../images/media/ged.gif';
$WT_IMAGES['media_globe'] = WT_THEME_DIR.'images/../../../images/media/globe.png';
$WT_IMAGES['media_html'] = WT_THEME_DIR.'images/../../../images/media/html.gif';
$WT_IMAGES['media_picasa'] = WT_THEME_DIR.'images/../../../images/media/picasa.png';
$WT_IMAGES['media_pdf'] = WT_THEME_DIR.'images/../../../images/media/pdf.gif';
$WT_IMAGES['media_tex'] = WT_THEME_DIR.'images/../../../images/media/tex.gif';
$WT_IMAGES['media_wmv'] = WT_THEME_DIR.'images/../../../images/media/wmv.png';
$WT_IMAGES['media_wmvrem'] = WT_THEME_DIR.'images/../../../images/media/wmvrem.png';

//- other images
$WT_IMAGES['add'] = WT_THEME_DIR.'images/add.gif';
$WT_IMAGES['darrow'] = WT_THEME_DIR.'images/darrow.gif';
$WT_IMAGES['darrow2'] = WT_THEME_DIR.'images/darrow2.gif';
$WT_IMAGES['ddarrow'] = WT_THEME_DIR.'images/ddarrow.gif';
$WT_IMAGES['dline'] = WT_THEME_DIR.'images/dline.gif';
$WT_IMAGES['dline2'] = WT_THEME_DIR.'images/dline2.gif';
$WT_IMAGES['webtrees'] = WT_THEME_DIR.'images/webtrees.gif';
$WT_IMAGES['hline'] = WT_THEME_DIR.'images/hline.gif';
$WT_IMAGES['larrow'] = WT_THEME_DIR.'images/larrow.gif';
$WT_IMAGES['larrow2'] = WT_THEME_DIR.'images/larrow2.gif';
$WT_IMAGES['ldarrow'] = WT_THEME_DIR.'images/ldarrow.gif';
$WT_IMAGES['minus'] = WT_THEME_DIR.'images/minus.gif';
$WT_IMAGES['note'] = WT_THEME_DIR.'images/notes.gif';
$WT_IMAGES['plus'] = WT_THEME_DIR.'images/plus.gif';
$WT_IMAGES['rarrow'] = WT_THEME_DIR.'images/rarrow.gif';
$WT_IMAGES['rarrow2'] = WT_THEME_DIR.'images/rarrow2.gif';
$WT_IMAGES['rdarrow'] = WT_THEME_DIR.'images/rdarrow.gif';
$WT_IMAGES['remove']	= WT_THEME_DIR.'images/remove.gif';
$WT_IMAGES['spacer'] = WT_THEME_DIR.'images/spacer.gif';
$WT_IMAGES['uarrow'] = WT_THEME_DIR.'images/uarrow.gif';
$WT_IMAGES['uarrow2'] = WT_THEME_DIR.'images/uarrow2.gif';
$WT_IMAGES['uarrow3'] = WT_THEME_DIR.'images/uarrow3.gif';
$WT_IMAGES['udarrow'] = WT_THEME_DIR.'images/udarrow.gif';
$WT_IMAGES['vline'] = WT_THEME_DIR.'images/vline.gif';
$WT_IMAGES['zoomin'] = WT_THEME_DIR.'images/zoomin.gif';
$WT_IMAGES['zoomout'] = WT_THEME_DIR.'images/zoomout.gif';
$WT_IMAGES['stop'] = WT_THEME_DIR.'images/stop.gif';
$WT_IMAGES['pin-out'] = WT_THEME_DIR.'images/pin-out.png';
$WT_IMAGES['pin-in'] = WT_THEME_DIR.'images/pin-in.png';
$WT_IMAGES['default_image_M'] = WT_THEME_DIR.'images/silhouette_male.gif';
$WT_IMAGES['default_image_F'] = WT_THEME_DIR.'images/silhouette_female.gif';
$WT_IMAGES['default_image_U'] = WT_THEME_DIR.'images/silhouette_unknown.gif';
$WT_IMAGES['slide_open'] = WT_THEME_DIR.'images/open.png';
$WT_IMAGES['slide_close'] = WT_THEME_DIR.'images/close.png';

// - lifespan chart arrows
$WT_IMAGES['lsltarrow'] = WT_THEME_DIR.'images/lsltarrow.gif';
$WT_IMAGES['lsrtarrow'] = WT_THEME_DIR.'images/lsrtarrow.gif';
$WT_IMAGES['lsdnarrow'] = WT_THEME_DIR.'images/lsdnarrow.gif';
$WT_IMAGES['lsuparrow'] = WT_THEME_DIR.'images/lsuparrow.gif';

//-- Variables for the Fan chart
$fanChart = array(
	'font'		=> WT_ROOT.'includes/fonts/DejaVuSans.ttf',
	'size'		=> '7px',
	'color'		=> '#000000',
	'bgColor'	=> '#eeeeee',
	'bgMColor'	=> '#cccccc',
	'bgFColor'	=> '#dddddd'
);

//-- This section defines variables for the pedigree chart
$bwidth = 225;		// -- width of boxes on pedigree chart
$bheight = 80;		// -- height of boxes on pedigree chart
$baseyoffset = 10;	// -- position the entire pedigree tree relative to the top of the page
$basexoffset = 10;	// -- position the entire pedigree tree relative to the left of the page
$bxspacing = 0;		// -- horizontal spacing between boxes on the pedigree chart
$byspacing = 5;		// -- vertical spacing between boxes on the pedigree chart
$brborder = 1;		// -- box right border thickness

// -- global variables for the descendancy chart
$Dbaseyoffset = 0;	// -- position the entire descendancy tree relative to the top of the page
$Dbasexoffset = 0;	// -- position the entire descendancy tree relative to the left of the page
$Dbxspacing = 0;	// -- horizontal spacing between boxes
$Dbyspacing = 1;	// -- vertical spacing between boxes
$Dbwidth = 270;		// -- width of DIV layer boxes
$Dbheight = 80;		// -- height of DIV layer boxes
$Dindent = 15;		// -- width to indent descendancy boxes
$Darrowwidth = 15;	// -- additional width to include for the up arrows

$CHARTS_CLOSE_HTML = true;		//-- should the charts, pedigree, descendacy, etc close the HTML on the page

// --  The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = "440";
$WT_STATS_S_CHART_Y = "125";
$WT_STATS_L_CHART_X = "900";
// --  For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = "440";
$WT_STATS_MAP_Y = "220";

$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "555555";
$WT_STATS_CHART_COLOR3 = "cccccc";

// Arrow symbol or icon for up-page links on Help pages
$UpArrow = "<b>^^&nbsp;&nbsp;</b>";
