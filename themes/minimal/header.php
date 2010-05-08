<?php
/**
 * Header for Minimal theme
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo i18n::html_markup(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<?php if (isset($_GET["mod_action"]) && $_GET["mod_action"]=="places_edit") { ?>
			<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> <?php } 
		?>
		<?php if ($FAVICON) { ?><link rel="shortcut icon" href="<?php echo $FAVICON; ?>" type="image/x-icon" /> <?php } ?>

		<title><?php echo $title; ?></title>
		<?php if ($ENABLE_RSS && !$REQUIRE_AUTHENTICATION){ ?>
			<link href="<?php echo encode_url("{$SERVER_URL}rss.php?ged={$GEDCOM}"); ?>" rel="alternate" type="<?php echo $applicationType; ?>" title=" <?php echo htmlspecialchars($GEDCOM_TITLE); ?>" />
		<?php } ?>
		<link rel="stylesheet" href="<?php echo $stylesheet; ?>" type="text/css" media="all" />
		<?php if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) {?> <link rel="stylesheet" href="<?php echo $rtl_stylesheet; ?>" type="text/css" media="all" /> <?php } ?>
		<?php if ($use_alternate_styles && $BROWSERTYPE != "other") { ?>
			<link rel="stylesheet" href="<?php echo WT_THEME_DIR.$BROWSERTYPE; ?>.css" type="text/css" media="all" />
		<?php }
		// Additional css files required (Only if Lightbox installed)
		if (WT_USE_LIGHTBOX) {
			if ($TEXT_DIRECTION=='rtl') {
				echo '<link rel="stylesheet" href="modules/lightbox/css/clearbox_music_RTL.css" type="text/css" />';
				echo '<link rel="stylesheet" href="modules/lightbox/css/album_page_RTL_ff.css" type="text/css" media="screen" />';
			} else {
				echo '<link rel="stylesheet" href="modules/lightbox/css/clearbox_music.css" type="text/css" />';
				echo '<link rel="stylesheet" href="modules/lightbox/css/album_page.css" type="text/css" media="screen" />';
			}
		} ?>

	<link rel="stylesheet" href="<?php echo $print_stylesheet; ?>" type="text/css" media="print" />
	<?php if ($BROWSERTYPE == "msie") { ?>
	<style type="text/css">
		FORM { margin-top: 0px; margin-bottom: 0px; }
	</style>
	<?php }
	if ($view!="preview" && $view!="simple") { ?>
		<?php if (!empty($META_AUTHOR)) { ?><meta name="author" content="<?php echo htmlspecialchars($META_AUTHOR); ?>" /><?php } ?>
		<?php if (!empty($META_PUBLISHER)) { ?><meta name="publisher" content="<?php echo htmlspecialchars($META_PUBLISHER); ?>" /><?php } ?>
		<?php if (!empty($META_COPYRIGHT)) { ?><meta name="copyright" content="<?php echo htmlspecialchars($META_COPYRIGHT); ?>" /><?php } ?>
		<meta name="keywords" content="<?php echo htmlspecialchars($META_KEYWORDS); ?>" />
		<?php if (!empty($META_DESCRIPTION)) {?><meta name="description" content="<?php echo htmlspecialchars($META_DESCRIPTION); ?>" /><?php } ?>
		<?php if (!empty($META_PAGE_TOPIC)) {?><meta name="page-topic" content="<?php echo htmlspecialchars($META_PAGE_TOPIC); ?>" /><?php } ?>
		<?php if (!empty($META_AUDIENCE)) {?><meta name="audience" content="<?php echo htmlspecialchars($META_AUDIENCE); ?>" /><?php } ?>
		<?php if (!empty($META_PAGE_TYPE)) {?><meta name="page-type" content="<?php echo htmlspecialchars($META_PAGE_TYPE); ?>" /><?php } ?>
		<?php if (!empty($META_ROBOTS)) {?><meta name="robots" content="<?php echo htmlspecialchars($META_ROBOTS); ?>" /><?php } ?>
		<?php if (!empty($META_REVISIT)) {?><meta name="revisit-after" content="<?php echo htmlspecialchars($META_REVISIT); ?>" /><?php } ?>
		<meta name="generator" content="<?php echo WT_WEBTREES, ' - ', WT_WEBTREES_URL; ?>" />
	<?php } ?>
	<?php echo $javascript; ?>
	<?php echo $head; //-- additional header information ?>
	<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>
	<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />
	<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme.css" rel="Stylesheet" />
	<?php if ($TEXT_DIRECTION=='rtl') {?>
		<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
	<?php }?>
	<link type="text/css" href="<?php echo WT_THEME_DIR?>modules.css" rel="Stylesheet" />
</head>
<body id="body" <?php echo $bodyOnLoad; ?>>
<!-- begin header section -->
<?php
if ($view!='simple')
if ($view=='preview') include($print_headerfile);
else {?>
<div id="header" class="<?php echo $TEXT_DIRECTION; ?>">
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%">
			<tr>
			<td valign="middle">
				<a href="<?php echo $HOME_SITE_URL; ?>"><?php echo $HOME_SITE_TEXT; ?></a>
				<br />
				<div class="title" style="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right"; ?>">
					<?php print_gedcom_title_link(TRUE); ?>
				</div>
			</td>
			<td align="center" valign="middle">
				<?php print_user_links(); ?>
			</td>
			<?php if (empty($SEARCH_SPIDER)) { ?>
			<td align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right"; ?>" valign="middle" >
				<?php print_theme_dropdown(); ?>
				<?php print_lang_form(); ?>
			</td>
			<td style="white-space: normal;" align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right"; ?>" valign="middle" >
				<form action="search.php" method="get">
					<input type="hidden" name="action" value="general" />
					<input type="hidden" name="topsearch" value="yes" />
					<input type="text" name="query" size="15" value="<?php echo i18n::translate('Search')?>" onfocus="if (this.value == '<?php echo i18n::translate('Search')?>') this.value=''; focusHandler();" onblur="if (this.value == '') this.value='<?php echo i18n::translate('Search')?>';" />
					<input type="submit" name="search" value=" &gt; " />
				</form>
				<?php print_favorite_selector(); ?>
			</td>
			<?php } ?>
			</tr>
			</table>
		</td>
	</tr>
</table>
<?php include($toplinks);
} ?>
<!-- end header section -->
<!-- begin content section -->
