<?php
/**
 * A form to edit site configuration.
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
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'admin_site_config.php');

require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(WT_I18N::translate('Server configuration'));

echo
	// Display the config items inline, rather than using a form.
	'<table class="site_config"><tr>',
	'<th>', WT_I18N::translate('Server configuration'), '</th><th>&nbsp;</th>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Data file directory'), help_link('INDEX_DIRECTORY'), '</td>',
	'<td>', edit_field_inline('site_setting-INDEX_DIRECTORY', get_site_setting('INDEX_DIRECTORY')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Memory limit'), help_link('MEMORY_LIMIT'), '</td>',
	'<td>', edit_field_inline('site_setting-MEMORY_LIMIT', get_site_setting('MEMORY_LIMIT')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('PHP time limit'), help_link('MAX_EXECUTION_TIME'), '</td>',
	'<td>', edit_field_inline('site_setting-MAX_EXECUTION_TIME', get_site_setting('MAX_EXECUTION_TIME')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Allow messages to be stored online'), help_link('STORE_MESSAGES'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-STORE_MESSAGES', get_site_setting('STORE_MESSAGES')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Allow visitors to request account registration'), help_link('USE_REGISTRATION_MODULE'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-USE_REGISTRATION_MODULE', get_site_setting('USE_REGISTRATION_MODULE')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Require an administrator to approve new user registrations'), help_link('REQUIRE_ADMIN_AUTH_REGISTRATION'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-REQUIRE_ADMIN_AUTH_REGISTRATION', get_site_setting('REQUIRE_ADMIN_AUTH_REGISTRATION')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Allow users to select their own theme'), help_link('ALLOW_USER_THEMES'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-ALLOW_USER_THEMES', get_site_setting('ALLOW_USER_THEMES')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Default Theme'), help_link('THEME'), '</td>',
	'<td>', select_edit_control_inline('site_setting-THEME', array_flip(get_theme_names()), null, get_site_setting('THEME')),
	'</tr><tr>',
	'<td>', WT_I18N::translate('Allow GEDCOM switching'), help_link('ALLOW_CHANGE_GEDCOM'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-ALLOW_CHANGE_GEDCOM', get_site_setting('ALLOW_CHANGE_GEDCOM')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Session timeout'), help_link('SESSION_TIME'), '</td>',
	'<td>', edit_field_inline('site_setting-SESSION_TIME', get_site_setting('SESSION_TIME')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Website URL'), help_link('SERVER_URL'), '</td>',
	'<td>', edit_field_inline('site_setting-SERVER_URL', get_site_setting('SERVER_URL')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Login URL'), help_link('LOGIN_URL'), '</td>',
	'<td>', edit_field_inline('site_setting-LOGIN_URL', get_site_setting('LOGIN_URL')), '</td>',
	'</tr><tr>',
	'<th>', WT_I18N::translate('SMTP mail configuration'), '</th><th>&nbsp;</th>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Messages'), help_link('SMTP_ACTIVE'), '</td>',
	'<td>', select_edit_control_inline('site_setting-SMTP_ACTIVE', array('internal'=>WT_I18N::translate('Use PHP mail to send messages'), 'external'=>WT_I18N::translate('Use SMTP to send messages'), 'disabled'=>WT_I18N::translate('Do not send messages')), null, get_site_setting('SMTP_ACTIVE')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Server'), help_link('SMTP_HOST'), '</td>',
	'<td>', edit_field_inline('site_setting-SMTP_HOST', get_site_setting('SMTP_HOST')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Port'), help_link('SMTP_PORT'), '</td>',
	'<td>', edit_field_inline('site_setting-SMTP_PORT', get_site_setting('SMTP_PORT')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Use simple mail headers'), help_link('SMTP_SIMPLE_MAIL'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-SMTP_SIMPLE_MAIL', get_site_setting('SMTP_SIMPLE_MAIL')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Use password'), help_link('SMTP_AUTH'), '</td>',
	'<td>', edit_field_yes_no_inline('site_setting-SMTP_AUTH', get_site_setting('SMTP_AUTH')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Username'), help_link('SMTP_AUTH_USER'), '</td>',
	'<td>', edit_field_inline('site_setting-SMTP_AUTH_USER', get_site_setting('SMTP_AUTH_USER')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Password'), help_link('SMTP_AUTH_PASS'), '</td>',
	'<td>', edit_field_inline('site_setting-SMTP_AUTH_PASS', get_site_setting('SMTP_AUTH_PASS')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Security'), help_link('SMTP_SSL'), '</td>',
	'<td>', select_edit_control_inline('site_setting-SMTP_SSL', array('none'=>WT_I18N::translate('none'), 'ssl'=>WT_I18N::translate('ssl'), 'tls'=>WT_I18N::translate('tls')), null, get_site_setting('SMTP_SSL')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('From email address'), help_link('SMTP_FROM_NAME'), '</td>',
	'<td>', edit_field_inline('site_setting-SMTP_FROM_NAME', get_site_setting('SMTP_FROM_NAME')), '</td>',
	'</tr><tr>',
	'<td>', WT_I18N::translate('Sender email address'), help_link('SMTP_HELO'), '</td>',
	'<td>', edit_field_inline('site_setting-SMTP_HELO', get_site_setting('SMTP_HELO')), '</td>',
	'</tr></table>';

print_footer();
