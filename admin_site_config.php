<?php
// A form to edit site configuration.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'admin_site_config.php');
require './includes/session.php';

$controller=new WT_Controller_Page();
$controller
	->requireAdminLogin()
	->addExternalJavascript(WT_JQUERY_JEDITABLE_URL)
	->addInlineJavascript('jQuery("#tabs").tabs();')
	->setPageTitle(WT_I18N::translate('Site configuration'))
	->pageHeader();

require WT_ROOT.'includes/functions/functions_edit.php';

echo
	// Display the config items inline, rather than using a form.
	'<div id="site-config">',
		'<div id="tabs">',
			'<ul>',
				'<li><a href="#site"><span>', WT_I18N::translate('Site configuration'), '</span></a></li>',
				'<li><a href="#mail"><span>', WT_I18N::translate('Mail configuration'), '</span></a></li>',
			'</ul>',
			'<div id="site"><table><tr><td><dl>',
			'<dt>', WT_I18N::translate('Data folder'), help_link('INDEX_DIRECTORY'), '</dt>',
			'<dd>', edit_field_inline('site_setting-INDEX_DIRECTORY', WT_Site::preference('INDEX_DIRECTORY'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Memory limit'), help_link('MEMORY_LIMIT'), '</dt>',
			'<dd>', edit_field_inline('site_setting-MEMORY_LIMIT', WT_Site::preference('MEMORY_LIMIT'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('PHP time limit'), help_link('MAX_EXECUTION_TIME'), '</dt>',
			'<dd>', edit_field_inline('site_setting-MAX_EXECUTION_TIME', WT_Site::preference('MAX_EXECUTION_TIME'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Allow visitors to request account registration'), help_link('USE_REGISTRATION_MODULE'), '</dt>',
			'<dd>', edit_field_yes_no_inline('site_setting-USE_REGISTRATION_MODULE', WT_Site::preference('USE_REGISTRATION_MODULE'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Require an administrator to approve new user registrations'), help_link('REQUIRE_ADMIN_AUTH_REGISTRATION'), '</dt>',
			'<dd>', edit_field_yes_no_inline('site_setting-REQUIRE_ADMIN_AUTH_REGISTRATION', WT_Site::preference('REQUIRE_ADMIN_AUTH_REGISTRATION'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Allow users to select their own theme'), help_link('ALLOW_USER_THEMES'), '</dt>',
			'<dd>', edit_field_yes_no_inline('site_setting-ALLOW_USER_THEMES', WT_Site::preference('ALLOW_USER_THEMES'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Default Theme'), help_link('THEME'), '</dt>',
			'<dd>', select_edit_control_inline('site_setting-THEME_DIR', array_flip(get_theme_names()), null, WT_Site::preference('THEME_DIR'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Allow GEDCOM switching'), help_link('ALLOW_CHANGE_GEDCOM'), '</dt>',
			'<dd>', edit_field_yes_no_inline('site_setting-ALLOW_CHANGE_GEDCOM', WT_Site::preference('ALLOW_CHANGE_GEDCOM'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Session timeout'), help_link('SESSION_TIME'), '</dt>',
			'<dd>', edit_field_inline('site_setting-SESSION_TIME', WT_Site::preference('SESSION_TIME'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Website URL'), help_link('SERVER_URL'), '</dt>',
			'<dd>', select_edit_control_inline('site_setting-SERVER_URL', array(WT_SERVER_NAME.WT_SCRIPT_PATH=>WT_SERVER_NAME.WT_SCRIPT_PATH), '', WT_Site::preference('SERVER_URL'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Login URL'), help_link('LOGIN_URL'), '</dt>',
			'<dd>', edit_field_inline('site_setting-LOGIN_URL', WT_Site::preference('LOGIN_URL'), $controller), '</dd>',
			'</dl></td></tr></table></div>',
			'<div id="mail">',
			'<table>',
			'<tr><td><dl>',
			'<dt>', WT_I18N::translate('Messages'), help_link('SMTP_ACTIVE'), '</dt>',
			'<dd>', select_edit_control_inline('site_setting-SMTP_ACTIVE', array('internal'=>WT_I18N::translate('Use PHP mail to send messages'), 'external'=>WT_I18N::translate('Use SMTP to send messages')), null, WT_Site::preference('SMTP_ACTIVE'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Sender name'), help_link('SMTP_FROM_NAME'), '</dt>',
			'<dd>', edit_field_inline('site_setting-SMTP_FROM_NAME', WT_Site::preference('SMTP_FROM_NAME'), $controller), '</dd>',
			'</dl>',
			'</td></tr></table>',
			'<br>',
			'<h4>', WT_I18N::translate('SMTP mail server'), '</h4>',
			'<table>',
			'<tr><td><dl>',
			'</dl>',
			'<dl>',
			'<dt>', WT_I18N::translate('Server name'), help_link('SMTP_HOST'), '</dt>',
			'<dd>', edit_field_inline('site_setting-SMTP_HOST', WT_Site::preference('SMTP_HOST'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Port number'), help_link('SMTP_PORT'), '</dt>',
			'<dd>', edit_field_inline('site_setting-SMTP_PORT', WT_Site::preference('SMTP_PORT'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Use password'), help_link('SMTP_AUTH'), '</dt>',
			'<dd>', edit_field_yes_no_inline('site_setting-SMTP_AUTH', WT_Site::preference('SMTP_AUTH'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Username'), help_link('SMTP_AUTH_USER'), '</dt>',
			'<dd>', edit_field_inline('site_setting-SMTP_AUTH_USER', WT_Site::preference('SMTP_AUTH_USER'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Password'), help_link('SMTP_AUTH_PASS'), '</dt>',
			// Don't show password.  save.php has special code for this.
			'<dd>', edit_field_inline('site_setting-SMTP_AUTH_PASS', '', $controller), '</dd>',
			'<dt>', WT_I18N::translate('Secure connection'), help_link('SMTP_SSL'), '</dt>',
			'<dd>', select_edit_control_inline('site_setting-SMTP_SSL', array('none'=>WT_I18N::translate('none'), /* I18N: Secure Sockets Layer - a secure communications protocol*/ 'ssl'=>WT_I18N::translate('ssl'), /* I18N: Transport Layer Security - a secure communications protocol */ 'tls'=>WT_I18N::translate('tls')), null, WT_Site::preference('SMTP_SSL'), $controller), '</dd>',
			'<dt>', WT_I18N::translate('Sending server name'), help_link('SMTP_HELO'), '</dt>',
			'<dd>', edit_field_inline('site_setting-SMTP_HELO', WT_Site::preference('SMTP_HELO'), $controller), '</dd>',
			'</dl></td></tr></table>',
			'<p>',
			WT_I18N::translate('To use a Google mail account, use the following settings: server=smtp.gmail.com, port=587, security=tls, username=xxxxx@gmail.com, password=[your gmail password]'),
			'</p>',
			'</div>',
		'</div>',
	'</div>';
