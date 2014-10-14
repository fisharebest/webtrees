<?php
// A form to edit site configuration.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'admin_site_config.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->addExternalJavascript(WT_JQUERY_JEDITABLE_URL)
	->addInlineJavascript('jQuery("#tabs").tabs();')
	->setPageTitle(WT_I18N::translate('Site configuration'))
	->pageHeader();

// Lists of options for <select> controls.
$SMTP_SSL_OPTIONS = array(
	'none'=>WT_I18N::translate('none'),
	/* I18N: Secure Sockets Layer - a secure communications protocol*/ 'ssl'=>WT_I18N::translate('ssl'),
	/* I18N: Transport Layer Security - a secure communications protocol */ 'tls'=>WT_I18N::translate('tls'),
);
$SMTP_ACTIVE_OPTIONS = array(
	'internal'=>WT_I18N::translate('Use PHP mail to send messages'),
	'external'=>WT_I18N::translate('Use SMTP to send messages'),
);
$WELCOME_TEXT_AUTH_MODE_OPTIONS = array(
	0 => WT_I18N::translate('No predefined text'),
	1 => WT_I18N::translate('Predefined text that states all users can request a user account'),
	2 => WT_I18N::translate('Predefined text that states admin will decide on each request for a user account'),
	3 => WT_I18N::translate('Predefined text that states only family members can request a user account'),
	4 => WT_I18N::translate('Choose user defined welcome text typed below'),
);

