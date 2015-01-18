<?php
// A form to edit site configuration.
//
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
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
use WT\Theme;

define('WT_SCRIPT_NAME', 'admin_site_config.php');
require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller->restrictAccess(Auth::isAdmin());

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

switch (WT_Filter::post('action')) {
case 'site':
	if (WT_Filter::checkCsrf()) {
		$INDEX_DIRECTORY = WT_Filter::post('INDEX_DIRECTORY');
		if (substr($INDEX_DIRECTORY, -1) !== '/') {
			$INDEX_DIRECTORY .= '/';
		}
		if (WT_File::mkdir($INDEX_DIRECTORY)) {
			WT_Site::setPreference('INDEX_DIRECTORY', $INDEX_DIRECTORY);
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', WT_Filter::escapeHtml($INDEX_DIRECTORY)), 'danger');
		}
		WT_Site::setPreference('MEMORY_LIMIT', WT_Filter::post('MEMORY_LIMIT'));
		WT_Site::setPreference('MAX_EXECUTION_TIME', WT_Filter::post('MAX_EXECUTION_TIME'));
		WT_Site::setPreference('ALLOW_USER_THEMES', WT_Filter::postBool('ALLOW_USER_THEMES'));
		WT_Site::setPreference('THEME_DIR', WT_Filter::post('THEME_DIR'));
		WT_Site::setPreference('ALLOW_CHANGE_GEDCOM', WT_Filter::postBool('ALLOW_CHANGE_GEDCOM'));
		WT_Site::setPreference('SESSION_TIME', WT_Filter::post('SESSION_TIME'));
		WT_Site::setPreference('SERVER_URL', WT_Filter::post('SERVER_URL'));
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=site');

	return;
case 'email':
	if (WT_Filter::checkCsrf()) {
		WT_Site::setPreference('SMTP_ACTIVE', WT_Filter::post('SMTP_ACTIVE'));
		WT_Site::setPreference('SMTP_FROM_NAME', WT_Filter::post('SMTP_FROM_NAME'));
		WT_Site::setPreference('SMTP_HOST', WT_Filter::post('SMTP_HOST'));
		WT_Site::setPreference('SMTP_PORT', WT_Filter::post('SMTP_PORT'));
		WT_Site::setPreference('SMTP_AUTH', WT_Filter::post('SMTP_AUTH'));
		WT_Site::setPreference('SMTP_AUTH_USER', WT_Filter::post('SMTP_AUTH_USER'));
		WT_Site::setPreference('SMTP_AUTH_PASS', WT_Filter::post('SMTP_AUTH_PASS'));
		WT_Site::setPreference('SMTP_SSL', WT_Filter::post('SMTP_SSL'));
		WT_Site::setPreference('SMTP_HELO', WT_Filter::post('SMTP_HELO'));
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=email');

	return;
case 'login':
	if (WT_Filter::checkCsrf()) {
		WT_Site::setPreference('LOGIN_URL', WT_Filter::post('LOGIN_URL'));
		WT_Site::setPreference('WELCOME_TEXT_AUTH_MODE', WT_Filter::post('WELCOME_TEXT_AUTH_MODE'));
		WT_Site::setPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE, WT_Filter::post('WELCOME_TEXT_AUTH_MODE_4'));
		WT_Site::setPreference('USE_REGISTRATION_MODULE', WT_Filter::post('USE_REGISTRATION_MODULE'));
		WT_Site::setPreference('LOGIN_URL', WT_Filter::post('LOGIN_URL'));
		WT_Site::setPreference('LOGIN_URL', WT_Filter::post('LOGIN_URL'));
		WT_Site::setPreference('LOGIN_URL', WT_Filter::post('LOGIN_URL'));
		WT_Site::setPreference('LOGIN_URL', WT_Filter::post('LOGIN_URL'));
		WT_Site::setPreference('LOGIN_URL', WT_Filter::post('LOGIN_URL'));
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=login');

	return;
}

switch (WT_Filter::get('action')) {
case 'site':
	$controller->setPageTitle(WT_I18N::translate('Site preferences'));
	break;
case 'email':
	$controller->setPageTitle(WT_I18N::translate('Sending email'));
	break;
case 'login':
	$controller->setPageTitle(WT_I18N::translate('Login and registration'));
	break;
default:
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'admin.php');

	return;
}

$controller->pageHeader();

?>

<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Administration'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>
<h2><?php echo $controller->getPageTitle(); ?></h2>

<form method="post" class="form-horizontal">
	<?php echo WT_Filter::getCsrf(); ?>

	<?php if (WT_Filter::get('action') === 'site'): ?>
	<input type="hidden" name="action" value="site">

	<!-- INDEX_DIRECTORY -->
	<div class="form-group">
		<label for="INDEX_DIRECTORY" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Data folder'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="INDEX_DIRECTORY" name="INDEX_DIRECTORY" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('INDEX_DIRECTORY')); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Data folder" site configuration setting */ WT_I18N::translate('This folder will be used by webtrees to store media files, GEDCOM files, temporary files, etc.  These files may contain private data, and should not be made available over the internet.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo /* I18N: “Apache” is a software program. */ WT_I18N::translate('To protect this private data, webtrees uses an Apache configuration file (.htaccess) which blocks all access to this folder.  If your web-server does not support .htaccess files, and you cannot restrict access to this folder, then you can select another folder, away from your web documents.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo WT_I18N::translate('If you select a different folder, you must also move all files (except config.ini.php, index.php, and .htaccess) from the existing folder to the new folder.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo WT_I18N::translate('The folder can be specified in full (e.g. /home/user_name/webtrees_data/) or relative to the installation folder (e.g. ../../webtrees_data/).'); ?>
			</p>
		</div>
	</div>

	<!-- MEMORY_LIMIT -->
	<div class="form-group">
		<label for="MEMORY_LIMIT" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Memory limit'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="MEMORY_LIMIT" name="MEMORY_LIMIT" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('MEMORY_LIMIT')); ?>" pattern="[0-9]+[KMG]" placeholder="<?php echo get_cfg_var('memory_limit'); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: %s is an amount of memory, such as 32MB */ WT_I18N::translate('By default, your server allows scripts to use %s of memory.', get_cfg_var('memory_limit')); ?>
				<?php echo WT_I18N::translate('You can request a higher or lower limit, although the server may ignore this request.'); ?>
				<?php echo WT_I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
			</p>
		</div>
	</div>

	<!-- MAX_EXECUTION_TIME -->
	<div class="form-group">
		<label for="MAX_EXECUTION_TIME" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('PHP time limit'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="MAX_EXECUTION_TIME" name="MAX_EXECUTION_TIME" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('MAX_EXECUTION_TIME')); ?>" pattern="[0-9]*" placeholder="<?php echo get_cfg_var('max_execution_time'); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo WT_I18N::plural(
					'By default, your server allows scripts to run for %s second.',
					'By default, your server allows scripts to run for %s seconds.',
					get_cfg_var('max_execution_time'), get_cfg_var('max_execution_time'));
				?>
				<?php echo WT_I18N::translate('You can request a higher or lower limit, although the server may ignore this request.'); ?>
				<?php echo WT_I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
			</p>
		</div>
	</div>

		<!-- THEME_DIR -->
		<div class="form-group">
			<label for="THEME_DIR" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Default theme'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('THEME_DIR', Theme::themeNames(), null, WT_Site::getPreference('THEME_DIR'), 'class="form-control"'); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Default theme" site configuration setting */ WT_I18N::translate('You can change the appearance of webtrees using “themes”.  Each theme has a different style, layout, color scheme, etc.'); ?>
				</p>
				<p class="small text-muted">
					<?php echo WT_I18N::translate('Themes can be selected at three levels: user, GEDCOM, and site.  User settings take priority over GEDCOM settings, which in turn take priority over the site setting.  Selecting “default theme” at user level will give the setting for the current GEDCOM.  Selecting “default theme” at GEDCOM level will give the site setting.'); ?>
				</p>
			</div>
		</div>

		<!-- ALLOW_USER_THEMES -->
	<div class="form-group">
		<label for="ALLOW_USER_THEMES" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Allow users to select their own theme'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo edit_field_yes_no('ALLOW_USER_THEMES', WT_Site::getPreference('ALLOW_USER_THEMES')); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Allow users to select their own theme” site configuration setting */ WT_I18N::translate('Gives users the option of selecting their own theme.'); ?>
			</p>
		</div>
	</div>

		<!-- ALLOW_CHANGE_GEDCOM -->
	<div class="form-group">
		<label for="ALLOW_CHANGE_GEDCOM" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Show list of family trees'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo edit_field_yes_no('ALLOW_CHANGE_GEDCOM', WT_Site::getPreference('ALLOW_CHANGE_GEDCOM')); ?>
			<p class="small text-muted">
				<?php /* I18N: Help text for the “Show list of family trees” site configuration setting */ WT_I18N::translate('For sites with more than one family tree, this option will show the list of family trees in the main menu, the search pages, etc.'); ?>
			</p>
		</div>
	</div>

		<!-- SESSION_TIME -->
		<div class="form-group">
			<label for="SESSION_TIME" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Session timeout'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="SESSION_TIME" name="SESSION_TIME" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SESSION_TIME')); ?>" pattern="[0-9]*" placeholder="7200" maxlength="255">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Session timeout” site configuration setting */ WT_I18N::translate('The time in seconds that a webtrees session remains active before requiring a login.  The default is 7200, which is 2 hours.'); ?>
					<?php echo WT_I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
				</p>
			</div>
		</div>

		<!-- SERVER_URL -->
		<div class="form-group">
			<label for="SERVER_URL" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Website URL'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('SERVER_URL', array(WT_SERVER_NAME . WT_SCRIPT_PATH=>WT_SERVER_NAME . WT_SCRIPT_PATH), '', WT_Site::getPreference('SERVER_URL'), 'class="form-control"'); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Website URL" site configuration setting */ WT_I18N::translate('If your site can be reached using more than one URL, such as <b>http://www.example.com/webtrees/</b> and <b>http://webtrees.example.com/</b>, you can specify the preferred URL.  Requests for the other URLs will be redirected to the preferred one.'); ?>
					<?php echo WT_I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
				</p>
			</div>
		</div>

	<?php elseif (WT_Filter::get('action') === 'email'): ?>
		<input type="hidden" name="action" value="email">

		<!-- SMTP_ACTIVE -->
		<div class="form-group">
			<label for="SMTP_ACTIVE" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Messages'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('SMTP_ACTIVE', $SMTP_ACTIVE_OPTIONS, null, WT_Site::getPreference('SMTP_ACTIVE'), 'class="form-control"'); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Messages” site configuration setting */ WT_I18N::translate('webtrees needs to send emails, such as password reminders and site notifications.  To do this, it can use this server’s built in PHP mail facility (which is not always available) or an external SMTP (mail-relay) service, for which you will need to provide the connection details.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_FROM_NAME -->
		<div class="form-group">
			<label for="SMTP_FROM_NAME" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Sender name'); ?>
			</label>
			<div class="col-sm-9">
				<input type="email" class="form-control" id="SMTP_FROM_NAME" name="SMTP_FROM_NAME" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SMTP_FROM_NAME')); ?>" placeholder="no-reply@localhost" maxlength="255">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Sender name” site configuration setting */ WT_I18N::translate('This name is used in the “From” field, when sending automatic emails from this server.'); ?>
				</p>
			</div>
		</div>

		<h3><?php echo WT_I18N::translate('SMTP mail server'); ?></h3>

		<!-- SMTP_HOST -->
		<div class="form-group">
			<label for="SMTP_HOST" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Server name'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="SMTP_HOST" name="SMTP_HOST" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SMTP_HOST')); ?>" placeholder="smtp.example.com" maxlength="255" pattern="[a-z0-9-]+(\.[a-z0-9-]+)*">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Server name” site configuration setting */ WT_I18N::translate('This is the name of the SMTP server. “localhost” means that the mail service is running on the same computer as your web server.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_PORT -->
		<div class="form-group">
			<label for="SMTP_PORT" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Port number'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="SMTP_PORT" name="SMTP_PORT" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SMTP_PORT')); ?>" pattern="[0-9]*" placeholder="25" maxlength="5">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Port number" site configuration setting */ WT_I18N::translate('By default, SMTP works on port 25.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_AUTH -->
		<div class="form-group">
			<label for="SMTP_AUTH" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Use password'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo edit_field_yes_no('SMTP_AUTH', WT_Site::getPreference('SMTP_AUTH')); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Use password” site configuration setting */ WT_I18N::translate('Most SMTP servers require a password.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_AUTH_USER -->
		<div class="form-group">
			<label for="SMTP_AUTH_USER" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Username'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="SMTP_AUTH_USER" name="SMTP_AUTH_USER" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SMTP_AUTH_USER')); ?>" maxlength="255">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Username" site configuration setting */ WT_I18N::translate('The user name required for authentication with the SMTP server.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_AUTH_PASS -->
		<div class="form-group">
			<label for="SMTP_AUTH_PASS" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Password'); ?>
			</label>
			<div class="col-sm-9">
				<input type="password" class="form-control" id="SMTP_AUTH_PASS" name="SMTP_AUTH_PASS" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SMTP_AUTH_PASS')); ?>">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Password" site configuration setting */ WT_I18N::translate('The password required for authentication with the SMTP server.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_SSL -->
		<div class="form-group">
			<label for="SMTP_SSL" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Secure connection'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('SMTP_SSL', $SMTP_SSL_OPTIONS, null, WT_Site::getPreference('SMTP_SSL'), 'class="form-control"'); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Secure connection” site configuration setting */ WT_I18N::translate('Most servers do not use secure connections.'); ?>
				</p>
			</div>
		</div>

		<!-- SMTP_HELO -->
		<div class="form-group">
			<label for="SMTP_HELO" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Sending server name'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="SMTP_HELO" name="SMTP_HELO" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('SMTP_HELO')); ?>" placeholder="localhost" maxlength="255" pattern="[a-z0-9-]+(\.[a-z0-9-]+)*">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Sending server name" site configuration setting */ WT_I18N::translate('Many mail servers require that the sending server identifies itself correctly, using a valid domain name.'); ?>
				</p>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<p class="alert alert-info">
					<?php echo WT_I18N::translate('To use a Google mail account, use the following settings: server=smtp.gmail.com, port=587, security=tls, username=xxxxx@gmail.com, password=[your gmail password]'); ?>
				</p>
			</div>
		</div>

	<?php elseif (WT_Filter::get('action') === 'login'): ?>
		<input type="hidden" name="action" value="login">

		<!-- LOGIN_URL -->
		<div class="form-group">
			<label for="LOGIN_URL" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Login URL'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="LOGIN_URL" name="LOGIN_URL" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('LOGIN_URL')); ?>" maxlength="255">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Login URL" site configuration setting */ WT_I18N::translate('You only need to enter a Login URL if you want to redirect to a different site or location when your users login.  This is very useful if you need to switch from http to https when your users login.  Include the full URL to <i>login.php</i>.  For example, https://www.yourserver.com/webtrees/login.php .'); ?>
				</p>
			</div>
		</div>

		<!-- WELCOME_TEXT_AUTH_MODE -->
		<div class="form-group">
			<label for="WELCOME_TEXT_AUTH_MODE" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Welcome text on login page'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('WELCOME_TEXT_AUTH_MODE', $WELCOME_TEXT_AUTH_MODE_OPTIONS, null, WT_Site::getPreference('WELCOME_TEXT_AUTH_MODE'), 'class="form-control"'); ?>
				<p class="small text-muted">
				</p>
			</div>
		</div>

		<!-- LOGIN_URL -->
		<div class="form-group">
			<label for="WELCOME_TEXT_AUTH_MODE_4" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Custom welcome text'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="WELCOME_TEXT_AUTH_MODE_4" name="WELCOME_TEXT_AUTH_MODE_4" value="<?php echo WT_Filter::escapeHtml(WT_Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE)); ?>" maxlength="255">
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Custom welcome text" site configuration setting */ WT_I18N::translate('To set this text for other languages, you must switch to that language, and visit this page again.'); ?>
				</p>
			</div>
		</div>

		<!-- USE_REGISTRATION_MODULE -->
		<div class="form-group">
			<label for="USE_REGISTRATION_MODULE" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Allow visitors to request account registration'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo edit_field_yes_no('USE_REGISTRATION_MODULE', WT_Site::getPreference('USE_REGISTRATION_MODULE')); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Allow visitors to request account registration” site configuration setting */ WT_I18N::translate('Gives visitors the option of registering themselves for an account on the site.<br><br>The visitor will receive an email message with a code to verify his application for an account.  After verification, an administrator will have to approve the registration before it becomes active.'); ?>
				</p>
			</div>
		</div>

		<!-- USE_REGISTRATION_MODULE -->
		<div class="form-group">
			<label for="REQUIRE_ADMIN_AUTH_REGISTRATION" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Require an administrator to approve new user registrations'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo edit_field_yes_no('REQUIRE_ADMIN_AUTH_REGISTRATION', WT_Site::getPreference('REQUIRE_ADMIN_AUTH_REGISTRATION')); ?>
				<p class="small text-muted">
				</p>
			</div>
		</div>

		<!-- USE_REGISTRATION_MODULE -->
		<div class="form-group">
			<label for="SHOW_REGISTER_CAUTION" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ WT_I18N::translate('Show acceptable use agreement on “Request new user account” page'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo edit_field_yes_no('SHOW_REGISTER_CAUTION', WT_Site::getPreference('SHOW_REGISTER_CAUTION')); ?>
				<p class="small text-muted">
				</p>
			</div>
		</div>

	<?php endif; ?>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?php echo WT_I18N::translate('save'); ?>
			</button>
		</div>
	</div>
</form>
