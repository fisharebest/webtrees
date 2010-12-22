<?php
/**
 * Administrative User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team
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
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'admin.php');
require './includes/session.php';

if (!WT_USER_GEDCOM_ADMIN) {
	if (WT_USER_ID) {
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
		exit;
	} else {
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
		exit;
	}
}

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];

if (!isset($action)) $action="";

$pending_changes=WT_DB::prepare("SELECT 1 FROM `##change` WHERE status='pending' LIMIT 1")->fetchOne();
if ($pending_changes) {
	$d_wt_changes = "<a href=\"javascript:;\" onclick=\"window.open('edit_changes.php','_blank','width=600,height=500,resizable=1,scrollbars=1'); return false;\">".i18n::translate('Moderate pending changes').help_link('edit_changes.php')."</a>";
} else {
	$d_wt_changes = '&nbsp;';
}

$verify_msg = false;
$warn_msg = false;
foreach (get_all_users() as $user_id=>$user_name) {
	if (!get_user_setting($user_id, 'verified_by_admin') && get_user_setting($user_id, 'verified'))  {
		$verify_msg = true;
	}
	$comment_exp=get_user_setting($user_id, 'comment_exp');
	if (!empty($comment_exp) && (strtotime($comment_exp) != "-1") && (strtotime($comment_exp) < time("U"))) {
		$warn_msg = true;
	}
	if ($verify_msg && $warn_msg) {
		break;
	}
}

print_header(i18n::translate('Administration'));
echo
	WT_JS_START,
	'function showchanges() {window.location.reload();}',
  'jQuery(document).ready(function() {',
  ' jQuery("#tabs").tabs();',
  '});',
	'function manageservers() {',
	' window.open("manageservers.php", "", "top=50,left=50,width=700,height=500,scrollbars=1,resizable=1");',
	'}',
	WT_JS_END,
	'<div class="center">',
	'<h2>', i18n::translate('Administration'), ' - ', WT_WEBTREES, ' ', WT_VERSION_TEXT, '</h2>',
	'<p>',
	i18n::translate('Current Server Time:'), ' ', format_timestamp(time()),
	"<br />".
	i18n::translate('Current User Time:'), ' ', format_timestamp(client_time()),
	'</p>';

if (WT_USER_IS_ADMIN) {
	if ($verify_msg) {
		echo "<p>";
		echo "<a href=\"useradmin.php?action=listusers&amp;filter=admunver"."\" class=\"error\">".i18n::translate('User accounts awaiting verification by admin')."</a>";
		echo "</p>";
	}
	if ($warn_msg) {
		echo "<p>";
		echo "<a href=\"useradmin.php?action=listusers&amp;filter=warnings"."\" class=\"error\" >".i18n::translate('One or more user accounts have warnings')."</a>";
		echo "</p>";
	}
}

echo
	'</div>',
	'<div id="tabs" class="width100">',
	'<!-- Tabs -->', 
	'<ul>',
	'<li><a href="#gedcom"><span>', i18n::translate('Family tree administration'), '</span></a></li>';

if (WT_USER_IS_ADMIN) {
	echo '<li><a href="#site"><span>', i18n::translate('Site administration'), '</span></a></li>';
	$modules = WT_Module::getInstalledModules();
	if ($modules) {
		echo
			'<li><a href="#modules" onclick="window.location=\'module_admin.php\';">',
			i18n::translate('Module administration'),
			'</a></li>';
	}
	echo
		'<li><a href="#users" onclick="window.location=\'useradmin.php\';">',
		i18n::translate('User administration'),
		'</a></li>',
		'<li><a href="#multimedia" onclick="window.location=\'media.php\';">',
		i18n::translate('Manage multimedia'),
		'</a></li>';
}

echo
	'</ul>',
	'<!-- GEDCOM admin -->',
	'<div id="gedcom">',
	'<table class="center ', $TEXT_DIRECTION, ' width100">',
	'<tr>',
	'<td colspan="2" class="topbottombar" style="text-align:center; ">', i18n::translate('Family tree administration'), '</td>',
	'</tr>',
	'<tr>',
	'<td class="optionbox width50"><a href="editgedcoms.php">',
	i18n::translate('GEDCOM administration'), '</a>', help_link('gedcom_administration'), '</td>',
	'<td class="optionbox with50">',
	'<a href="javascript:;" onclick="addnewchild(\'\'); return false;">', i18n::translate('Add an unlinked person'), '</a>',
	help_link('edit_add_unlinked_person'),
	'</td>',
	'</tr>',
	'<tr>',
	'<td class="optionbox width50">';

if (WT_USER_CAN_EDIT) {
	echo '<a href="module.php?mod=batch_update&mod_action=admin_batch_update">', i18n::translate('Batch Update'), '</a>', help_link('help_batch_update.php');
} else {
	echo '&nbsp;';
}

echo '</td>';

echo
	'<td class="optionbox with50">',
	'<a href="javascript:;" onclick="addnewnote(\'\'); return false;">', i18n::translate('Add an unlinked note'), '</a>',
	help_link('edit_add_unlinked_note'),
	'</td>',
	'</tr>',
	'<tr>',
	'<td class="optionbox width50"><a href="edit_merge.php">',
	i18n::translate('Merge records'), '</a>', help_link('edit_merge'),
	'</td>',
	'<td class="optionbox width50">',
	'<a href="javascript:;" onclick="addnewsource(\'\'); return false;">', i18n::translate('Add an unlinked source'), '</a>',
	help_link('edit_add_unlinked_source'),
	'</td>',
	'</tr>';

if ($pending_changes) {
	echo '<tr><td colspan="2" class="optionbox">', $d_wt_changes, '</td></tr>';
}

echo
	'</table>',
	'</div>';

if (WT_USER_IS_ADMIN) {
	echo
		'<!-- Site admin -->',
		'<div id="site">',
		'<table class="center ', $TEXT_DIRECTION, ' width100">',
		'<tr>',
		'<td colspan="2" class="topbottombar" style="text-align:center; ">', i18n::translate('Site administration'), '</td>',
		'</tr>',
		'<tr>',
		'<td class="optionbox width50"><a	href="siteconfig.php">', i18n::translate('Configuration'), '</a>', help_link('help_editconfig.php'), '</td>',
		'<td class="optionbox width50"><a href="manageservers.php">', i18n::translate('Manage sites'), '</a>', help_link('help_managesites'), '</td>',
		'</tr>',
		'<tr>',
		'<td class="optionbox width50">',
		'<a href="readme.html" target="manual">', i18n::translate('README documentation'), '</a>',
		'</td>',
		'<td class="optionbox width50">',
		'<a href="logs.php">', i18n::translate('Logs'), '</a>', help_link('logs.php'),
		'</td>',
		'</tr>',
		'<tr>',
		'<td class="optionbox width50">',
		'<a href="dir_editor.php">', i18n::translate('Cleanup data directory'), '</a>', help_link('help_dir_editor.php'),
		'</td>',
		'<td class="optionbox width50">',
		'<a href="wtinfo.php?action=phpinfo">', i18n::translate('PHP information'), '</a>', help_link('phpinfo'),
		'</td>',
		'</tr>',
		'</table>',
		'</div>',
		'<!-- Module admin -->';
	
	if ($modules) {
		echo '<div id="modules">', i18n::translate('Loading...'), '</div>';
	}
	echo '<div id="users">', i18n::translate('Loading...'), '</div>';
	echo '<div id="multimedia">', i18n::translate('Loading...'), '</div>';
}
echo '</div></td></tr></table>';
print_footer();
