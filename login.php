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
use Fisharebest\Webtrees\Functions\Functions;
use Rhumsaa\Uuid\Uuid;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'login.php');
require './includes/session.php';

// If we are already logged in, then go to the “Home page”
if (Auth::check() && $WT_TREE) {
	header('Location: ' . WT_BASE_URL);

	return;
}

$controller = new PageController;

$action          = Filter::post('action');
$user_realname   = Filter::post('user_realname');
$user_name       = Filter::post('user_name');
$user_email      = Filter::postEmail('user_email');
$user_password01 = Filter::post('user_password01', WT_REGEX_PASSWORD);
$user_password02 = Filter::post('user_password02', WT_REGEX_PASSWORD);
$user_comments   = Filter::post('user_comments');
$user_password   = Filter::post('user_password');
$user_hashcode   = Filter::post('user_hashcode');
$url             = Filter::post('url'); // Not actually a URL - just a path
$username        = Filter::post('username');
$password        = Filter::post('password');

// These parameters may come from the URL which is emailed to users.
if (!$action)        $action        = Filter::get('action');
if (!$user_name)     $user_name     = Filter::get('user_name');
if (!$user_hashcode) $user_hashcode = Filter::get('user_hashcode');
if (!$url)           $url           = Filter::get('url');

$message = '';

switch ($action) {
case 'login':
	try {
		if (!$_COOKIE) {
			Log::addAuthenticationLog('Login failed (no session cookies): ' . $username);
			throw new \Exception(I18N::translate('You cannot login because your browser does not accept cookies.'));
		}

		$user = User::findByIdentifier($username);

		if (!$user) {
			Log::addAuthenticationLog('Login failed (no such user/email): ' . $username);
			throw new \Exception(I18N::translate('The username or password is incorrect.'));
		}

		if (!$user->checkPassword($password)) {
			Log::addAuthenticationLog('Login failed (incorrect password): ' . $username);
			throw new \Exception(I18N::translate('The username or password is incorrect.'));
		}

		if (!$user->getPreference('verified')) {
			Log::addAuthenticationLog('Login failed (not verified by user): ' . $username);
			throw new \Exception(I18N::translate('This account has not been verified.  Please check your email for a verification message.'));
		}

		if (!$user->getPreference('verified_by_admin')) {
			Log::addAuthenticationLog('Login failed (not approved by admin): ' . $username);
			throw new \Exception(I18N::translate('This account has not been approved.  Please wait for an administrator to approve it.'));
		}

		Auth::login($user);
		Log::addAuthenticationLog('Login: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
		Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);

		Session::put('locale', Auth::user()->getPreference('language'));
		Session::put('theme_id', Auth::user()->getPreference('theme'));

		// We're logging in as an administrator
		if (Auth::isAdmin()) {
			// Check for updates
			$latest_version_txt = Functions::fetchLatestVersion();
			if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
				list($latest_version, $earliest_version, $download_url) = explode('|', $latest_version_txt);
				if (version_compare(WT_VERSION, $latest_version) < 0) {
					FlashMessages::addMessage(
						I18N::translate('A new version of webtrees is available.') .
						' <a href="admin_site_upgrade.php"><b>' .
						I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $latest_version . '</span>') .
						'</b></a>'
					);
				}
			}
		}

		// If we were on a "home page", redirect to "my page"
		if ($url === '' || strpos($url, 'index.php?ctype=gedcom') === 0) {
			$url = 'index.php?ctype=user';
			// Switch to a tree where we have a genealogy record (or keep to the current/default).
			$tree = Database::prepare(
				"SELECT gedcom_name FROM `##gedcom` JOIN `##user_gedcom_setting` USING (gedcom_id)" .
				" WHERE setting_name = 'gedcomid' AND user_id = :user_id"
			)->execute(array(
				'user_id' => Auth::user()->getUserId(),
			))->fetchOne();
			$url .= '&ged=' . Filter::escapeUrl($tree);
		}

		// Redirect to the target URL
		header('Location: ' . WT_BASE_URL . $url);

		return;
	} catch (\Exception $ex) {
		$message = $ex->getMessage();
	}
	// No break;

