<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

define('WT_SCRIPT_NAME', 'admin_site_config.php');
require './includes/session.php';

$controller = new PageController;
$controller->restrictAccess(Auth::isAdmin());

switch (Filter::post('action')) {
case 'site':
	if (Filter::checkCsrf()) {
		$INDEX_DIRECTORY = Filter::post('INDEX_DIRECTORY');
		if (substr($INDEX_DIRECTORY, -1) !== '/') {
			$INDEX_DIRECTORY .= '/';
		}
		if (File::mkdir($INDEX_DIRECTORY)) {
			Site::setPreference('INDEX_DIRECTORY', $INDEX_DIRECTORY);
		} else {
			FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Filter::escapeHtml($INDEX_DIRECTORY)), 'danger');
		}
		Site::setPreference('MEMORY_LIMIT', Filter::post('MEMORY_LIMIT'));
		Site::setPreference('MAX_EXECUTION_TIME', Filter::post('MAX_EXECUTION_TIME'));
		Site::setPreference('ALLOW_USER_THEMES', Filter::postBool('ALLOW_USER_THEMES'));
		Site::setPreference('THEME_DIR', Filter::post('THEME_DIR'));
		Site::setPreference('ALLOW_CHANGE_GEDCOM', Filter::postBool('ALLOW_CHANGE_GEDCOM'));
		Site::setPreference('SESSION_TIME', Filter::post('SESSION_TIME'));
		Site::setPreference('SERVER_URL', Filter::post('SERVER_URL'));
		Site::setPreference('TIMEZONE', Filter::post('TIMEZONE'));
		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
	}
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;

case 'email':
	if (Filter::checkCsrf()) {
		Site::setPreference('SMTP_ACTIVE', Filter::post('SMTP_ACTIVE'));
		Site::setPreference('SMTP_FROM_NAME', Filter::post('SMTP_FROM_NAME'));
		Site::setPreference('SMTP_HOST', Filter::post('SMTP_HOST'));
		Site::setPreference('SMTP_PORT', Filter::post('SMTP_PORT'));
		Site::setPreference('SMTP_AUTH', Filter::post('SMTP_AUTH'));
		Site::setPreference('SMTP_AUTH_USER', Filter::post('SMTP_AUTH_USER'));
		Site::setPreference('SMTP_SSL', Filter::post('SMTP_SSL'));
		Site::setPreference('SMTP_HELO', Filter::post('SMTP_HELO'));
		if (Filter::post('SMTP_AUTH_PASS')) {
			Site::setPreference('SMTP_AUTH_PASS', Filter::post('SMTP_AUTH_PASS'));
		}
		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
	}
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;
case 'login':
	if (Filter::checkCsrf()) {
		Site::setPreference('LOGIN_URL', Filter::post('LOGIN_URL'));
		Site::setPreference('WELCOME_TEXT_AUTH_MODE', Filter::post('WELCOME_TEXT_AUTH_MODE'));
		Site::setPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE, Filter::post('WELCOME_TEXT_AUTH_MODE_4'));
		Site::setPreference('USE_REGISTRATION_MODULE', Filter::post('USE_REGISTRATION_MODULE'));
		Site::setPreference('SHOW_REGISTER_CAUTION', Filter::post('SHOW_REGISTER_CAUTION'));
		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
	}
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;

case 'tracking':
	if (Filter::checkCsrf()) {
		Site::setPreference('BING_WEBMASTER_ID', Filter::post('BING_WEBMASTER_ID'));
		Site::setPreference('GOOGLE_WEBMASTER_ID', Filter::post('GOOGLE_WEBMASTER_ID'));
		Site::setPreference('GOOGLE_ANALYTICS_ID', Filter::post('GOOGLE_ANALYTICS_ID'));
		Site::setPreference('PIWIK_URL', Filter::post('PIWIK_URL'));
		Site::setPreference('PIWIK_SITE_ID', Filter::post('PIWIK_SITE_ID'));
		Site::setPreference('STATCOUNTER_PROJECT_ID', Filter::post('STATCOUNTER_PROJECT_ID'));
		Site::setPreference('STATCOUNTER_SECURITY_ID', Filter::post('STATCOUNTER_SECURITY_ID'));
		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
	}
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;

case 'languages':
	if (Filter::checkCsrf()) {
		Site::setPreference('LANGUAGES', implode(',', Filter::postArray('LANGUAGES')));
		FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
	}
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;
}

