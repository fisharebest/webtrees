<?php
// Animated Sidebar for the Individual Page
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//
// @version $Id$

if (!defined('WT_SCRIPT_NAME')) define('WT_SCRIPT_NAME', 'sidebar.php');
require_once('includes/session.php');

$sidebarmods = WT_Module::getActiveSidebars();
if (!$sidebarmods) {
	return;
}

$sb_action = safe_GET('sb_action', WT_REGEX_ALPHANUM, 'none');
//-- handle ajax calls
if ($sb_action!='none') {
	header('Content-type: text/html; charset=UTF-8');
	class tempController {
		var $pid;
		var $famid;
	}

	$controller = new tempController();

	$pid = safe_GET_xref('pid', '');
	if (empty($pid)) $pid = safe_POST_xref('pid', '');
	if (!empty($pid)) {
		$controller->pid = $pid;
	}
	$pid = safe_GET_xref('rootid',  '');
	if (empty($pid)) $pid = safe_POST_xref('rootid', '');
	if (!empty($pid)) {
		$controller->pid = $pid;
	}
	$famid = safe_GET('famid', WT_REGEX_XREF, '');
	if (empty($famid)) $famid = safe_POST('famid', WT_REGEX_XREF, '');
	if (!empty($famid)) {
		$controller->famid = $famid;
	}
	$sid = safe_GET('sid', WT_REGEX_XREF, '');
	if (empty($sid)) $sid = safe_POST('sid', WT_REGEX_XREF, '');
	if (!empty($sid)) {
		$controller->sid = $sid;
	}

	if ($sb_action=='loadMods') {
		$counter = 0;
		foreach ($sidebarmods as $mod) {
			if (isset($controller)) $mod->setController($controller);
			if ($mod->hasSidebarContent()) {
				?>
				<h3 title="<?php echo $mod->getName(); ?>"><a href="#"><?php echo '<span title="', $mod->getTitle(), '">', $mod->getTitle(), '</span>'; ?></a></h3>
				<div id="sb_content_<?php echo $mod->getName(); ?>">
				<?php if ($counter==0) echo $mod->getSidebarContent();
				else { ?><img src="<?php echo WT_THEME_DIR; ?>images/loading.gif" /><?php } ?>
				</div>
				<?php
				$counter++;
			}
		}
		exit;
	}
	if ($sb_action=='loadmod') {
		$modName = safe_GET('mod', WT_REGEX_URL, '');
		if (isset($sidebarmods[$modName])) {
			$mod = $sidebarmods[$modName];
			if (isset($controller)) $mod->setController($controller);
			echo $mod->getSidebarContent();
		}
		exit;
	}
	if (isset($sidebarmods[$sb_action])) {
		$mod = $sidebarmods[$sb_action];
		echo $mod->getSidebarAjaxContent();
	}
	exit;
}

global $controller;
$pid='';
$famid='';
if (isset($controller)) {
	if (isset($controller->pid)) $pid = $controller->pid;
	if (isset($controller->rootid)) $pid = $controller->rootid;
	if (isset($controller->famid)) $famid = $controller->famid;
	if (isset($controller->sid)) $pid = $controller->sid;
} else {
	$pid = safe_GET_xref('pid', '');
	if (empty($pid)) $pid = safe_POST_xref('pid', '');
	if (empty($pid)) $pid = safe_GET_xref('rootid',  '');
	if (empty($pid)) $pid = safe_POST_xref('rootid', '');
	if (empty($pid)) $pid = safe_POST_xref('sid', '');
	if (empty($pid)) $pid = safe_GET_xref('sid', '');
	$famid = safe_GET('famid', WT_REGEX_XREF, '');
	if (empty($famid)) $famid = safe_POST('famid', WT_REGEX_XREF, '');
}

	
echo '<div id="sidebarAccordion">';
	$counter = 0;
	foreach ($sidebarmods as $mod) {
		if (isset($controller)) $mod->setController($controller);
		if ($mod->hasSidebarContent()) {
			?>
			<h3 title="<?php echo $mod->getName(); ?>"><a href="#"><?php echo $mod->getTitle(); ?></a></h3>
			<div id="sb_content_<?php echo $mod->getName(); ?>">
			<?php  echo $mod->getSidebarContent();?>
			</div>
			<?php
			$counter++;
		}
	}
echo '</div>';
echo WT_JS_START,'jQuery("#sidebarAccordion").accordion({active:0, icons:false, autoHeight: false, collapsible: true});', WT_JS_END;