default:
	$controller
		->setPageTitle(I18N::translate('Login'))
		->pageHeader()
		->addInlineJavascript('
			jQuery("#new_passwd_form").hide();
			jQuery("#passwd_click").click(function() {
				jQuery("#new_passwd_form").slideToggle(100, function() {
					jQuery("#new_passwd_username").focus()
				});
				return false;
			});
		');

	echo '<div id="login-page">';
	echo '<div id="login-text">';

	switch (Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
	case 1:
		echo I18N::translate('<center><b>Welcome to this genealogy website</b></center><br>Access to this website is permitted to every visitor who has a user account.<br><br>If you have a user account, you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying your application, the website administrator will activate your account.  You will receive an email when your application has been approved.');
		break;
	case 2:
		echo I18N::translate('<center><b>Welcome to this genealogy website</b></center><br>Access to this website is permitted to <u>authorized</u> users only.<br><br>If you have a user account you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.');
		break;
	case 3:
		echo I18N::translate('<center><b>Welcome to this genealogy website</b></center><br>Access to this website is permitted to <u>family members only</u>.<br><br>If you have a user account you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.');
		break;
	case 4:
		echo '<p style="white-space: pre-wrap;">', Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE), '</p>';
		break;
	}

	echo '</div>';
	echo '<div id="login-box">';
		if ($message) {
			echo '<p class="error">', $message, '</p>';
		}
	echo '<form id="login-form" name="login-form" method="post" action="', WT_LOGIN_URL, '">
		<input type="hidden" name="action" value="login">
		<input type="hidden" name="url" value="', Filter::escapeHtml($url), '">';
		echo '<div>
			<label for="username">', I18N::translate('Username'),
			'<input type="text" id="username" name="username" value="', Filter::escapeHtml($username), '" class="formField" autofocus>
			</label>
		</div>
		<div>
			<label for="password">', I18N::translate('Password'),
				'<input type="password" id="password" name="password" class="formField">
			</label>
		</div>
		<div>
			<input type="submit" value="', I18N::translate('Login'), '">
		</div>
		';
		// Emails are sent from a TREE, not from a SITE.  Therefore if there is no
		// tree available (initial setup or all trees private), then we can't send email.
		if ($WT_TREE) {
			echo '
			<div>
				<a href="#" id="passwd_click">', I18N::translate('Request new password'), '</a>
			</div>';
			if (Site::getPreference('USE_REGISTRATION_MODULE')) {
				echo '<div><a href="' . WT_LOGIN_URL . '?action=register">', I18N::translate('Request new user account'), '</a></div>';
			}
		}
	echo '</form>';

	// hidden New Password block
	echo '<div id="new_passwd">
		<form id="new_passwd_form" name="new_passwd_form" action="' . WT_LOGIN_URL . '" method="post">
		<input type="hidden" name="action" value="requestpw">
		<h4>', I18N::translate('Lost password request'), '</h4>
		<div>
			<label for="new_passwd_username">', I18N::translate('Username or email address'),
				'<input type="text" id="new_passwd_username" name="new_passwd_username" value="">
			</label>
		</div>
		<div><input type="submit" value="', /* I18N: button label */ I18N::translate('continue'), '"></div>
		</form>
	</div>';
	echo '</div>';

	echo '</div>';
	break;

