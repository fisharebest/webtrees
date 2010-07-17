<?php
/**
 * Standard theme
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
  * PNG Icons By:Alessandro Rei; License: GPL; http://www.kde-look.org/content/show.php/Dark-Glass+reviewed?content=67902
 *
 * @package webtrees
 * @subpackage Themes
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$theme_name = "webtrees"; // need double quotes, as file is scanned/parsed by script
$stylesheet       = WT_THEME_DIR.'style.css';
$rtl_stylesheet   = WT_THEME_DIR.'style_rtl.css';
$print_stylesheet = WT_THEME_DIR.'print.css';
$toplinks         = WT_THEME_DIR.'toplinks.php';
$headerfile       = WT_THEME_DIR.'header.php';
$footerfile       = WT_THEME_DIR.'footer.php';
$FAVICON          = WT_THEME_DIR.'images/favicon.ico';
$WT_USE_HELPIMG   = true;
$WT_MENU_LOCATION = 'top';

//-- variables for image names
//- PGV main icons
$WT_IMAGES['calendar']['large'] = WT_THEME_DIR.'images/calendar.png';
$WT_IMAGES['clippings']['large'] = WT_THEME_DIR.'images/clippings.png';
$WT_IMAGES['gedcom']['large'] = WT_THEME_DIR.'images/gedcom.png';
$WT_IMAGES['help']['large'] = WT_THEME_DIR.'images/help.png';
$WT_IMAGES['indis']['large'] = WT_THEME_DIR.'images/indis.png';
$WT_IMAGES['media']['large'] = WT_THEME_DIR.'images/media.gif';
$WT_IMAGES['mypage']['large'] = WT_THEME_DIR.'images/mypage.png';
$WT_IMAGES['notes']['large'] = WT_THEME_DIR.'images/notes.gif';
$WT_IMAGES['other']['large'] = WT_THEME_DIR.'images/other.png';
$WT_IMAGES['pedigree']['large'] = WT_THEME_DIR.'images/pedigree.png';
$WT_IMAGES['reports']['large'] = WT_THEME_DIR.'images/reports.png';
$WT_IMAGES['repository']['large'] = WT_THEME_DIR.'images/repository.gif';
$WT_IMAGES['search']['large'] = WT_THEME_DIR.'images/search.png';
$WT_IMAGES['sfamily']['large'] = WT_THEME_DIR.'images/sfamily.gif';
$WT_IMAGES['source']['large'] = WT_THEME_DIR.'images/source.gif';
$WT_IMAGES['sex']['large'] = WT_THEME_DIR.'images/male.gif';
$WT_IMAGES['sexf']['large'] = WT_THEME_DIR.'images/female.gif';
$WT_IMAGES['sexn']['large'] = WT_THEME_DIR.'images/fe_male.gif';
$WT_IMAGES['edit_fam']['large'] = WT_THEME_DIR.'images/edit_fam.png';
$WT_IMAGES['edit_indi']['large'] = WT_THEME_DIR.'images/edit_indi.png';
$WT_IMAGES['edit_media']['large'] = WT_THEME_DIR.'images/edit_media.png';
$WT_IMAGES['edit_note']['large'] = WT_THEME_DIR.'images/edit_note.png';
$WT_IMAGES['edit_repo']['large'] = WT_THEME_DIR.'images/edit_repo.png';
$WT_IMAGES['edit_source']['large'] = WT_THEME_DIR.'images/edit_source.png';

//- PGV small icons
$WT_IMAGES['admin']['small'] = WT_THEME_DIR.'images/small/admin.png';
$WT_IMAGES['ancestry']['small'] = WT_THEME_DIR.'images/small/ancestry.png';
$WT_IMAGES['calendar']['small'] = WT_THEME_DIR.'images/small/calendar.png';
$WT_IMAGES['cfamily']['small'] = WT_THEME_DIR.'images/small/cfamily.png';
$WT_IMAGES['clippings']['small'] = WT_THEME_DIR.'images/small/clippings.png';
$WT_IMAGES['descendant']['small'] = WT_THEME_DIR.'images/small/descendancy.png';
$WT_IMAGES['favorites']['small'] = WT_THEME_DIR.'images/small/favorites.png';
$WT_IMAGES['edit_fam']['small'] = WT_THEME_DIR.'images/small/edit_fam.png';
$WT_IMAGES['edit_indi']['small'] = WT_THEME_DIR.'images/small/edit_indi.gif';
$WT_IMAGES['edit_media']['small'] = WT_THEME_DIR.'images/small/edit_media.png';
$WT_IMAGES['edit_note']['small'] = WT_THEME_DIR.'images/small/edit_note.png';
$WT_IMAGES['edit_repo']['small'] = WT_THEME_DIR.'images/small/edit_repo.png';
$WT_IMAGES['edit_sour']['small'] = WT_THEME_DIR.'images/small/edit_source.png';
$WT_IMAGES['fambook']['small'] = WT_THEME_DIR.'images/small/fambook.png';
$WT_IMAGES['fanchart']['small'] = WT_THEME_DIR.'images/small/fanchart.png';
$WT_IMAGES['gedcom']['small'] = WT_THEME_DIR.'images/small/tree.png';
$WT_IMAGES['help']['small'] = WT_THEME_DIR.'images/small/help.png';
$WT_IMAGES['hourglass']['small'] = WT_THEME_DIR.'images/small/hourglass.png';
$WT_IMAGES['indis']['small'] = WT_THEME_DIR.'images/small/indis.png';
$WT_IMAGES['media']['small'] = WT_THEME_DIR.'images/small/media.gif';
$WT_IMAGES['menu_help']['small'] = WT_THEME_DIR.'images/small/help2.png';
$WT_IMAGES['menu_media']['small'] = WT_THEME_DIR.'images/small/media.png';
$WT_IMAGES['menu_repository']['small'] = WT_THEME_DIR.'images/small/repository.png';
$WT_IMAGES['menu_source']['small'] = WT_THEME_DIR.'images/small/source.png';
$WT_IMAGES['mypage']['small'] = WT_THEME_DIR.'images/small/mypage.png';
$WT_IMAGES['notes']['small'] = WT_THEME_DIR.'images/small/notes.png';
$WT_IMAGES['patriarch']['small'] = WT_THEME_DIR.'images/small/patriarch.png';
$WT_IMAGES['pedigree']['small'] = WT_THEME_DIR.'images/small/pedigree.png';
$WT_IMAGES['place']['small'] = WT_THEME_DIR.'images/small/place.png';
$WT_IMAGES['relationship']['small'] = WT_THEME_DIR.'images/small/relationship.gif';
$WT_IMAGES['reports']['small'] = WT_THEME_DIR.'images/small/reports.gif';
$WT_IMAGES['repository']['small'] = WT_THEME_DIR.'images/small/repository.png';
$WT_IMAGES['search']['small'] = WT_THEME_DIR.'images/small/search.png';
$WT_IMAGES['sex']['small'] = WT_THEME_DIR.'images/small/male.gif';
$WT_IMAGES['sexf']['small'] = WT_THEME_DIR.'images/small/female.gif';
$WT_IMAGES['sexn']['small'] = WT_THEME_DIR.'images/small/fe_male.gif';
$WT_IMAGES['sfamily']['small'] = WT_THEME_DIR.'images/small/sfamily.png';
$WT_IMAGES['source']['small'] = WT_THEME_DIR.'images/small/source.png';
$WT_IMAGES['statistic']['small'] = WT_THEME_DIR.'images/small/statistic.png';
$WT_IMAGES['timeline']['small'] = WT_THEME_DIR.'images/small/timeline.png';
$WT_IMAGES['tree']['small'] = WT_THEME_DIR.'images/small/tree.png';
$WT_IMAGES['wiki']['small'] = WT_THEME_DIR.'images/small/w_22.png';

//- PGV buttons for data entry pages
$WT_IMAGES['addmedia']['button'] = WT_THEME_DIR.'images/buttons/addmedia.gif';
$WT_IMAGES['addrepository']['button'] = WT_THEME_DIR.'images/buttons/addrepository.gif';
$WT_IMAGES['addsource']['button'] = WT_THEME_DIR.'images/buttons/addsource.gif';
$WT_IMAGES['addnote']['button'] = WT_THEME_DIR.'images/buttons/addnote.gif';
$WT_IMAGES['calendar']['button'] = WT_THEME_DIR.'images/buttons/calendar.gif';
$WT_IMAGES['family']['button'] = WT_THEME_DIR.'images/buttons/family.gif';
$WT_IMAGES['indi']['button'] = WT_THEME_DIR.'images/buttons/indi.gif';
$WT_IMAGES['keyboard']['button'] = WT_THEME_DIR.'images/buttons/keyboard.gif';
$WT_IMAGES['media']['button'] = WT_THEME_DIR.'images/buttons/media.gif';
$WT_IMAGES['place']['button'] = WT_THEME_DIR.'images/buttons/place.gif';
$WT_IMAGES['repository']['button'] = WT_THEME_DIR.'images/buttons/repository.gif';
$WT_IMAGES['source']['button'] = WT_THEME_DIR.'images/buttons/source.gif';
$WT_IMAGES['note']['button'] = WT_THEME_DIR.'images/buttons/note.gif';
$WT_IMAGES['head']['button'] = WT_THEME_DIR.'images/buttons/head.gif';
$WT_IMAGES['find_facts']['button'] = WT_THEME_DIR.'images/buttons/find_facts.png';

// Media images
$WT_IMAGES['media']['audio'] = WT_THEME_DIR.'images/media/audio.png';
$WT_IMAGES['media']['doc'] = WT_THEME_DIR.'images/media/doc.gif';
$WT_IMAGES['media']['flash'] = WT_THEME_DIR.'images/media/flash.png';
$WT_IMAGES['media']['flashrem'] = WT_THEME_DIR.'images/media/flashrem.png';
$WT_IMAGES['media']['ged'] = WT_THEME_DIR.'images/media/ged.gif';
$WT_IMAGES['media']['globe'] = WT_THEME_DIR.'images/media/globe.png';
$WT_IMAGES['media']['html'] = WT_THEME_DIR.'images/media/html.gif';
$WT_IMAGES['media']['picasa'] = WT_THEME_DIR.'images/media/picasa.png';
$WT_IMAGES['media']['pdf'] = WT_THEME_DIR.'images/media/pdf.gif';
$WT_IMAGES['media']['tex'] = WT_THEME_DIR.'images/media/tex.gif';
$WT_IMAGES['media']['wmv'] = WT_THEME_DIR.'images/media/wmv.png';
$WT_IMAGES['media']['wmvrem'] = WT_THEME_DIR.'images/media/wmvrem.png';

//- other images
$WT_IMAGES['add']['other']	= WT_THEME_DIR.'images/add.gif';
$WT_IMAGES['darrow']['other'] = WT_THEME_DIR.'images/darrow.gif';
$WT_IMAGES['darrow2']['other'] = WT_THEME_DIR.'images/darrow2.gif';
$WT_IMAGES['ddarrow']['other'] = WT_THEME_DIR.'images/ddarrow.gif';
$WT_IMAGES['dline']['other'] = WT_THEME_DIR.'images/dline.gif';
$WT_IMAGES['dline2']['other'] = WT_THEME_DIR.'images/dline2.gif';
$WT_IMAGES['webtrees']['other'] = WT_THEME_DIR.'images/webtrees_s.png';
$WT_IMAGES['hline']['other'] = WT_THEME_DIR.'images/hline.gif';
$WT_IMAGES['larrow']['other'] = WT_THEME_DIR.'images/larrow.gif';
$WT_IMAGES['larrow2']['other'] = WT_THEME_DIR.'images/larrow2.gif';
$WT_IMAGES['ldarrow']['other'] = WT_THEME_DIR.'images/ldarrow.gif';
$WT_IMAGES['minus']['other'] = WT_THEME_DIR.'images/minus.gif';
$WT_IMAGES['note']['other'] = WT_THEME_DIR.'images/notes.gif';
$WT_IMAGES['plus']['other'] = WT_THEME_DIR.'images/plus.gif';
$WT_IMAGES['rarrow']['other'] = WT_THEME_DIR.'images/rarrow.gif';
$WT_IMAGES['rarrow2']['other'] = WT_THEME_DIR.'images/rarrow2.gif';
$WT_IMAGES['rdarrow']['other'] = WT_THEME_DIR.'images/rdarrow.gif';
$WT_IMAGES['remove']['other']	= WT_THEME_DIR.'images/remove.gif';
$WT_IMAGES['spacer']['other'] = WT_THEME_DIR.'images/spacer.gif';
$WT_IMAGES['uarrow']['other'] = WT_THEME_DIR.'images/uarrow.gif';
$WT_IMAGES['uarrow2']['other'] = WT_THEME_DIR.'images/uarrow2.gif';
$WT_IMAGES['uarrow3']['other'] = WT_THEME_DIR.'images/uarrow3.gif';
$WT_IMAGES['udarrow']['other'] = WT_THEME_DIR.'images/udarrow.gif';
$WT_IMAGES['vline']['other'] = WT_THEME_DIR.'images/vline.gif';
$WT_IMAGES['zoomin']['other'] = WT_THEME_DIR.'images/zoomin.gif';
$WT_IMAGES['zoomout']['other'] = WT_THEME_DIR.'images/zoomout.gif';
$WT_IMAGES['stop']['other'] = WT_THEME_DIR.'images/stop.gif';
$WT_IMAGES['pin-out']['other'] = WT_THEME_DIR.'images/pin-out.png';
$WT_IMAGES['pin-in']['other'] = WT_THEME_DIR.'images/pin-in.png';
$WT_IMAGES['default_image_M']['other'] = WT_THEME_DIR.'images/silhouette_male.gif';
$WT_IMAGES['default_image_F']['other'] = WT_THEME_DIR.'images/silhouette_female.gif';
$WT_IMAGES['default_image_U']['other'] = WT_THEME_DIR.'images/silhouette_unknown.gif';
$WT_IMAGES['slide_open']['other'] = WT_THEME_DIR.'images/open.png';
$WT_IMAGES['slide_close']['other'] = WT_THEME_DIR.'images/close.png';

// - lifespan chart arrows
$WT_IMAGES['lsltarrow']['other'] = WT_THEME_DIR.'images/lsltarrow.gif';
$WT_IMAGES['lsrtarrow']['other'] = WT_THEME_DIR.'images/lsrtarrow.gif';
$WT_IMAGES['lsdnarrow']['other'] = WT_THEME_DIR.'images/lsdnarrow.gif';
$WT_IMAGES['lsuparrow']['other'] = WT_THEME_DIR.'images/lsuparrow.gif';

//-- Variables for the Fan chart
$fanChart = array(
	'font'		=> WT_ROOT.'includes/fonts/DejaVuSans.ttf',
	'size'		=> '7px',
	'color'		=> '#000000',
	'bgColor'	=> '#eeeeee',
	'bgMColor'	=> '#b1cff0',
	'bgFColor'	=> '#e9daf1'
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
$Dbasexoffset = 0;		// -- position the entire descendancy tree relative to the left of the page
$Dbxspacing = 0;		// -- horizontal spacing between boxes
$Dbyspacing = 1;		// -- vertical spacing between boxes
$Dbwidth = 270;			// -- width of DIV layer boxes
$Dbheight = 80;			// -- height of DIV layer boxes
$Dindent = 15;			// -- width to indent descendancy boxes
$Darrowwidth = 15;		// -- additional width to include for the up arrows

$CHARTS_CLOSE_HTML = true;		//-- should the charts, pedigree, descendacy, etc close the HTML on the page

// --  The largest possible area for charts is 300,000 pixels. As the maximum height or width is 1000 pixels
$WT_STATS_S_CHART_X = "440";
$WT_STATS_S_CHART_Y = "125";
$WT_STATS_L_CHART_X = "900";
// --  For map charts, the maximum size is 440 pixels wide by 220 pixels high
$WT_STATS_MAP_X = "440";
$WT_STATS_MAP_Y = "220";

$WT_STATS_CHART_COLOR1 = "ffffff";
$WT_STATS_CHART_COLOR2 = "9ca3d4";
$WT_STATS_CHART_COLOR3 = "e5e6ef";

// Arrow symbol or icon for up-page links on Help pages
$UpArrow = "<img src=\"".WT_THEME_DIR."images/uarrow3.gif\" class=\"icon\" border=\"0\" alt=\"^\" />";
