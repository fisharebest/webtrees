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
$stylesheet       = WT_THEME_URL.'style.css';
$headerfile       = WT_THEME_DIR.'header.php';
$footerfile       = WT_THEME_DIR.'footer.php';

//- main icons
$WT_IMAGES=array(
	'webtrees'=>WT_THEME_URL.'images/header.png',
	'edit'=>WT_THEME_URL.'images/edit.png',
	'email'=>WT_THEME_URL.'images/email.png',
	'open'=>WT_THEME_URL.'images/open.png',
	'close'=>WT_THEME_URL.'images/close.png',
	'button_indi'=>WT_THEME_URL.'images/indi.png', // needs to be left here until all themes no longer use it.
	'button_family'=>WT_THEME_URL.'images/family.png',
	'button_note'=>WT_THEME_URL.'images/note.png',
	'button_media'=>WT_THEME_URL.'images/media.png',
	'button_repository'=>WT_THEME_URL.'images/repository.png',
	'button_source'=>WT_THEME_URL.'images/source.png',
	'button_find_facts'=>WT_THEME_URL.'images/find_facts.png',
	'zoomin'=>WT_THEME_URL.'images/zoomin.png',
	'zoomout'=>WT_THEME_URL.'images/zoomout.png',
	'minus'=>WT_THEME_URL.'images/close.png',
	'plus'=>WT_THEME_URL.'images/open.png',
	'remove'=>WT_THEME_URL.'images/delete.png',
	'remove_grey'=>WT_THEME_URL.'images/delete_grey.png',
	'rarrow2'=>WT_THEME_URL.'images/rarrow2.png',
	'larrow2'=>WT_THEME_URL.'images/larrow2.png',
	'darrow2'=>WT_THEME_URL.'images/darrow2.png',
	'uarrow2'=>WT_THEME_URL.'images/uarrow2.png',
	'rarrow'=>WT_THEME_URL.'images/rarrow.png',
	'larrow'=>WT_THEME_URL.'images/larrow.png',
	'darrow'=>WT_THEME_URL.'images/darrow.png',
	'uarrow'=>WT_THEME_URL.'images/uarrow.png',
	'rdarrow'=>WT_THEME_URL.'images/rdarrow.png',
	'ldarrow'=>WT_THEME_URL.'images/ldarrow.png',
	'ddarrow'=>WT_THEME_URL.'images/ddarrow.png',
	'udarrow'=>WT_THEME_URL.'images/udarrow.png',
	'sex_f_9x9'=>WT_THEME_URL.'images/sex_f_9x9.png',
	'sex_m_9x9'=>WT_THEME_URL.'images/sex_m_9x9.png',
	'sex_u_9x9'=>WT_THEME_URL.'images/sex_u_9x9.png',
	'warning'=>WT_THEME_URL.'images/warning.png',

	// media images
	'media'=>WT_THEME_URL.'images/media/media.png',
	'media_audio'=>WT_THEME_URL.'images/media/audio.png',
	'media_doc'=>WT_THEME_URL.'images/media/doc.png',
	'media_flash'=>WT_THEME_URL.'images/media/flash.png',
	'media_flashrem'=>WT_THEME_URL.'images/media/flash_rem.png',
	'media_ged'=>WT_THEME_URL.'images/media/ged.png',
	'media_globe'=>WT_THEME_URL.'images/media/globe.png',
	'media_html'=>WT_THEME_URL.'images/media/html.pmg',
	'media_picasa'=>WT_THEME_URL.'images/media/picasa.png',
	'media_pdf'=>WT_THEME_URL.'images/media/pdf.png',
	'media_tex'=>WT_THEME_URL.'images/media/tex.png',
	'media_wmv'=>WT_THEME_URL.'images/media/wmv.png',
	'media_wmvrem'=>WT_THEME_URL.'images/media/wmv_rem.png',	
);