case 'requestpw':
	$user_name = Filter::post('new_passwd_username');
	$user      = User::findByIdentifier($user_name);

	if ($user) {
		$passchars   = 'abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$user_new_pw = '';
		$max         = strlen($passchars) - 1;
		for ($i = 0; $i < 8; $i++) {
			$index = rand(0, $max);
			$user_new_pw .= $passchars{$index};
		}

		$user->setPassword($user_new_pw);
		Log::addAuthenticationLog('Password request was sent to user: ' . $user->getUserName());

		Mail::systemMessage(
			$WT_TREE,
			$user,
			I18N::translate('Lost password request'),
			I18N::translate('Hello %s…', $user->getRealNameHtml()) . Mail::EOL . Mail::EOL .
			I18N::translate('A new password has been requested for your user name.') . Mail::EOL . Mail::EOL .
			I18N::translate('Username') . ": " . Filter::escapeHtml($user->getUserName()) . Mail::EOL .
			I18N::translate('Password') . ": " . $user_new_pw . Mail::EOL . Mail::EOL .
			I18N::translate('After you have logged in, select the “My account” link under the “My page” menu and fill in the password fields to change your password.') . Mail::EOL . Mail::EOL .
			'<a href="' . WT_BASE_URL . 'login.php?ged=' . $WT_TREE->getNameUrl() . '">' . WT_BASE_URL . 'login.php?ged=' . $WT_TREE->getNameUrl() . '</a>'
		);

		FlashMessages::addMessage(I18N::translate('A new password has been created and emailed to %s.  You can change this password after you login.', Filter::escapeHtml($user_name)), 'success');
	} else {
		FlashMessages::addMessage(I18N::translate('There is no account with the username or email “%s”.', Filter::escapeHtml($user_name)), 'danger');
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
	break;

case 'register':
	if (!Site::getPreference('USE_REGISTRATION_MODULE')) {
		header('Location: ' . WT_BASE_URL);

		return;
	}

	$controller->setPageTitle(I18N::translate('Request new user account'));

	// The form parameters are mandatory, and the validation errors are shown in the client.
	if (Session::get('good_to_send') && $user_name && $user_password01 && $user_password01 == $user_password02 && $user_realname && $user_email && $user_comments) {

		// These validation errors cannot be shown in the client.
		if (User::findByIdentifier($user_name)) {
			FlashMessages::addMessage(I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
		} elseif (User::findByIdentifier($user_email)) {
			FlashMessages::addMessage(I18N::translate('Duplicate email address.  A user with that email already exists.'));
		} elseif (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $user_comments, $match)) {
			FlashMessages::addMessage(
				I18N::translate('You are not allowed to send messages that contain external links.') . ' ' .
				I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1])
			);
			Log::addAuthenticationLog('Possible spam registration from "' . $user_name . '"/"' . $user_email . '" comments="' . $user_comments . '"');
		} else {
			// Everything looks good - create the user
			$controller->pageHeader();
			Log::addAuthenticationLog('User registration requested for: ' . $user_name);

			$user = User::create($user_name, $user_realname, $user_email, $user_password01);
			$user
				->setPreference('language', WT_LOCALE)
				->setPreference('verified', '0')
				->setPreference('verified_by_admin', 0)
				->setPreference('reg_timestamp', date('U'))
				->setPreference('reg_hashcode', md5(Uuid::uuid4()))
				->setPreference('contactmethod', 'messaging2')
				->setPreference('comment', $user_comments)
				->setPreference('visibleonline', '1')
				->setPreference('auto_accept', '0')
				->setPreference('canadmin', '0')
				->setPreference('sessiontime', '0');

			// Generate an email in the admin’s language
			$webmaster = User::find($WT_TREE->getPreference('WEBMASTER_USER_ID'));
			I18N::init($webmaster->getPreference('language'));

			$mail1_body =
				I18N::translate('Hello administrator…') . Mail::EOL . Mail::EOL .
				/* I18N: %s is a server name/URL */
				I18N::translate('A prospective user has registered with webtrees at %s.', WT_BASE_URL . ' ' . $WT_TREE->getTitleHtml()) . Mail::EOL . Mail::EOL .
				I18N::translate('Username') . ' ' . Filter::escapeHtml($user->getUserName()) . Mail::EOL .
				I18N::translate('Real name') . ' ' . $user->getRealNameHtml() . Mail::EOL .
				I18N::translate('Email address') . ' ' . Filter::escapeHtml($user->getEmail()) . Mail::EOL .
				I18N::translate('Comments') . ' ' . Filter::escapeHtml($user_comments) . Mail::EOL . Mail::EOL .
				I18N::translate('The user has been sent an e-mail with the information necessary to confirm the access request.') . Mail::EOL . Mail::EOL .
				I18N::translate('You will be informed by e-mail when this prospective user has confirmed the request.  You can then complete the process by activating the user name.  The new user will not be able to login until you activate the account.');

			$mail1_subject = /* I18N: %s is a server name/URL */ I18N::translate('New registration at %s', WT_BASE_URL . ' ' . $WT_TREE->getTitle());
			I18N::init(WT_LOCALE);

			echo '<div id="login-register-page">';

			// Generate an email in the user’s language
			$mail2_body =
				I18N::translate('Hello %s…', $user->getRealNameHtml()) . Mail::EOL . Mail::EOL .
				/* I18N: %1$s is the site URL and %2$s is an email address */
				I18N::translate('You (or someone claiming to be you) has requested an account at %1$s using the email address %2$s.', WT_BASE_URL . ' ' . $WT_TREE->getTitleHtml(), $user->getEmail()) . '  ' .
				I18N::translate('Information about the request is shown under the link below.') . Mail::EOL .
				I18N::translate('Please click on the following link and fill in the requested data to confirm your request and email address.') . Mail::EOL . Mail::EOL .
				'<a href="' . WT_LOGIN_URL . '?user_name=' . Filter::escapeUrl($user->getUserName()) . '&amp;user_hashcode=' . $user->getPreference('reg_hashcode') . '&amp;action=userverify&amp;ged=' . $WT_TREE->getNameUrl() . '">' .
				WT_LOGIN_URL . "?user_name=" . Filter::escapeHtml($user->getUserName()) . "&amp;user_hashcode=" . urlencode($user->getPreference('reg_hashcode')) . '&amp;action=userverify&amp;ged=' . $WT_TREE->getNameHtml() .
				'</a>' . Mail::EOL . Mail::EOL .
				I18N::translate('Username') . " - " . Filter::escapeHtml($user->getUserName()) . Mail::EOL .
				I18N::translate('Verification code') . " - " . $user->getPreference('reg_hashcode') . Mail::EOL .
				I18N::translate('Comments') . " - " . $user->getPreference('comment') . Mail::EOL .
				I18N::translate('If you didn’t request an account, you can just delete this message.') . Mail::EOL;
			$mail2_subject = /* I18N: %s is a server name/URL */ I18N::translate('Your registration at %s', WT_BASE_URL);
			$mail2_to      = $user->getEmail();
			$mail2_from    = $WT_TREE->getPreference('WEBTREES_EMAIL');

			// Send user message by email only
			Mail::send(
				// “From:” header
				$WT_TREE,
				// “To:” header
				$mail2_to,
				$mail2_to,
				// “Reply-To:” header
				$mail2_from,
				$mail2_from,
				// Message body
				$mail2_subject,
				$mail2_body
			);

			// Send admin message by email and/or internal messaging
			Mail::send(
				// “From:” header
				$WT_TREE,
				// “To:” header
				$webmaster->getEmail(),
				$webmaster->getRealName(),
				// “Reply-To:” header
				$user->getEmail(),
				$user->getRealName(),
				// Message body
				$mail1_subject,
				$mail1_body
			);
			$mail1_method = $webmaster->getPreference('contact_method');
			if ($mail1_method != 'messaging3' && $mail1_method != 'mailto' && $mail1_method != 'none') {
				Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
					->execute(array($user->getEmail(), WT_CLIENT_IP, $webmaster->getUserId(), $mail1_subject, Filter::unescapeHtml($mail1_body)));
			}

			echo '<div class="confirm"><p>', I18N::translate('Hello %s…<br>Thank you for your registration.', $user->getRealNameHtml()), '</p>';
			echo '<p>', I18N::translate('We will now send a confirmation email to the address <b>%s</b>.  You must verify your account request by following instructions in the confirmation email.  If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br><br>After you have followed the instructions in the confirmation email, the administrator still has to approve your request before your account can be used.<br><br>To login to this website, you will need to know your user name and password.', $user->getEmail()), '</p>';
			echo '</div>';
			echo '</div>';

			return;
		}
	}

	Session::put('good_to_send', true);
	$controller
		->pageHeader()
		->addInlineJavascript('function regex_quote(str) {return str.replace(/[\\\\.?+*()[\](){}|]/g, "\\\\$&");}');

	?>
	<div id="login-register-page">
		<h2><?php echo $controller->getPageTitle(); ?></h2>

		<?php if (Site::getPreference('SHOW_REGISTER_CAUTION')): ?>
		<div id="register-text">
			<?php echo I18N::translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living individuals listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our website.</li></ul></div>'); ?>
		</div>
		<?php endif; ?>
		<div id="register-box">
			<form id="register-form" name="register-form" method="post" onsubmit="return checkform(this);" autocomplete="off">
				<input type="hidden" name="action" value="register">
				<h4><?php echo I18N::translate('All fields must be completed.'); ?></h4>
				<hr>

				<div>
					<label for="user_realname">
						<?php echo I18N::translate('Real name'); ?>
						<input type="text" id="user_realname" name="user_realname" required maxlength="64" value="<?php echo Filter::escapeHtml($user_realname); ?>" autofocus>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('This is your real name, as you would like it displayed on screen.'); ?>
					</p>
				</div>

				<div>
					<label for="user_email">
						<?php echo I18N::translate('Email address'); ?>
						<input type="email" id="user_email" name="user_email" required maxlength="64" value="<?php echo Filter::escapeHtml($user_email); ?>">
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.'); ?>
					</p>
				</div>

				<div>
					<label for="username">
						<?php echo I18N::translate('Desired user name'); ?>
						<input type="text" id="username" name="user_name" required maxlength="32" value="<?php Filter::escapeHtml($user_name); ?>">
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.'); ?>
					</p>
				</div>

				<div>
					<label for="user_password01">
						<?php echo I18N::translate('Desired password'); ?>
						<input required
							type="password"
							id="user_password01" name="user_password01"
							value="<?php echo Filter::escapeHtml($user_password01); ?>"
							placeholder="<?php echo /* I18N: placeholder text for new-password field */ I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, I18N::number(WT_MINIMUM_PASSWORD_LENGTH)); ?>"
							pattern="<?php echo  WT_REGEX_PASSWORD; ?>"
							onchange="form.user_password02.pattern = regex_quote(this.value);"
						>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.'); ?>
					</p>
				</div>

				<div>
					<label for="user_password02">
						<?php echo I18N::translate('Confirm password'); ?>
						<input required
							type="password"
							id="user_password02" name="user_password02"
							value="<?php echo Filter::escapeHtml($user_password02); ?>"
							placeholder="<?php echo /* I18N: placeholder text for repeat-password field */ I18N::translate('Type the password again.'); ?>"
							pattern="<?php echo WT_REGEX_PASSWORD; ?>"
						>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('Type your password again, to make sure you have typed it correctly.'); ?>
					</p>
				</div>

				<div>
					<label for="user_comments">
						<?php echo I18N::translate('Comments'); ?>
						<textarea required
							cols="50" rows="5"
							id="user_comments" name="user_comments"
							placeholder="<?php /* I18N: placeholder text for registration-comments field */ I18N::translate('Explain why you are requesting an account.'); ?>"
						><?php echo Filter::escapeHtml($user_comments); ?></textarea>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site.  You can also use this to enter any other comments you may have for the site administrator.'); ?>
					</p>
				</div>

				<hr>

				<div id="registration-submit">
					<input type="submit" value="<?php echo I18N::translate('continue'); ?>">
				</div>
			</form>
		</div>
	</div>
	<?php
	break;

