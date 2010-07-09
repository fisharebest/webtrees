<?php
/**
 * PGV to webtrees transfer wizard
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @version $Id: pgv_to_wt.php 9030 2010-07-07 21:54:31Z greg $
 */

define('WT_SCRIPT_NAME', 'pgv_to_wt.php.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// style elements

if (!WT_USER_GEDCOM_ADMIN) {
	if (WT_USER_ID) {
		header("Location: index.php");
		exit;
	} else {
		header("Location: login.php?url=pgv_to_wt.php");
		exit;
	}
}

print_header(i18n::translate('PGV to webtrees transfer wizard'));
echo
	'<style type="text/css">
		#container {width: 70%; margin:15px auto; border: 1px solid gray; padding: 10px;}
		#container dl {margin:0 0 40px 25px;}
		#container dt {display:inline; width: 320px; font-weight:normal;}
		#container dd {color: #81A9CB; margin-bottom:20px;font-weight:bold;}
		#container p {color: #81A9CB; font-size: 14px; font-style: italic; font-weight:bold; padding: 0 5px 5px; align: top;
		h2 {color: #81A9CB;}
		.good {color: green;}
		.bad {color: red; font-weight: bold;}
		.indifferent {color: blue;}
	</style>';

// start of content proper
echo '<div id="container">';
	echo '<h2>', i18n::translate('PGV to webtrees transfer wizard'), '</h2>';
	// Check pre-requisites
	echo
		'<p>', i18n::translate('Minimum requirements (tick box to confirm):'), '</p>',
		'<dl>',
			'<dt>', i18n::translate('<b>webtrees</b> database must be on the same server as PGV\'s'), '</dt>',
				'<dd>', checkbox('server', get_user_setting(WT_USER_ID, 'server')),'</dd>',
			'<dt>', i18n::translate('PGV must be version 4.2.3, or any SVN up to #6973'), '</dt>',
				'<dd>', checkbox('version', get_gedcom_setting(WT_GED_ID, 'version')),'</dd>',
			'<dt>', i18n::translate('All changes in PGV must be accepted'), '</dt>',
				'<dd>', checkbox('changes', get_gedcom_setting(WT_GED_ID, 'changes')), '</dd>',
			'<dt>', i18n::translate('You must export your latest GEDCOM data'), '</dt>',
				'<dd>', checkbox('export', get_gedcom_setting(WT_GED_ID, 'export')), '</dd>',
			'<dt>', i18n::translate('The current <b>webtrees</b> admin username must be the same as an existing PGV admin username'), '</dt>',
				'<dd>', checkbox('user', get_gedcom_setting(WT_GED_ID, 'user')), '</dd>',
			'<dt>', i18n::translate('All existing PGV users must have distinct email addresses'), '</dt>',
				'<dd>', checkbox('email', get_gedcom_setting(WT_GED_ID, 'email')), '</dd>',
		'</dl>';
	// Get basic details
	echo
		'<p>', i18n::translate('Essential details (change as necessary):'), '</p>',
		'<dl>',
			'<dt>',i18n::translate('PGV Database name'), '</dt>',
				'<dd><input type="text" name="dbname" value="phpgedview"><dd>',
			'<dt>',i18n::translate('PGV Table prefix'), '</dt>',
				'<dd><input type="text" name="tblpfx" value="pgv_"><dd>',
			'<dt>',i18n::translate('PGV path to index directory'), '</dt>',
				'<dd><input type="text" name="path" value="/index"><dd>',
		'</dl>';
	// Get media options
	echo
		'<p>', i18n::translate('Media item options (select one):'), '</p>',
		'<dl>',
			'<dt>',i18n::translate('Use existing PGV media directory for <b>webtrees</b>'), '</dt>',
				'<dd>', edit_field_yes_no('media', get_gedcom_setting(WT_GED_ID, 'media')), '</dd>',
			'<dt>',i18n::translate('Copy media from PGV media directory to <b>webtrees</b> media directory'), '</dt>',
				'<dd>', edit_field_yes_no('media', get_gedcom_setting(WT_GED_ID, 'media')), '</dd>',
			'<dt>',i18n::translate('Move media from PGV media directory to <b>webtrees</b> media directory'), '</dt>',
				'<dd>', edit_field_yes_no('media', get_gedcom_setting(WT_GED_ID, 'media')), '</dd>',
		'</dl>';
	// Finish
	echo '<div class="center"><input type="submit" value="'.i18n::translate('Finish').'"></div>';
echo '</div>';