// Lists of options for <select> controls.
$SMTP_SSL_OPTIONS = array(
	'none'                                                                        => I18N::translate('none'),
	/* I18N: Secure Sockets Layer - a secure communications protocol*/ 'ssl'      => I18N::translate('ssl'),
	/* I18N: Transport Layer Security - a secure communications protocol */ 'tls' => I18N::translate('tls'),
);
$SMTP_ACTIVE_OPTIONS = array(
	'internal' => I18N::translate('Use PHP mail to send messages'),
	'external' => I18N::translate('Use SMTP to send messages'),
);
$WELCOME_TEXT_AUTH_MODE_OPTIONS = array(
	0 => I18N::translate('No predefined text'),
	1 => I18N::translate('Predefined text that states all users can request a user account'),
	2 => I18N::translate('Predefined text that states admin will decide on each request for a user account'),
	3 => I18N::translate('Predefined text that states only family members can request a user account'),
	4 => I18N::translate('Choose user defined welcome text typed below'),
);

$language_tags = array();
foreach (I18N::activeLocales() as $active_locale) {
	$language_tags[] = $active_locale->languageTag();
}

switch (Filter::get('action')) {
case 'site':
	$controller->setPageTitle(I18N::translate('Website preferences'));
	break;
case 'email':
	$controller->setPageTitle(I18N::translate('Sending email'));
	break;
case 'login':
	$controller->setPageTitle(I18N::translate('Login and registration'));
	break;
case 'tracking':
	$controller->setPageTitle(/* I18N: e.g. http://www.google.com/analytics */ I18N::translate('Tracking and analytics'));
	break;
case 'languages':
	$controller->setPageTitle(I18N::translate('Languages'));
	break;
default:
	header('Location: ' . WT_BASE_URL . 'admin.php');

	return;
}

$controller->pageHeader();

?>