case 'userverify':
	if (!Site::getPreference('USE_REGISTRATION_MODULE')) {
		header('Location: ' . WT_BASE_URL);

		return;
	}

	// Change to the new user’s language
	$user = User::findByIdentifier($user_name);

	I18N::init($user->getPreference('language'));

	$controller->setPageTitle(I18N::translate('User verification'));
	$controller->pageHeader();

	echo '<div id="login-register-page">
		<form id="verify-form" name="verify-form" method="post" action="', WT_LOGIN_URL, '">
			<input type="hidden" name="action" value="verify_hash">
			<h4>', I18N::translate('User verification'), '</h4>
			<div>
				<label for="username">', I18N::translate('Username'), '</label>
				<input type="text" id="username" name="user_name" value="', $user_name, '">
			</div>
			<div>
			<label for="user_password">', I18N::translate('Password'), '</label>
			<input type="password" id="user_password" name="user_password" value="" autofocus>
			</div>
			<div>
			<label for="user_hashcode">', I18N::translate('Verification code'), '</label>
			<input type="text" id="user_hashcode" name="user_hashcode" value="', $user_hashcode, '">
			</div>
			<div>
				<input type="submit" value="', I18N::translate('Send'), '">
			</div>
		</form>
	</div>';
	break;

case 'verify_hash':
	if (!Site::getPreference('USE_REGISTRATION_MODULE')) {
		header('Location: ' . WT_BASE_URL);

		return;
	}

	// switch language to webmaster settings
	$webmaster = User::find($WT_TREE->getPreference('WEBMASTER_USER_ID'));
	I18N::init($webmaster->getPreference('language'));

	$user          = User::findByIdentifier($user_name);
	$edit_user_url = WT_BASE_URL . "admin_users.php?action=edit&amp;user_id=" . $user->getUserId();
	$mail1_body    =
		I18N::translate('Hello administrator…') .
		Mail::EOL . Mail::EOL .
		/* I18N: %1$s is a real-name, %2$s is a username, %3$s is an email address */ I18N::translate(
			'A new user (%1$s) has requested an account (%2$s) and verified an email address (%3$s).',
			$user->getRealNameHtml(),
			Filter::escapeHtml($user->getUserName()),
			Filter::escapeHtml($user->getEmail())
		) .
		Mail::EOL . Mail::EOL .
		I18N::translate('You need to review the account details.') .
		Mail::EOL . Mail::EOL .
		'<a href="' . $edit_user_url . '">' . $edit_user_url . '</a>' .
		Mail::EOL . Mail::EOL .
		/* I18N: You need to: */ I18N::translate('Set the status to “approved”.') .
		Mail::EOL .
		/* I18N: You need to: */ I18N::translate('Set the access level for each tree.') .
		Mail::EOL .
		/* I18N: You need to: */ I18N::translate('Link the user account to an individual.');

	$mail1_subject = /* I18N: %s is a server name/URL */ I18N::translate('New user at %s', WT_BASE_URL . ' ' . $WT_TREE->getTitle());

	// Change to the new user’s language
	I18N::init($user->getPreference('language'));

	$controller->setPageTitle(I18N::translate('User verification'));
	$controller->pageHeader();

	echo '<div id="login-register-page">';
	echo '<h2>' . I18N::translate('User verification') . '</h2>';
	echo '<div id="user-verify">';
	if ($user && $user->checkPassword($user_password) && $user->getPreference('reg_hashcode') === $user_hashcode) {
		Mail::send(
		// “From:” header
			$WT_TREE,
			// “To:” header
			$webmaster->getEmail(),
			$webmaster->getRealName(),
			// “Reply-To:” header
			$WT_TREE->getPreference('WEBTREES_EMAIL'),
			$WT_TREE->getPreference('WEBTREES_EMAIL'),
			// Message body
			$mail1_subject,
			$mail1_body
		);
		$mail1_method = $webmaster->getPreference('CONTACT_METHOD');
		if ($mail1_method != 'messaging3' && $mail1_method != 'mailto' && $mail1_method != 'none') {
			Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
				->execute(array($user_name, WT_CLIENT_IP, $webmaster->getUserId(), $mail1_subject, Filter::unescapeHtml($mail1_body)));
		}

		$user
			->setPreference('verified', '1')
			->setPreference('reg_timestamp', date('U'))
			->deletePreference('reg_hashcode');

		Log::addAuthenticationLog('User ' . $user_name . ' verified their email address');

		echo '<p>', I18N::translate('You have confirmed your request to become a registered user.'), '</p>';
		echo '<p>', I18N::translate('The administrator has been informed.  As soon as they give you permission to login, you can login with your user name and password.'), '</p>';
	} else {
		echo '<p class="warning">';
		echo I18N::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
		echo '</p>';
	}
	echo '</div>';
	echo '</div>';
	break;
}
