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

print_header(WT_I18N::translate('Site configuration'));

// "Help for this page" link
echo '<div id="page_help">', help_link('help_editconfig.php'), '</div>';

echo WT_JS_START;
?>
jQuery(document).ready(function() {
jQuery("#tabs").tabs();
});
<?php 
echo WT_JS_END;

echo
	// Display the config items inline, rather than using a form.
	'<table class="site_config">',
		'<tr>',
			'<td>',
				'<div id="tabs">',
					'<ul>',
						'<li><a href="#site"><span>', WT_I18N::translate('Site configuration'), '</span></a></li>',
						'<li><a href="#mail"><span>', WT_I18N::translate('Mail configuration'), '</span></a></li>',
					'</ul>',
					'<div id="site"><table><tr><td><dl>',
					'<dt>', WT_I18N::translate('Data file directory'), help_link('INDEX_DIRECTORY'), '</dt>',
					'<dd>', edit_field_inline('site_setting-INDEX_DIRECTORY', get_site_setting('INDEX_DIRECTORY')), '</dd>',
					'<dt>', WT_I18N::translate('Memory limit'), help_link('MEMORY_LIMIT'), '</dt>',
					'<dd>', edit_field_inline('site_setting-MEMORY_LIMIT', get_site_setting('MEMORY_LIMIT')), '</dd>',
					'<dt>', WT_I18N::translate('PHP time limit'), help_link('MAX_EXECUTION_TIME'), '</dt>',
					'<dd>', edit_field_inline('site_setting-MAX_EXECUTION_TIME', get_site_setting('MAX_EXECUTION_TIME')), '</dd>',
					'<dt>', WT_I18N::translate('Allow messages to be stored online'), help_link('STORE_MESSAGES'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-STORE_MESSAGES', get_site_setting('STORE_MESSAGES')), '</dd>',
					'<dt>', WT_I18N::translate('Allow visitors to request account registration'), help_link('USE_REGISTRATION_MODULE'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-USE_REGISTRATION_MODULE', get_site_setting('USE_REGISTRATION_MODULE')), '</dd>',
					'<dt>', WT_I18N::translate('Require an administrator to approve new user registrations'), help_link('REQUIRE_ADMIN_AUTH_REGISTRATION'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-REQUIRE_ADMIN_AUTH_REGISTRATION', get_site_setting('REQUIRE_ADMIN_AUTH_REGISTRATION')), '</dd>',
					'<dt>', WT_I18N::translate('Allow users to select their own theme'), help_link('ALLOW_USER_THEMES'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-ALLOW_USER_THEMES', get_site_setting('ALLOW_USER_THEMES')), '</dd>',
					'<dt>', WT_I18N::translate('Default Theme'), help_link('THEME'), '</dt>',
					'<dd>', select_edit_control_inline('site_setting-THEME_DIR', array_flip(get_theme_names()), null, get_site_setting('THEME_DIR')), '</dd>',
					'<dt>', WT_I18N::translate('Allow GEDCOM switching'), help_link('ALLOW_CHANGE_GEDCOM'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-ALLOW_CHANGE_GEDCOM', get_site_setting('ALLOW_CHANGE_GEDCOM')), '</dd>',
					'<dt>', WT_I18N::translate('Session timeout'), help_link('SESSION_TIME'), '</dt>',
					'<dd>', edit_field_inline('site_setting-SESSION_TIME', get_site_setting('SESSION_TIME')), '</dd>',
					'<dt>', WT_I18N::translate('Website URL'), help_link('SERVER_URL'), '</dt>',
					'<dd>', select_edit_control_inline('site_setting-SERVER_URL', array(WT_SERVER_NAME.WT_SCRIPT_PATH=>WT_SERVER_NAME.WT_SCRIPT_PATH), '', get_site_setting('SERVER_URL')), '</dd>',
					'<dt>', WT_I18N::translate('Login URL'), help_link('LOGIN_URL'), '</dt>',
					'<dd>', edit_field_inline('site_setting-LOGIN_URL', get_site_setting('LOGIN_URL')), '</dd>',
					'</dl></td></tr></table></div>',
					'<div id="mail"><table><tr><td><dl>',
					'<dt>', WT_I18N::translate('Messages'), help_link('SMTP_ACTIVE'), '</dt>',
					'<dd>', select_edit_control_inline('site_setting-SMTP_ACTIVE', array('internal'=>WT_I18N::translate('Use PHP mail to send messages'), 'external'=>WT_I18N::translate('Use SMTP to send messages'), 'disabled'=>WT_I18N::translate('Do not send messages')), null, get_site_setting('SMTP_ACTIVE')), '</dd>',
					'<dt>', WT_I18N::translate('Server'), help_link('SMTP_HOST'), '</dt>',
					'<dd>', edit_field_inline('site_setting-SMTP_HOST', get_site_setting('SMTP_HOST')), '</dd>',
					'<dt>', WT_I18N::translate('Port'), help_link('SMTP_PORT'), '</dt>',
					'<dd>', edit_field_inline('site_setting-SMTP_PORT', get_site_setting('SMTP_PORT')), '</dd>',
					'<dt>', WT_I18N::translate('Use simple mail headers'), help_link('SMTP_SIMPLE_MAIL'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-SMTP_SIMPLE_MAIL', get_site_setting('SMTP_SIMPLE_MAIL')), '</dd>',
					'<dt>', WT_I18N::translate('Use password'), help_link('SMTP_AUTH'), '</dt>',
					'<dd>', edit_field_yes_no_inline('site_setting-SMTP_AUTH', get_site_setting('SMTP_AUTH')), '</dd>',
					'<dt>', WT_I18N::translate('Username'), help_link('SMTP_AUTH_USER'), '</dt>',
					'<dd>', edit_field_inline('site_setting-SMTP_AUTH_USER', get_site_setting('SMTP_AUTH_USER')), '</dd>',
					'<dt>', WT_I18N::translate('Password'), help_link('SMTP_AUTH_PASS'), '</dt>',
					// Don't show password.  save.php has special code for this.
					'<dd>', edit_field_inline('site_setting-SMTP_AUTH_PASS', ''), '</dd>',
					'<dt>', WT_I18N::translate('Security'), help_link('SMTP_SSL'), '</dt>',
					'<dd>', select_edit_control_inline('site_setting-SMTP_SSL', array('none'=>WT_I18N::translate('none'), 'ssl'=>WT_I18N::translate('ssl'), 'tls'=>WT_I18N::translate('tls')), null, get_site_setting('SMTP_SSL')), '</dd>',
					'<dt>', WT_I18N::translate('From email address'), help_link('SMTP_FROM_NAME'), '</dt>',
					'<dd>', edit_field_inline('site_setting-SMTP_FROM_NAME', get_site_setting('SMTP_FROM_NAME')), '</dd>',
					'<dt>', WT_I18N::translate('Sender email address'), help_link('SMTP_HELO'), '</dt>',
					'<dd>', edit_field_inline('site_setting-SMTP_HELO', get_site_setting('SMTP_HELO')), '</dd>',
					'</dl></td></tr></table></div>',
				'</div>',
			'</td>',
		'</tr>',
	'</table>';

print_footer();
