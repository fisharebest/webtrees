<?php
/*
 * 	Header for the JustLight theme
 *
 *  webtrees: Web based Family History software
 *  Copyright (C) 2014 webtrees development team.
 *  Copyright (C) 2014 JustCarmen.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

use WT\Auth;

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
define('WT_GOOGLE_SOCIALTRACKING',  './js/ga_social_tracking.js');

// This theme uses extra javascript
$this
	->addExternalJavascript(WT_GOOGLE_SOCIALTRACKING)
	->addExternalJavascript(WT_JQUERY_COLORBOX_URL)
	->addExternalJavascript(WT_JQUERY_WHEELZOOM_URL)
	->addExternalJavascript(JL_THEME_URL . 'js/jquery.waituntilexists.min.js')
	->addExternalJavascript(JL_BOOTSTRAP_URL . 'js/bootstrap.min.js')
	->addExternalJavascript(JL_BOOTSTRAP_URL . 'js/justlight.bootstrap.js')
	->addExternalJavascript(JL_THEME_URL . 'js/justlight.js')
	->addExternalJavascript(JL_COLORBOX_URL . 'justlight.colorbox.js')
	->addInlineJavascript('
		// load bootstrap datatable script
		if(jQuery(".dataTable").length){
			var script=document.createElement("script");
			script.type="text/javascript";
			script.src="' . JL_BOOTSTRAP_URL . 'js/dataTables.bootstrap.js";
			document.body.appendChild(script);
		}
	');
?>
<!DOCTYPE html>
<html <?php echo WT_I18N::html_markup(); ?>>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php echo include(WT_ROOT."/keywords.php")?>
		<?php echo header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL); ?>
		<title><?php echo WT_Filter::escapeHtml($title); ?></title>
		<link rel="icon" href="<?php echo WT_CSS_URL; ?>favicon.png" type="image/png">
		<link rel="stylesheet" type="text/css" href="<?php echo JL_JQUERY_UI_CSS; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo JL_COLORBOX_URL; ?>colorbox.css">
		<link href="<?php echo JL_BOOTSTRAP_URL; ?>css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo JL_BOOTSTRAP_URL; ?>css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="<?php echo JL_BOOTSTRAP_URL; ?>css/dataTables.bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>style.css">
		<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>justlight.css">
		<link rel="stylesheet" type="text/css" href="<?php echo WT_CSS_URL; ?>treeview.css">
		<!--[if IE 8]>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
		<![endif]-->
	</head>
	<body id="body">
		<div id="wrap">
			<?php if ($view != 'simple'): ?>
				<?php getJLScriptVars(); ?>
				<header>
					<div id="nav-container" class="navbar navbar-default navbar-fixed-top">
						<div class="navbar-inner">
							<div class="container-fluid">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
								<div class="navbar-header">
									<h1><a href="index.php" class="navbar-brand"><?php echo WT_TREE_TITLE; ?></a></h1>
								</div>

								<div class="navbar-collapse collapse navbar-top">
									<div class="div_search">
										<form action="search.php" method="post">
											<input type="hidden" name="action" value="general" />
											<input type="hidden" name="topsearch" value="yes" />
											<input type="search" name="query" id="searc-basic" placeholder="<?php echo WT_I18N::translate('Search'); ?>" dir="auto" />
											<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
										</form>
									</div>
									<div class="navbar-right">
										<?php
										echo JL_TopMenu::getFavoritesMenu();
										if (WT_GED_ID && !$SEARCH_SPIDER && WT_Site::getPreference('ALLOW_USER_THEMES') && $WT_TREE->getPreference('ALLOW_THEME_DROPDOWN')) {
											echo JL_TopMenu::getTopMenu(WT_I18N::translate('Theme'), "themes");
										}
										echo JL_TopMenu::getTopMenu(WT_I18N::translate('Language'), "languages");
										if (Auth::check()) {
											echo JL_TopMenu::getTopMenu(WT_I18N::translate('My account'), 'login');
										} else {
											?>
											<a href="login.php" class="btn btn-default">Login</a>
										<?php } ?>
									</div><!--/.navbar-right -->
								</div><!--/.nav-collapse -->

								<div class="navbar-collapse collapse">
									<div class="navbar-text">
										<ul class="nav nav-pills" role="tablist">
											<?php echo getJLNavMenu(); ?>
										</ul>
									</div><!-- /.navbar-text -->
									<div style="margin:auto;padding:10px;width:300px;"><div class="g-follow" data-annotation="bubble" data-height="20" data-href="//plus.google.com/u/0/106237023746635567418" data-rel="publisher"></div></div>
								</div><!--/.nav-collapse -->
							</div><!-- /.container-fluid -->
						</div><!-- /.navbar-inner -->
					</div><!-- /.navbar -->
					<?php if (exists_pending_change()): ?>
						<a class="pending-changes-message" href="#" onclick="window.open('edit_changes.php', '_blank', chan_window_specs);
								return false;">
							<p class="alert alert-warning"><?php echo WT_I18N::translate('There are pending changes for you to moderate.') ?></p>
						</a>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<?php echo WT_FlashMessages::getHtmlMessages(); ?>
			<?php echo getJLMediaList(); ?>

			<?php
			if (WT_Filter::get('action') === 'addnewnote_assisted') {
				$style = 'style="width: 100%"';
				$this->addInlineJavascript('jQuery("#edit_interface-page").addClass("census-assistant")');
			} else {
				$style = WT_SCRIPT_NAME === 'individual.php' || WT_SCRIPT_NAME === 'family.php' || WT_SCRIPT_NAME === 'medialist.php' || WT_Filter::get('mod_action') === 'treeview' ? ' style="width: 98%"' : '';
			}
			?>
			<div id="responsive"></div><main id="content" class="container"<?php echo $style ?>>