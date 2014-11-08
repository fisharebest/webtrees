<?php
// Administration theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

// Theme name - this needs double quotes, as file is scanned/parsed by script
$theme_name = "_administration";

// A version number in the path prevents browser-cache problems during upgrade
define('WT_CSS_URL', WT_THEME_URL . 'css-1.6.0/');

$headerfile = WT_THEME_DIR . 'header.php';
$footerfile = WT_THEME_DIR . 'footer.php';

// Main icons
$WT_IMAGES=array(
	// Lightbox module uses this in manage media links, and also admin_media.php for delete folder.
	'remove'          => WT_CSS_URL . 'images/delete.png',

	// Need different sizes before moving to CSS
	'default_image_F' => WT_CSS_URL . 'images/silhouette_female.png',
	'default_image_M' => WT_CSS_URL . 'images/silhouette_male.png',
	'default_image_U' => WT_CSS_URL . 'images/silhouette_unknown.png',
);
