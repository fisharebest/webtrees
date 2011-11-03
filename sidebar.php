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
// $Id$

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

	if ($sb_action=='loadMods') {
		$counter = 0;
		foreach ($sidebarmods as $mod) {
			if ($mod->hasSidebarContent()) {
				echo '<h3 id="', $mod->getName(), '"><a href="#">', $mod->getTitle(), '</a></h3>',
						'<div id="sb_content_', $mod->getName(), '">';
				if ($counter==0) {
					echo $mod->getSidebarContent();
				} else {
					echo '<img src="', WT_THEME_URL, 'images/loading.gif" />';
				}
				echo '</div>';
				$counter++;
			}
		}
		exit;
	}
	if ($sb_action=='loadmod') {
		$modName = safe_GET('mod', WT_REGEX_URL, '');
		if (isset($sidebarmods[$modName])) {
			$mod = $sidebarmods[$modName];
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

echo WT_JS_START; //jQuery code to remove table elements from INDI facts
?>
jQuery(document).ready(function() {
	jQuery('#sb_content_extra_info table').replaceWith(function() { return jQuery(this).contents(); });
	jQuery('#sb_content_extra_info tbody').replaceWith(function() { return jQuery(this).contents(); });
	jQuery('#sb_content_extra_info tr').replaceWith(function() { return jQuery(this).contents();	});
	jQuery('#sb_content_extra_info td').replaceWith(function() { return jQuery(this).contents();	});
});
<?php
echo WT_JS_END;

$counter = 0;
echo '<div id="sidebarAccordion">';
foreach ($sidebarmods as $mod) {
	if ($mod->hasSidebarContent()) {
			echo '<h3 id="', $mod->getName(), '"><a href="#">', $mod->getTitle(), '</a></h3>',
				'<div id="sb_content_', $mod->getName(), '">', $mod->getSidebarContent(), '</div>';
			$counter++;
	}
}
echo '</div>';
echo WT_JS_START,'jQuery("#sidebarAccordion").accordion({active:"#family_nav", autoHeight: false, collapsible: true, icons:{ "header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" }});', WT_JS_END;