<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form method="post" class="form-horizontal">
	<?php echo Filter::getCsrf(); ?>

	<?php if (Filter::get('action') === 'site'): ?>
	<input type="hidden" name="action" value="site">

	<!-- INDEX_DIRECTORY -->
	<div class="form-group">
		<label for="INDEX_DIRECTORY" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Data folder'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" dir="ltr" id="INDEX_DIRECTORY" name="INDEX_DIRECTORY" value="<?php echo Filter::escapeHtml(Site::getPreference('INDEX_DIRECTORY')); ?>" maxlength="255" placeholder="data/" required>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Data folder" site configuration setting */ I18N::translate('This folder will be used by webtrees to store media files, GEDCOM files, temporary files, etc.  These files may contain private data, and should not be made available over the internet.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo /* I18N: “Apache” is a software program. */ I18N::translate('To protect this private data, webtrees uses an Apache configuration file (.htaccess) which blocks all access to this folder.  If your web-server does not support .htaccess files, and you cannot restrict access to this folder, then you can select another folder, away from your web documents.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo I18N::translate('If you select a different folder, you must also move all files (except config.ini.php, index.php, and .htaccess) from the existing folder to the new folder.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo I18N::translate('The folder can be specified in full (e.g. /home/user_name/webtrees_data/) or relative to the installation folder (e.g. ../../webtrees_data/).'); ?>
			</p>
		</div>
	</div>

	<!-- MEMORY_LIMIT -->
	<div class="form-group">
		<label for="MEMORY_LIMIT" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Memory limit'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="MEMORY_LIMIT" name="MEMORY_LIMIT" value="<?php echo Filter::escapeHtml(Site::getPreference('MEMORY_LIMIT')); ?>" pattern="[0-9]+[KMG]" placeholder="<?php echo get_cfg_var('memory_limit'); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: %s is an amount of memory, such as 32MB */ I18N::translate('By default, your server allows scripts to use %s of memory.', get_cfg_var('memory_limit')); ?>
				<?php echo I18N::translate('You can request a higher or lower limit, although the server may ignore this request.'); ?>
				<?php echo I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
			</p>
		</div>
	</div>

	<!-- MAX_EXECUTION_TIME -->
	<div class="form-group">
		<label for="MAX_EXECUTION_TIME" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('PHP time limit'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="MAX_EXECUTION_TIME" name="MAX_EXECUTION_TIME" value="<?php echo Filter::escapeHtml(Site::getPreference('MAX_EXECUTION_TIME')); ?>" pattern="[0-9]*" placeholder="<?php echo get_cfg_var('max_execution_time'); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo I18N::plural(
					'By default, your server allows scripts to run for %s second.',
					'By default, your server allows scripts to run for %s seconds.',
					get_cfg_var('max_execution_time'), I18N::number(get_cfg_var('max_execution_time')));
				?>
				<?php echo I18N::translate('You can request a higher or lower limit, although the server may ignore this request.'); ?>
				<?php echo I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
			</p>
		</div>
	</div>

	<!-- TIMEZONE -->
	<div class="form-group">
		<label for="TIMEZONE" class="col-sm-3 control-label">
			<?php echo I18N::translate('Time zone'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('TIMEZONE', array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers()), null, Site::getPreference('TIMEZONE') ?: 'UTC', 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo I18N::translate('The time zone is required for date calculations, such as knowing today’s date.'); ?>
			</p>
		</div>
	</div>

	<!-- THEME_DIR -->
	<div class="form-group">
		<label for="THEME_DIR" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Default theme'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('THEME_DIR', Theme::themeNames(), null, Site::getPreference('THEME_DIR'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Default theme" site configuration setting */ I18N::translate('You can change the appearance of webtrees using “themes”.  Each theme has a different style, layout, color scheme, etc.'); ?>
			</p>
			<p class="small text-muted">
				<?php echo I18N::translate('Themes can be selected at three levels: user, family tree, and website.  User settings take priority over family tree settings, which in turn take priority over the website setting.  Selecting “default theme” at one level will use the theme at the next level.'); ?>
			</p>
		</div>
	</div>

	<!-- ALLOW_USER_THEMES -->
	<fieldset class="form-group">
		<legend class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Allow users to select their own theme'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::editFieldYesNo('ALLOW_USER_THEMES', Site::getPreference('ALLOW_USER_THEMES'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Allow users to select their own theme” site configuration setting */ I18N::translate('Gives users the option of selecting their own theme.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- ALLOW_CHANGE_GEDCOM -->
	<fieldset class="form-group">
		<legend class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Show list of family trees'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::editFieldYesNo('ALLOW_CHANGE_GEDCOM', Site::getPreference('ALLOW_CHANGE_GEDCOM'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php /* I18N: Help text for the “Show list of family trees” site configuration setting */ I18N::translate('For websites with more than one family tree, this option will show the list of family trees in the main menu, the search pages, etc.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SESSION_TIME -->
	<div class="form-group">
		<label for="SESSION_TIME" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Session timeout'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SESSION_TIME" name="SESSION_TIME" value="<?php echo Filter::escapeHtml(Site::getPreference('SESSION_TIME')); ?>" pattern="[0-9]*" placeholder="7200" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Session timeout” site configuration setting */ I18N::translate('The time in seconds that a webtrees session remains active before requiring a login.  The default is 7200, which is 2 hours.'); ?>
				<?php echo I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
			</p>
		</div>
	</div>

	<!-- SERVER_URL -->
	<div class="form-group">
		<label for="SERVER_URL" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Website URL'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('SERVER_URL', array(WT_BASE_URL => WT_BASE_URL), '', Site::getPreference('SERVER_URL'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Website URL" site configuration setting */ I18N::translate('If your website can be reached using more than one URL, such as <b>http://www.example.com/webtrees/</b> and <b>http://webtrees.example.com/</b>, you can specify the preferred URL.  Requests for the other URLs will be redirected to the preferred one.'); ?>
				<?php echo I18N::translate('If you leave this setting empty, the default value will be used.'); ?>
			</p>
		</div>
	</div>

<?php elseif (Filter::get('action') === 'email'): ?>
	<input type="hidden" name="action" value="email">

	<!-- SMTP_ACTIVE -->
	<div class="form-group">
		<label for="SMTP_ACTIVE" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Messages'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('SMTP_ACTIVE', $SMTP_ACTIVE_OPTIONS, null, Site::getPreference('SMTP_ACTIVE'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Messages” site configuration setting */ I18N::translate('webtrees needs to send emails, such as password reminders and website notifications.  To do this, it can use this server’s built in PHP mail facility (which is not always available) or an external SMTP (mail-relay) service, for which you will need to provide the connection details.'); ?>
			</p>
		</div>
	</div>

	<!-- SMTP_FROM_NAME -->
	<div class="form-group">
		<label for="SMTP_FROM_NAME" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Sender name'); ?>
		</label>
		<div class="col-sm-9">
			<input type="email" class="form-control" id="SMTP_FROM_NAME" name="SMTP_FROM_NAME" value="<?php echo Filter::escapeHtml(Site::getPreference('SMTP_FROM_NAME')); ?>" placeholder="no-reply@localhost" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Sender name” site configuration setting */ I18N::translate('This name is used in the “From” field, when sending automatic emails from this server.'); ?>
			</p>
		</div>
	</div>

	<h2><?php echo I18N::translate('SMTP mail server'); ?></h2>

	<!-- SMTP_HOST -->
	<div class="form-group">
		<label for="SMTP_HOST" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Server name'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_HOST" name="SMTP_HOST" value="<?php echo Filter::escapeHtml(Site::getPreference('SMTP_HOST')); ?>" placeholder="smtp.example.com" maxlength="255" pattern="[a-z0-9-]+(\.[a-z0-9-]+)*">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Server name” site configuration setting */ I18N::translate('This is the name of the SMTP server.  “localhost” means that the mail service is running on the same computer as your web server.'); ?>
			</p>
		</div>
	</div>

	<!-- SMTP_PORT -->
	<div class="form-group">
		<label for="SMTP_PORT" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Port number'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_PORT" name="SMTP_PORT" value="<?php echo Filter::escapeHtml(Site::getPreference('SMTP_PORT')); ?>" pattern="[0-9]*" placeholder="25" maxlength="5">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Port number" site configuration setting */ I18N::translate('By default, SMTP works on port 25.'); ?>
			</p>
		</div>
	</div>

	<!-- SMTP_AUTH -->
	<fieldset class="form-group">
		<legend class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Use password'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::editFieldYesNo('SMTP_AUTH', Site::getPreference('SMTP_AUTH'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Use password” site configuration setting */ I18N::translate('Most SMTP servers require a password.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SMTP_AUTH_USER -->
	<div class="form-group">
		<label for="SMTP_AUTH_USER" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Username'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_AUTH_USER" name="SMTP_AUTH_USER" value="<?php echo Filter::escapeHtml(Site::getPreference('SMTP_AUTH_USER')); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Username" site configuration setting */ I18N::translate('The user name required for authentication with the SMTP server.'); ?>
			</p>
		</div>
	</div>

	<!-- SMTP_AUTH_PASS -->
	<div class="form-group">
		<label for="SMTP_AUTH_PASS" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Password'); ?>
		</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="SMTP_AUTH_PASS" name="SMTP_AUTH_PASS" value="">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Password" site configuration setting */ I18N::translate('The password required for authentication with the SMTP server.'); ?>
			</p>
		</div>
	</div>

	<!-- SMTP_SSL -->
	<div class="form-group">
		<label for="SMTP_SSL" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Secure connection'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('SMTP_SSL', $SMTP_SSL_OPTIONS, null, Site::getPreference('SMTP_SSL'), 'class="form-control"'); ?>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the “Secure connection” site configuration setting */ I18N::translate('Most servers do not use secure connections.'); ?>
			</p>
		</div>
	</div>

	<!-- SMTP_HELO -->
	<div class="form-group">
		<label for="SMTP_HELO" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Sending server name'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="SMTP_HELO" name="SMTP_HELO" value="<?php echo Filter::escapeHtml(Site::getPreference('SMTP_HELO')); ?>" placeholder="localhost" maxlength="255" pattern="[a-z0-9-]+(\.[a-z0-9-]+)*">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Sending server name" site configuration setting */ I18N::translate('Many mail servers require that the sending server identifies itself correctly, using a valid domain name.'); ?>
			</p>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<p class="small text-muted">
				<?php echo Theme::theme()->htmlAlert(I18N::translate('To use a Google mail account, use the following settings: server=smtp.gmail.com, port=587, security=tls, username=xxxxx@gmail.com, password=[your gmail password]'), 'info', false); ?>
			</p>
		</div>
	</div>

	<?php elseif (Filter::get('action') === 'login'): ?>
	<input type="hidden" name="action" value="login">

	<!-- LOGIN_URL -->
	<div class="form-group">
		<label for="LOGIN_URL" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Login URL'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="LOGIN_URL" name="LOGIN_URL" value="<?php echo Filter::escapeHtml(Site::getPreference('LOGIN_URL')); ?>" maxlength="255">
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Login URL" site configuration setting */ I18N::translate('You only need to enter a Login URL if you want to redirect to a different website or location when your users login.  This is very useful if you need to switch from http to https when your users login.  Include the full URL to <i>login.php</i>.  For example, https://www.yourserver.com/webtrees/login.php .'); ?>
			</p>
		</div>
	</div>

	<!-- WELCOME_TEXT_AUTH_MODE -->
	<div class="form-group">
		<label for="WELCOME_TEXT_AUTH_MODE" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Welcome text on login page'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::selectEditControl('WELCOME_TEXT_AUTH_MODE', $WELCOME_TEXT_AUTH_MODE_OPTIONS, null, Site::getPreference('WELCOME_TEXT_AUTH_MODE'), 'class="form-control"'); ?>
			<p class="small text-muted">
			</p>
		</div>
	</div>

	<!-- LOGIN_URL -->
	<div class="form-group">
		<label for="WELCOME_TEXT_AUTH_MODE_4" class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Custom welcome text'); ?>
		</label>
		<div class="col-sm-9">
			<textarea class="form-control" maxlength="2000" id="WELCOME_TEXT_AUTH_MODE_4" name="WELCOME_TEXT_AUTH_MODE_4" rows="4"><?php echo Filter::escapeHtml(Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE)); ?></textarea>
			<p class="small text-muted">
				<?php echo /* I18N: Help text for the "Custom welcome text" site configuration setting */ I18N::translate('To set this text for other languages, you must switch to that language, and visit this page again.'); ?>
			</p>
		</div>
	</div>

	<!-- USE_REGISTRATION_MODULE -->
	<fieldset class="form-group">
		<legend class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Allow visitors to request a new user account'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::editFieldYesNo('USE_REGISTRATION_MODULE', Site::getPreference('USE_REGISTRATION_MODULE'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
				<?php echo I18N::translate('The new user will be asked to confirm their email address before the account is created.'); ?>
				<?php echo I18N::translate('Details of the new user will be sent to the genealogy contact for the corresponding family tree.'); ?>
				<?php echo I18N::translate('An administrator must approve the new user account and select an access level before the user can log in.'); ?>
			</p>
		</div>
	</fieldset>

	<!-- SHOW_REGISTER_CAUTION -->
	<fieldset class="form-group">
		<legend class="col-sm-3 control-label">
			<?php echo /* I18N: A site configuration setting */ I18N::translate('Show acceptable use agreement on “Request new user account” page'); ?>
		</legend>
		<div class="col-sm-9">
			<?php echo FunctionsEdit::editFieldYesNo('SHOW_REGISTER_CAUTION', Site::getPreference('SHOW_REGISTER_CAUTION'), 'class="radio-inline"'); ?>
			<p class="small text-muted">
			</p>
		</div>
	</fieldset>

	<?php elseif (Filter::get('action') === 'tracking'): ?>
	<input type="hidden" name="action" value="tracking">

		<p>
			<?php echo I18N::translate('If you use one of the following tracking and analytics services, webtrees can add the tracking codes automatically.'); ?>
		</p>

		<h2><a href="https://http://www.bing.com/toolbox/webmaster/">Bing Webmaster Tools</a></h2>

		<!-- BING_WEBMASTER_ID -->
		<div class="form-group">
			<label for="BING_WEBMASTER_ID" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Site verification code'); ?>
				<span class="sr-only">Google Webmaster Tools</span>
			</label>
			<div class="col-sm-9">
				<input
					type="text" class="form-control"
					id="BING_WEBMASTER_ID" name="BING_WEBMASTER_ID" <?php echo dirname(parse_url(WT_BASE_URL, PHP_URL_PATH)) === '/' ? '' : 'disabled'; ?>
					value="<?php echo Filter::escapeHtml(Site::getPreference('BING_WEBMASTER_ID')); ?>"
					maxlength="255" pattern="[0-9a-zA-Z+=/_:.!-]*"
					>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Site verification code for Google Webmaster Tools" site configuration setting */ I18N::translate('Site verification codes do not work when webtrees is installed in a subfolder.'); ?>
				</p>
			</div>
		</div>

		<h2><a href="https://www.google.com/webmasters/">Google Webmaster Tools</a></h2>

		<!-- GOOGLE_WEBMASTER_ID -->
		<div class="form-group">
			<label for="GOOGLE_WEBMASTER_ID" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Site verification code'); ?>
				<span class="sr-only">Google Webmaster Tools</span>
			</label>
			<div class="col-sm-9">
				<input
					type="text" class="form-control"
					id="GOOGLE_WEBMASTER_ID" name="GOOGLE_WEBMASTER_ID" <?php echo dirname(parse_url(WT_BASE_URL, PHP_URL_PATH)) === '/' ? '' : 'disabled'; ?>
					value="<?php echo Filter::escapeHtml(Site::getPreference('GOOGLE_WEBMASTER_ID')); ?>"
					maxlength="255" pattern="[0-9a-zA-Z+=/_:.!-]*"
				>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the "Site verification code for Google Webmaster Tools" site configuration setting */ I18N::translate('Site verification codes do not work when webtrees is installed in a subfolder.'); ?>
				</p>
			</div>
		</div>

		<h2><a href="http://www.google.com/analytics/">Google Analytics</a></h2>

		<!-- GOOGLE_ANALYTICS_ID -->
		<div class="form-group">
			<label for="GOOGLE_ANALYTICS_ID" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Site identification code'); ?>
				<span class="sr-only">Google Analytics</span>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="GOOGLE_ANALYTICS_ID" name="GOOGLE_ANALYTICS_ID" value="<?php echo Filter::escapeHtml(Site::getPreference('GOOGLE_ANALYTICS_ID')); ?>" placeholder="UA-12345-6" maxlength="255" pattern="UA-[0-9]+-[0-9]+">
				<p class="small text-muted">
					<?php echo I18N::translate('Tracking and analytics are not added to the control panel.'); ?>
				</p>
			</div>
		</div>

		<h2><a href="https://piwik.org/">Piwik</a></h2>

		<!-- PIWIK_SITE_ID -->
		<div class="form-group">
			<label for="PIWIK_SITE_ID" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Site identification code'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="PIWIK_SITE_ID" name="PIWIK_SITE_ID" value="<?php echo Filter::escapeHtml(Site::getPreference('PIWIK_SITE_ID')); ?>" maxlength="255" pattern="[0-9]+">
			</div>
		</div>

		<!-- PIWIK_URL -->
		<div class="form-group">
			<label for="PIWIK_URL" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('URL'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="PIWIK_URL" name="PIWIK_URL" value="<?php echo Filter::escapeHtml(Site::getPreference('PIWIK_URL')); ?>" placeholder="example.com/piwik" maxlength="255">
				<p class="small text-muted">
					<?php echo I18N::translate('Tracking and analytics are not added to the control panel.'); ?>
				</p>
			</div>
		</div>

		<h2><a href="https://statcounter.com/">StatCounter</a></h2>

		<!-- STATCOUNTER_PROJECT_ID -->
		<div class="form-group">
			<label for="STATCOUNTER_PROJECT_ID" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Site identification code'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="STATCOUNTER_PROJECT_ID" name="STATCOUNTER_PROJECT_ID" value="<?php echo Filter::escapeHtml(Site::getPreference('STATCOUNTER_PROJECT_ID')); ?>" maxlength="255" pattern="[0-9]+">
			</div>
		</div>

		<!-- STATCOUNTER_SECURITY_ID -->
		<div class="form-group">
			<label for="STATCOUNTER_SECURITY_ID" class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Security code'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="STATCOUNTER_SECURITY_ID" name="STATCOUNTER_SECURITY_ID" value="<?php echo Filter::escapeHtml(Site::getPreference('STATCOUNTER_SECURITY_ID')); ?>" maxlength="255" pattern="[0-9a-zA-Z]+">
				<p class="small text-muted">
					<?php echo I18N::translate('Tracking and analytics are not added to the control panel.'); ?>
				</p>
			</div>
		</div>

	<?php elseif (Filter::get('action') === 'languages'): ?>
		<input type="hidden" name="action" value="languages">

		<p>
			<?php echo I18N::translate('Select the languages that will be shown in menus.'); ?>
		</p>

		<fieldset class="form-group">
			<legend class="col-sm-3 control-label">
				<?php echo /* I18N: A site configuration setting */ I18N::translate('Language'); ?>
			</legend>
			<div class="col-sm-9" style="columns: 4 150px;-moz-columns: 4 150px;">
				<?php foreach (I18N::installedLocales() as $installed_locale): ?>
					<div class="checkbox">
						<label title="<?php echo $installed_locale->languageTag(); ?>">
							<input type="checkbox" name="LANGUAGES[]" value="<?php echo $installed_locale->languageTag(); ?>" <?php echo in_array($installed_locale->languageTag(), $language_tags) ? 'checked' : ''; ?>>
							<?php echo $installed_locale->endonym(); ?>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
		</fieldset>

	<?php endif; ?>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-check"></i>
				<?php echo I18N::translate('save'); ?>
			</button>
		</div>
	</div>
</form>
