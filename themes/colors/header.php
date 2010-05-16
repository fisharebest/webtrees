<?php
/**
 * Header for colors theme
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
 * @package webtrees
 * @subpackage Themes
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Definitions to simplify logic on pages with right-to-left languages
// TODO: merge this into the trunk?
if ($TEXT_DIRECTION=='ltr') {
	define ('WT_CSS_ALIGN',         'left');
	define ('WT_CSS_REVERSE_ALIGN', 'right');
} else {
	define ('WT_CSS_ALIGN',         'right');
	define ('WT_CSS_REVERSE_ALIGN', 'left');
}

echo
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<html xmlns="http://www.w3.org/1999/xhtml" ',  i18n::html_markup(), '>',
	'<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',
	'<title>', htmlspecialchars($GEDCOM_TITLE), '</title>',
	'<link rel="shortcut icon" href="', $FAVICON, '" type="image/x-icon">';

if ($ENABLE_RSS && !$REQUIRE_AUTHENTICATION) {
	echo '<link href="', urlencode($SERVER_URL.'rss.php?ged='.WT_GEDCOM), '" rel="alternate" type="', $applicationType, '" title="', htmlspecialchars($GEDCOM_TITLE), '" />';
}

if (WT_USE_LIGHTBOX) {
	if ($TEXT_DIRECTION=='rtl') {
		echo
			'<link rel="stylesheet" href="modules/lightbox/css/clearbox_music_RTL.css" type="text/css" />',
			'<link rel="stylesheet" href="modules/lightbox/css/album_page_RTL_ff.css" type="text/css" media="screen" />';
	} else {
		echo
			'<link rel="stylesheet" href="modules/lightbox/css/clearbox_music.css" type="text/css" />',
			'<link rel="stylesheet" href="modules/lightbox/css/album_page.css" type="text/css" media="screen" />';
	}
}

echo
	'<meta name="author" content="', htmlspecialchars($META_AUTHOR), '" />',
	'<meta name="publisher" content="', htmlspecialchars($META_PUBLISHER), '" />',
	'<meta name="copyright" content="', htmlspecialchars($META_COPYRIGHT), '" />',
	'<meta name="description" content="', htmlspecialchars($META_DESCRIPTION), '" />',
	'<meta name="page-topic" content="', htmlspecialchars($META_PAGE_TOPIC), '" />',
	'<meta name="audience" content="', htmlspecialchars($META_AUDIENCE), '" />',
	'<meta name="page-type" content="', htmlspecialchars($META_PAGE_TYPE), '" />',
	'<meta name="robots" content="', htmlspecialchars($META_ROBOTS), '" />',
	'<meta name="revisit-after" content="', htmlspecialchars($META_REVISIT), '" />',
	'<meta name="keywords" content="', htmlspecialchars($META_KEYWORDS), '" />',
	'<meta name="generator" content="', WT_WEBTREES, ' ', WT_VERSION_TEXT, '" />';


echo
	$javascript, $head, 
	'<script type="text/javascript" src="js/jquery/jquery.min.js"></script>',
	'<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>',
	'<script type="text/javascript" src="js/jquery/jquery.tablesorter.js"></script>',
	'<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />';
?>

<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme.css" rel="Stylesheet" />
<link rel="stylesheet" href="<?php echo $print_stylesheet; ?>" type="text/css" media="print" />

<?php
if ($TEXT_DIRECTION=='rtl') { ?>
	<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
<?php } 

echo
	'<link type="text/css" href="themes/colors/modules.css" rel="Stylesheet" />',
	'<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';
	
if ($use_alternate_styles && $BROWSERTYPE != "other") { ?>
	<link rel="stylesheet" href="<?php echo $THEME_DIR.$BROWSERTYPE; ?>.css" type="text/css" media="all" />
<?php 
}
	
	
if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) {?> 
	<link rel="stylesheet" href="<?php echo $rtl_stylesheet; ?>" type="text/css" media="all" /> 
<?php }
	echo
	'</head><body id="body" ', $bodyOnLoad, '>';
flush(); // Allow the browser to start fetching external stylesheets, javascript, etc.
?>

<!-- Remove header for edit windows -->
<?php if ($view!='simple') {?>

<!-- begin header section -->
<div id="header" class="<?php echo $TEXT_DIRECTION; ?>">
<!-- begin colors code -->
<table class="header">
	<tr>
		<td align="<?php echo $TEXT_DIRECTION=="ltr"?"left":"right" ?>">
		<div class="title">
			<?php print_gedcom_title_link(TRUE);?>
		</div>
		</td>

<?php if(empty($SEARCH_SPIDER)) { ?>

		<td align="<?php echo $TEXT_DIRECTION=="ltr"?"right":"left" ?>">
			<div style="white-space: normal;" align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right" ?>">
			<form action="search.php" method="post">
				<input type="hidden" name="action" value="general" />
				<input type="hidden" name="topsearch" value="yes" />
				<input type="text" class="formbut" name="query" size="15" value="<?php echo i18n::translate('Search')?>" onfocus="if (this.value == '<?php echo i18n::translate('Search')?>') this.value=''; focusHandler();" onblur="if (this.value == '') this.value='<?php echo i18n::translate('Search')?>';" />
				<input type="image" src="<?php echo $WT_IMAGE_DIR ?>/go.gif" align="top" title="<?php echo i18n::translate('Search')?>" />
			</form>
			</div>
			<div align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right" ?>">
				<?php } ?>
					<?php print_favorite_selector(); ?>
			</div>
		</td>
	</tr>
</table>
</div>
<!--end colors code -->
<?php include($toplinks);
} ?>
<!-- end header section -->
<!-- begin content section -->