?>
<div id="site-config">
	<div id="tabs">
		<ul>
			<li>
				<a href="#site"><span><?php echo WT_I18N::translate('Site configuration'); ?></span></a>
			</li>
			<li>
				<a href="#mail"><span><?php echo WT_I18N::translate('Mail configuration'); ?></span></a>
			</li>
			<li>
				<a href="#login"><span><?php echo WT_I18N::translate('Login'); ?></span></a>
			</li>
		</ul>
		<div id="site">
			<table>
				<tr>
					<td>
						<dl>
							<dt><?php echo WT_I18N::translate('Data folder'), help_link('INDEX_DIRECTORY'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-INDEX_DIRECTORY', WT_Site::getPreference('INDEX_DIRECTORY'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Memory limit'), help_link('MEMORY_LIMIT'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-MEMORY_LIMIT', WT_Site::getPreference('MEMORY_LIMIT'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('PHP time limit'), help_link('MAX_EXECUTION_TIME'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-MAX_EXECUTION_TIME', WT_Site::getPreference('MAX_EXECUTION_TIME'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Allow users to select their own theme'), help_link('ALLOW_USER_THEMES'); ?></dt>
							<dd><?php echo edit_field_yes_no_inline('site_setting-ALLOW_USER_THEMES', WT_Site::getPreference('ALLOW_USER_THEMES'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Default theme'), help_link('THEME'); ?></dt>
							<dd><?php echo select_edit_control_inline('site_setting-THEME_DIR', array_flip(get_theme_names()), null, WT_Site::getPreference('THEME_DIR'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Show list of family trees'), help_link('ALLOW_CHANGE_GEDCOM'); ?></dt>
							<dd><?php echo edit_field_yes_no_inline('site_setting-ALLOW_CHANGE_GEDCOM', WT_Site::getPreference('ALLOW_CHANGE_GEDCOM'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Session timeout'), help_link('SESSION_TIME'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SESSION_TIME', WT_Site::getPreference('SESSION_TIME'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Website URL'), help_link('SERVER_URL'); ?></dt>
							<dd><?php echo select_edit_control_inline('site_setting-SERVER_URL', array(WT_SERVER_NAME.WT_SCRIPT_PATH=>WT_SERVER_NAME.WT_SCRIPT_PATH), '', WT_Site::getPreference('SERVER_URL'), $controller); ?></dd>
						</dl>
					</td>
				</tr>
			</table>
		</div>
		<div id="mail">
			<table>
				<tr>
					<td>
						<dl>
							<dt><?php echo WT_I18N::translate('Messages'), help_link('SMTP_ACTIVE'); ?></dt>
							<dd><?php echo select_edit_control_inline('site_setting-SMTP_ACTIVE', $SMTP_ACTIVE_OPTIONS, null, WT_Site::getPreference('SMTP_ACTIVE'), $controller); ?></dd>
							<dt><?php echo WT_I18N::translate('Sender name'), help_link('SMTP_FROM_NAME'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SMTP_FROM_NAME', WT_Site::getPreference('SMTP_FROM_NAME'), $controller); ?></dd>
						</dl>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<?php echo WT_I18N::translate('SMTP mail server'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<dl>
							<dt><?php echo WT_I18N::translate('Server name'), help_link('SMTP_HOST'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SMTP_HOST', WT_Site::getPreference('SMTP_HOST'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Port number'), help_link('SMTP_PORT'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SMTP_PORT', WT_Site::getPreference('SMTP_PORT'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Use password'), help_link('SMTP_AUTH'); ?></dt>
							<dd><?php echo edit_field_yes_no_inline('site_setting-SMTP_AUTH', WT_Site::getPreference('SMTP_AUTH'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Username'), help_link('SMTP_AUTH_USER'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SMTP_AUTH_USER', WT_Site::getPreference('SMTP_AUTH_USER'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Password'), help_link('SMTP_AUTH_PASS'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SMTP_AUTH_PASS', '' /* Don't show password.  save.php has special code for this. */, $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Secure connection'), help_link('SMTP_SSL'); ?></dt>
							<dd><?php echo select_edit_control_inline('site_setting-SMTP_SSL', $SMTP_SSL_OPTIONS, null, WT_Site::getPreference('SMTP_SSL'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Sending server name'), help_link('SMTP_HELO'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-SMTP_HELO', WT_Site::getPreference('SMTP_HELO'), $controller); ?></dd>
						</dl>
					</td>
				</tr>
			</table>
			<p>
				<?php echo WT_I18N::translate('To use a Google mail account, use the following settings: server=smtp.gmail.com, port=587, security=tls, username=xxxxx@gmail.com, password=[your gmail password]'); ?>
			</p>
		</div>
		<div id="login">
			<table>
				<tr>
					<td>
						<dl>
							<dt><?php echo WT_I18N::translate('Login URL'), help_link('LOGIN_URL'); ?></dt>
							<dd><?php echo edit_field_inline('site_setting-LOGIN_URL', WT_Site::getPreference('LOGIN_URL'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Welcome text on login page'), help_link('WELCOME_TEXT_AUTH_MODE'); ?></dt>
							<dd><?php echo select_edit_control_inline('site_setting-WELCOME_TEXT_AUTH_MODE', $WELCOME_TEXT_AUTH_MODE_OPTIONS, null, WT_Site::getPreference('WELCOME_TEXT_AUTH_MODE'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Custom welcome text'), ' — ', WT_LOCALE, help_link('WELCOME_TEXT_AUTH_MODE_CUST'); ?></dt>
							<dd><?php echo edit_text_inline('site_setting-WELCOME_TEXT_AUTH_MODE_4', WT_Site::getPreference('WELCOME_TEXT_AUTH_MODE_'.WT_LOCALE), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Allow visitors to request account registration'), help_link('USE_REGISTRATION_MODULE'); ?></dt>
							<dd><?php echo edit_field_yes_no_inline('site_setting-USE_REGISTRATION_MODULE', WT_Site::getPreference('USE_REGISTRATION_MODULE'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Require an administrator to approve new user registrations'), help_link('REQUIRE_ADMIN_AUTH_REGISTRATION'); ?></dt>
							<dd><?php echo edit_field_yes_no_inline('site_setting-REQUIRE_ADMIN_AUTH_REGISTRATION', WT_Site::getPreference('REQUIRE_ADMIN_AUTH_REGISTRATION'), $controller); ?></dd>

							<dt><?php echo WT_I18N::translate('Show acceptable use agreement on “Request new user account” page'), help_link('SHOW_REGISTER_CAUTION'); ?></dt>
							<dd><?php echo edit_field_yes_no_inline('site_setting-SHOW_REGISTER_CAUTION', WT_Site::getPreference('SHOW_REGISTER_CAUTION'), $controller); ?></dd>
						</dl>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
