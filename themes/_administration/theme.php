<?php
// Administration theme
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// $Id: theme.php 9831 2010-11-13 04:43:15Z nigel $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$theme_name = "_administration"; // need double quotes, as file is scanned/parsed by script
$stylesheet = WT_THEME_URL.'style.css';
$headerfile = WT_THEME_DIR.'header.php';
$footerfile = WT_THEME_DIR.'footer.php';

//- main icons
$WT_IMAGES=array(
	// lightbox module uses this in manage media links, and also admin_media.php for delete folder.
	'remove'         =>WT_THEME_URL.'images/delete.png',

	// need different sizes before moving to CSS
	'default_image_F'=>WT_THEME_URL.'images/silhouette_female.png',
	'default_image_M'=>WT_THEME_URL.'images/silhouette_male.png',
	'default_image_U'=>WT_THEME_URL.'images/silhouette_unknown.png',

	// need to replace with a system based on mime-types
	'media'          =>WT_THEME_URL.'images/media/media.png',
	'media_audio'    =>WT_THEME_URL.'images/media/audio.png',
	'media_doc'      =>WT_THEME_URL.'images/media/doc.png',
	'media_flash'    =>WT_THEME_URL.'images/media/flash.png',
	'media_flashrem' =>WT_THEME_URL.'images/media/flash_rem.png',
	'media_ged'      =>WT_THEME_URL.'images/media/ged.png',
	'media_globe'    =>WT_THEME_URL.'images/media/globe.png',
	'media_html'     =>WT_THEME_URL.'images/media/html.pmg',
	'media_picasa'   =>WT_THEME_URL.'images/media/picasa.png',
	'media_pdf'      =>WT_THEME_URL.'images/media/pdf.png',
	'media_tex'      =>WT_THEME_URL.'images/media/tex.png',
	'media_wmv'      =>WT_THEME_URL.'images/media/wmv.png',
	'media_wmvrem'   =>WT_THEME_URL.'images/media/wmv_rem.png',	
);
