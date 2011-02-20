<?php
/**
 * Header for Xenea theme
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

global $DATE_FORMAT;

$displayDate=timestamp_to_gedcom_date(client_time())->Display(false, $DATE_FORMAT);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo WT_I18N::html_markup(); ?>>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<?php if (isset($_GET["mod_action"]) && $_GET["mod_action"]=="places_edit") { ?>
			<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> <?php }
		?>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

		<title><?php echo htmlspecialchars($title); ?></title>
		<link rel="stylesheet" href="<?php echo $stylesheet; ?>" type="text/css" media="all" />
		<?php if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) { ?> <link rel="stylesheet" href="<?php echo $rtl_stylesheet; ?>" type="text/css" media="all" /> <?php } ?>
		<?php if (file_exists(WT_THEME_DIR.$BROWSERTYPE.'.css')) { ?>
			<link rel="stylesheet" href="<?php echo WT_THEME_DIR.$BROWSERTYPE; ?>.css" type="text/css" media="all" />
		<?php }
		// Additional css files required (Only if Lightbox installed)
		if (WT_USE_LIGHTBOX) {
			if ($TEXT_DIRECTION=='rtl') {
				echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/clearbox_music_RTL.css" type="text/css" />';
				echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/album_page_RTL_ff.css" type="text/css" media="screen" />';
			} else {
				echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/clearbox_music.css" type="text/css" />';
				echo '<link rel="stylesheet" href="', WT_MODULES_DIR, 'lightbox/css/album_page.css" type="text/css" media="screen" />';
			}
		} ?>

	<link rel="stylesheet" href="<?php echo $print_stylesheet; ?>" type="text/css" media="print" />
	<?php 
	if (!empty($LINK_CANONICAL)) {
		echo '<link rel="canonical" href="', $LINK_CANONICAL, '" />';
	}
	if (!empty($META_DESCRIPTION)) {
		echo '<meta name="description" content="', htmlspecialchars($META_DESCRIPTION), '" />';
	}
	if (!empty($META_ROBOTS)) {
		echo '<meta name="robots" content="', htmlspecialchars($META_ROBOTS), '" />';
	}
	if (!empty($META_GENERATOR)) {
		echo '<meta name="generator" content="', $META_GENERATOR, '" />';
	}
	?>
	<?php echo $javascript; ?>
	<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />
	<link type="text/css" href="<?php echo WT_THEME_DIR; ?>jquery/jquery-ui_theme.css" rel="Stylesheet" />
	<?php if ($TEXT_DIRECTION=='rtl') { ?>
		<link type="text/css" href="<?php echo WT_THEME_DIR; ?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
	<?php } ?>
	<link type="text/css" href="<?php echo WT_THEME_DIR; ?>modules.css" rel="Stylesheet" />
</head>
<body id="body">
<!-- begin header section -->
<?php if ($view!='simple') { ?>
<div id="header" class="<?php echo $TEXT_DIRECTION; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="1" bgcolor="#003399">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-image:url('<?php
			if ($TEXT_DIRECTION=="ltr") {
				echo WT_THEME_DIR,"images/cabeza.jpg'); ";
				echo "background-position:left top; ";
			} else {
				echo WT_THEME_DIR,"images/cabeza_rtl.jpg'); ";
				echo "background-position:right top; ";
			}
			?>background-repeat:repeat-y; height:40px;">
			<tr>
				<td width="10"><img src="<?php echo WT_THEME_DIR; ?>images/pixel.gif" width="1" height="1" alt="" /></td>
				<td valign="middle"><font color="#FFFFFF" size="5" face="Verdana, Arial, Helvetica, sans-serif">
				<?php echo PrintReady($GEDCOM_TITLE, true); ?>
				</font></td>
		<?php if (empty($SEARCH_SPIDER)) { ?>
				<td align="<?php echo $TEXT_DIRECTION=="rtl"?"left":"right"; ?>">
				<form action="search.php" method="get">
				<input type="hidden" name="action" value="general" />
				<input type="hidden" name="topsearch" value="yes" />
				<input type="text" name="query" size="12" value="<?php echo WT_I18N::translate('Search'); ?>" onfocus="if (this.value == '<?php echo WT_I18N::translate('Search'); ?>') this.value=''; focusHandler();" onblur="if (this.value == '') this.value='<?php echo WT_I18N::translate('Search'); ?>';" />
				<input type="submit" name="search" value="&gt;" />
				</form>
				</td>
				<td width="10"><img src="<?php echo WT_THEME_DIR; ?>images/pixel.gif" width="1" height="1" alt="" /></td>
		<?php } ?>
			</tr></table>
		<?php if (empty($SEARCH_SPIDER)) { ?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#84beff" style="background-image:url('<?php echo WT_THEME_DIR; ?>images/barra.gif');">
			 <tr class="blanco">
				<td width="10" height="40"><img src="<?php echo WT_THEME_DIR; ?>images/pixel.gif" width="1" height="18" alt="" /></td>
				<td width="115"><div id="favtheme" align="<?php echo $TEXT_DIRECTION=="rtl"?"right":"left"; ?>" >
					<?php
					$menu=WT_MenuBar::getThemeMenu();
					if ($menu) {
						echo $menu->getMenu();
					}
					print_favorite_selector(1);
				?>
				</div></td>
				<td><div align="center" >
				<?php
					if (WT_USER_ID) {
						echo '<a href="edituser.php" class="link">', WT_I18N::translate('Logged in as '), ' (', WT_USER_NAME, ')</a> | ', logout_link();
					} elseif (empty($SEARCH_SPIDER)) {
						echo login_link();
					}
				?>
				</div></td>
				<td>
					<div id="extra-menu" class="makeMenu"><?php
						$language_menu=WT_MenuBar::getLanguageMenu();
						if ($language_menu) {
							echo $language_menu->getMenuAsList();
						}
						?></div>
					<div id="favdate" align="right" ><?php echo $displayDate; ?></div>
				</td>
				<td width="10"><img src="<?php echo WT_THEME_DIR; ?>images/pixel.gif" width="1" height="1" alt="" /></td></tr></table>
		<?php } ?>

<!-- Begin Toplinks menu section" -->
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#FFFFFF" style="border: 1px solid #84beff">
	<tr>
		<td>
			<div align="center">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		<td width="10">
			&nbsp;
		</td>
		<?php
		$menu=WT_MenuBar::getGedcomMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menu=WT_MenuBar::getMyPageMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menu=WT_MenuBar::getChartsMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menu=WT_MenuBar::getListsMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menu=WT_MenuBar::getCalendarMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menu=WT_MenuBar::getReportsMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menu=WT_MenuBar::getSearchMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		$menus=WT_MenuBar::getModuleMenus();
		foreach ($menus as $menu) {
			if ($menu) {
				echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
			}
		}
		$menu=WT_MenuBar::getHelpMenu();
		if ($menu) {
			echo '<td width="7%" valign="top">', $menu->getMenu(), '</td>';
		}
		?>
		<td width="10">
			&nbsp;
		</td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
</table>
</td></tr></table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-image:url('<?php echo WT_THEME_DIR; ?>images/sombra.gif'); height:4px;">
	<tr>
		<td><img src="<?php echo WT_THEME_DIR; ?>images/pixel.gif" width="1" height="1" alt="" /></td>
	</tr>
</table>
<br />
<!-- close div for div id="header" -->
</div>
<?php } ?>
<!-- end toplinks menu section -->
<!-- begin content section -->
<div id="content">
