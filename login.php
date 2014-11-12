<?php
// Register as a new User or request new password if it is lost
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

use Rhumsaa\Uuid\Uuid;
use WT\Auth;
use WT\Log;
use WT\User;

define('WT_SCRIPT_NAME', 'login.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';
// If we are already logged in, then go to the “Home page”
if (Auth::check() && WT_GED_ID) {
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH);
	exit;
}

$controller = new WT_Controller_Page();

$REQUIRE_ADMIN_AUTH_REGISTRATION = WT_Site::getPreference('REQUIRE_ADMIN_AUTH_REGISTRATION');

$action          = WT_Filter::post('action');
$user_realname   = WT_Filter::post('user_realname');
$user_name       = WT_Filter::post('user_name',       WT_REGEX_USERNAME);
$user_email      = WT_Filter::postEmail('user_email');
$user_password01 = WT_Filter::post('user_password01', WT_REGEX_PASSWORD);
$user_password02 = WT_Filter::post('user_password02', WT_REGEX_PASSWORD);
$user_comments   = WT_Filter::post('user_comments');
$user_password   = WT_Filter::post('user_password');
$user_hashcode   = WT_Filter::post('user_hashcode');
$url             = WT_Filter::post('url'); // Not actually a URL - just a path
$username        = WT_Filter::post('username');
$password        = WT_Filter::post('password');
$timediff        = WT_Filter::postInteger('timediff', -43200, 50400, 0); // Same range as date('Z')

// These parameters may come from the URL which is emailed to users.
if (!$action)        $action        = WT_Filter::get('action');
if (!$user_name)     $user_name     = WT_Filter::get('user_name', WT_REGEX_USERNAME);
if (!$user_hashcode) $user_hashcode = WT_Filter::get('user_hashcode');
if (!$url)           $url           = WT_Filter::get('url'); // Not actually a URL - just a path

$message = '';

switch ($action) {
case 'login':
	try {
		if (!$_COOKIE) {
			Log::addAuthenticationLog('Login failed (no session cookies): ' . $username);
			throw new Exception(WT_I18N::translate('You cannot login because your browser does not accept cookies.'));
		}

		$user = User::findByIdentifier($username);

		if (!$user) {
			Log::addAuthenticationLog('Login failed (no such user/email): ' . $username);
			throw new Exception(WT_I18N::translate('The username or password is incorrect.'));
		}

		if (!$user->checkPassword($password)) {
			Log::addAuthenticationLog('Login failed (incorrect password): ' . $username);
			throw new Exception(WT_I18N::translate('The username or password is incorrect.'));
		}

		if (!$user->getPreference('verified')) {
			Log::addAuthenticationLog('Login failed (not verified by user): ' . $username);
			throw new Exception(WT_I18N::translate('This account has not been verified.  Please check your email for a verification message.'));
		}

		if (!$user->getPreference('verified_by_admin')) {
			Log::addAuthenticationLog('Login failed (not approved by admin): ' . $username);
			throw new Exception(WT_I18N::translate('This account has not been approved.  Please wait for an administrator to approve it.'));
		}

		Auth::login($user);
		Log::addAuthenticationLog('Login: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());

		$WT_SESSION->timediff      = $timediff;
		$WT_SESSION->locale        = Auth::user()->getPreference('language');
		$WT_SESSION->theme_dir     = Auth::user()->getPreference('theme');
		$WT_SESSION->activity_time = WT_TIMESTAMP;

		Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);

		// If we’ve clicked login from the login page, we don’t want to go back there.
		if (strpos($url, WT_SCRIPT_NAME) === 0) {
			$url = '';
		}

		// We're logging in as an administrator
		if (Auth::isAdmin()) {
			// Check for updates
			$latest_version_txt = fetch_latest_version();
			if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
				list($latest_version, $earliest_version, $download_url) = explode('|', $latest_version_txt);
				if (version_compare(WT_VERSION, $latest_version)<0) {
					// An upgrade is available.  Let the admin know, by redirecting to the upgrade wizard
					$url = 'admin_site_upgrade.php';
				}
			} else {
				// Cannot determine the latest version
			}
		}

		// Redirect to the target URL
		header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . $url);
		// Explicitly write the session data before we exit,
		// as it doesn’t always happen when using APC.
		Zend_Session::writeClose();
		exit;
	} catch (Exception $ex) {
		$message = $ex->getMessage();
	}
	// No break;

default:
	$controller
		->setPageTitle(WT_I18N::translate('Login'))
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

	switch (WT_Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
	case 1:
		echo WT_I18N::translate('<center><b>Welcome to this genealogy website</b></center><br>Access to this site is permitted to every visitor who has a user account.<br><br>If you have a user account, you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying your application, the site administrator will activate your account.  You will receive an email when your application has been approved.');
		break;
	case 2:
		echo WT_I18N::translate('<center><b>Welcome to this genealogy website</b></center><br>Access to this site is permitted to <u>authorized</u> users only.<br><br>If you have a user account you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.');
		break;
	case 3:
		echo WT_I18N::translate('<center><b>Welcome to this genealogy website</b></center><br>Access to this site is permitted to <u>family members only</u>.<br><br>If you have a user account you can login on this page.  If you don’t have a user account, you can apply for one by clicking on the appropriate link below.<br><br>After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.');
		break;
	case 4:
		echo '<p>', WT_Site::getPreference('WELCOME_TEXT_AUTH_MODE_'.WT_LOCALE), '</p>';
		break;
	}

	echo '</div>';
	echo '<div id="login-box">
		<form id="login-form" name="login-form" method="post" action="', WT_LOGIN_URL, '" onsubmit="d=new Date(); this.timediff.value=d.getTimezoneOffset()*60;">
		<input type="hidden" name="action" value="login">
		<input type="hidden" name="url" value="', WT_Filter::escapeHtml($url), '">
		<input type="hidden" name="timediff" value="0">';
		if ($message) {
			echo '<p class="error">', $message, '</p>';
		}
		echo '<div>
			<label for="username">', WT_I18N::translate('Username'),
			'<input type="text" id="username" name="username" value="', WT_Filter::escapeHtml($username), '" class="formField" autofocus>
			</label>
		</div>
		<div>
			<label for="password">', WT_I18N::translate('Password'),
				'<input type="password" id="password" name="password" class="formField">
			</label>
		</div>
		<div>
			<input type="submit" value="', WT_I18N::translate('Login'), '">
		</div>
		';
		// Emails are sent from a TREE, not from a SITE.  Therefore if there is no
		// tree available (initial setup or all trees private), then we can't send email.
		if ($WT_TREE) {
			echo '
			<div>
				<a href="#" id="passwd_click">', WT_I18N::translate('Request new password'), '</a>
			</div>';
			if (WT_Site::getPreference('USE_REGISTRATION_MODULE')) {
				echo '<div><a href="'.WT_LOGIN_URL.'?action=register">', WT_I18N::translate('Request new user account'), '</a></div>';
			}
		}
	echo '</form>';

	// hidden New Password block
	echo '<div id="new_passwd">
		<form id="new_passwd_form" name="new_passwd_form" action="'.WT_LOGIN_URL.'" method="post">
		<input type="hidden" name="action" value="requestpw">
		<h4>', WT_I18N::translate('Lost password request'), '</h4>
		<div>
			<label for="new_passwd_username">', WT_I18N::translate('Username or email address'),
				'<input type="text" id="new_passwd_username" name="new_passwd_username" value="">
			</label>
		</div>
		<div><input type="submit" value="', /* I18N: button label */ WT_I18N::translate('continue'), '"></div>
		</form>
	</div>';
	echo '</div>';

	echo '</div>';
	break;

case 'requestpw':
	$controller
		->setPageTitle(WT_I18N::translate('Lost password request'))
		->pageHeader();
	echo '<div id="login-page">';
	$user_name = WT_Filter::post('new_passwd_username', WT_REGEX_USERNAME);

	$user = User::findByIdentifier($user_name);
	if ($user) {
		$passchars = 'abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$user_new_pw = '';
		$max = strlen($passchars)-1;
		for ($i=0; $i<8; $i++) {
			$index = rand(0,$max);
			$user_new_pw .= $passchars{$index};
		}

		$user->setPassword($user_new_pw);
		Log::addAuthenticationLog('Password request was sent to user: ' . $user->getUserName());

		WT_Mail::systemMessage(
			$WT_TREE,
			$user,
			WT_I18N::translate('Lost password request'),
			WT_I18N::translate('Hello %s…', WT_Filter::escapeHtml($user->getRealName())) . WT_Mail::EOL . WT_Mail::EOL .
			WT_I18N::translate('A new password was requested for your user name.') . WT_Mail::EOL . WT_Mail::EOL .
			WT_I18N::translate('Username') . ": " . WT_Filter::escapeHtml($user->getUserName()) . WT_Mail::EOL .
			WT_I18N::translate('Password') . ": " . $user_new_pw  . WT_Mail::EOL . WT_Mail::EOL .
			WT_I18N::translate('After you have logged in, select the “My account” link under the “My page” menu and fill in the password fields to change your password.') . WT_Mail::EOL . WT_Mail::EOL .
			'<a href="' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'login.php?ged=' . WT_GEDURL . '">' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'login.php?ged=' . WT_GEDURL . '</a>'
		);
	}
	// Show a success message, even if the user account does not exist.
	// Otherwise this page can be used to guess/test usernames.
	// A genuine user will hopefully always know their own email address.
	echo
		'<div class="confirm"><p>',
		/* I18N: %s is a username */
		WT_I18N::translate('A new password has been created and emailed to %s.  You can change this password after you login.', $user_name),
		'</p></div>';
	echo '</div>';
	break;

case 'register':
	if (!WT_Site::getPreference('USE_REGISTRATION_MODULE')) {
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
		exit;
	}

	$controller->setPageTitle(WT_I18N::translate('Request new user account'));

	// The form parameters are mandatory, and the validation errors are shown in the client.
	if ($WT_SESSION->good_to_send && $user_name && $user_password01 && $user_password01==$user_password02 && $user_realname && $user_email && $user_comments) {

		// These validation errors cannot be shown in the client.
		if (User::findByIdentifier($user_name)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
		} elseif (User::findByIdentifier($user_email)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate email address.  A user with that email already exists.'));
		} elseif (preg_match('/(?!'.preg_quote(WT_SERVER_NAME, '/').')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $user_comments, $match)) {
			WT_FlashMessages::addMessage(
				WT_I18N::translate('You are not allowed to send messages that contain external links.') . ' ' .
				WT_I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1])
			);
			Log::addAuthenticationLog('Possible spam registration from "' . $user_name . '"/"' . $user_email . '" comments="' . $user_comments . '"');
		} else {
			// Everything looks good - create the user
			$controller->pageHeader();
			Log::addAuthenticationLog('User registration requested for: ' . $user_name);

			$user = User::create($user_name, $user_realname, $user_email, $user_password01);
			$user
				->setPreference('language',          WT_LOCALE)
				->setPreference('verified',          '0')
				->setPreference('verified_by_admin', !$REQUIRE_ADMIN_AUTH_REGISTRATION)
				->setPreference('reg_timestamp',     date('U'))
				->setPreference('reg_hashcode',      md5(Uuid::uuid4()))
				->setPreference('contactmethod',     'messaging2')
				->setPreference('comment',           $user_comments)
				->setPreference('visibleonline',     '1')
				->setPreference('editaccount',       '1')
				->setPreference('auto_accept',       '0')
				->setPreference('canadmin',          '0')
				->setPreference('sessiontime',       '0');

			// Generate an email in the admin’s language
			$webmaster = User::find($WT_TREE->getPreference('WEBMASTER_USER_ID'));
			WT_I18N::init($webmaster->getPreference('language'));

			$mail1_body =
				WT_I18N::translate('Hello administrator…') . WT_Mail::EOL . WT_Mail::EOL .
				/* I18N: %s is a server name/URL */
				WT_I18N::translate('A prospective user has registered with webtrees at %s.', WT_SERVER_NAME . WT_SCRIPT_PATH . ' ' . $WT_TREE->tree_title_html) . WT_Mail::EOL . WT_Mail::EOL .
				WT_I18N::translate('Username')       . ' ' . $user->getUserName() . WT_Mail::EOL .
				WT_I18N::translate('Real name')      . ' ' . $user->getRealName() . WT_Mail::EOL .
				WT_I18N::translate('Email address:') . ' ' . $user->getEmail()    . WT_Mail::EOL .
				WT_I18N::translate('Comments')       . ' ' . $user_comments       . WT_Mail::EOL . WT_Mail::EOL .
				WT_I18N::translate('The user has been sent an e-mail with the information necessary to confirm the access request') . WT_Mail::EOL . WT_Mail::EOL;
			if ($REQUIRE_ADMIN_AUTH_REGISTRATION) {
				$mail1_body .= WT_I18N::translate('You will be informed by e-mail when this prospective user has confirmed the request.  You can then complete the process by activating the user name.  The new user will not be able to login until you activate the account.');
			} else {
				$mail1_body .= WT_I18N::translate('You will be informed by e-mail when this prospective user has confirmed the request.  After this, the user will be able to login without any action on your part.');
			}
			$mail1_body .= WT_Mail::auditFooter();

			$mail1_subject = /* I18N: %s is a server name/URL */ WT_I18N::translate('New registration at %s', WT_SERVER_NAME . WT_SCRIPT_PATH . ' ' . $WT_TREE->tree_title);
			WT_I18N::init(WT_LOCALE);

			echo '<div id="login-register-page">';

			// Generate an email in the user’s language
			$mail2_body=
				WT_I18N::translate('Hello %s…', $user->getRealName()) . WT_Mail::EOL . WT_Mail::EOL .
				/* I18N: %1$s is the site URL and %2$s is an email address */
				WT_I18N::translate('You (or someone claiming to be you) has requested an account at %1$s using the email address %2$s.', WT_SERVER_NAME . WT_SCRIPT_PATH . ' ' . $WT_TREE->tree_title_html, $user->getEmail()) . '  '.
				WT_I18N::translate('Information about the request is shown under the link below.') . WT_Mail::EOL .
				WT_I18N::translate('Please click on the following link and fill in the requested data to confirm your request and email address.') . WT_Mail::EOL . WT_Mail::EOL .
				'<a href="' . WT_LOGIN_URL . "?user_name=".WT_Filter::escapeUrl($user->getUserName())."&amp;user_hashcode=".$user->getPreference('reg_hashcode') . '&amp;action=userverify">' .
				WT_LOGIN_URL . "?user_name=".WT_Filter::escapeUrl($user->getUserName())."&user_hashcode=".urlencode($user->getPreference('reg_hashcode'))."&action=userverify" .
				'</a>' . WT_Mail::EOL . WT_Mail::EOL .
				WT_I18N::translate('Username') . " " . WT_Filter::escapeHtml($user->getUserName()) . WT_Mail::EOL .
				WT_I18N::translate('Verification code:') . " " . $user->getPreference('reg_hashcode') . WT_Mail::EOL .
				WT_I18N::translate('Comments').": " . $user->getPreference('comment') . WT_Mail::EOL .
				WT_I18N::translate('If you didn’t request an account, you can just delete this message.') . WT_Mail::EOL;
			$mail2_subject = /* I18N: %s is a server name/URL */ WT_I18N::translate('Your registration at %s', WT_SERVER_NAME.WT_SCRIPT_PATH);
			$mail2_to      = $user->getEmail();
			$mail2_from    = $WEBTREES_EMAIL;

			// Send user message by email only
			WT_Mail::send(
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
			WT_Mail::send(
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
			if ($mail1_method!='messaging3' && $mail1_method!='mailto' && $mail1_method!='none') {
				WT_DB::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
					->execute(array($user->getEmail(), $WT_REQUEST->getClientIp(), $webmaster->getUserId(), $mail1_subject, WT_Filter::unescapeHtml($mail1_body)));
			}

			echo '<div class="confirm"><p>', WT_I18N::translate('Hello %s…<br>Thank you for your registration.', $user->getRealName()), '</p><p>';
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) {
					echo WT_I18N::translate('We will now send a confirmation email to the address <b>%s</b>.  You must verify your account request by following instructions in the confirmation email.  If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br><br>After you have followed the instructions in the confirmation email, the administrator still has to approve your request before your account can be used.<br><br>To login to this site, you will need to know your user name and password.', $user->getEmail());
				} else {
					echo WT_I18N::translate('We will now send a confirmation email to the address <b>%s</b>.  You must verify your account request by following instructions in the confirmation email.  If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br><br>After you have followed the instructions in the confirmation email, you can login.  To login to this site, you will need to know your user name and password.', $user->getEmail());
				}
				echo '</p>
			</div>';
			echo '</div>';
			exit;
		}
	}

	$WT_SESSION->good_to_send = true;
	$controller
		->pageHeader()
		->addInlineJavascript('function regex_quote(str) {return str.replace(/[\\\\.?+*()[\](){}|]/g, "\\\\$&");}');

	echo '<div id="login-register-page">
		<h2>', $controller->getPageTitle(), '</h2>';
		if (WT_Site::getPreference('SHOW_REGISTER_CAUTION')) {
			echo '<div id="register-text">';
			echo WT_I18N::translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living individuals listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div>');
			echo '</div>';
		}
		echo '<div id="register-box">
			<form id="register-form" name="register-form" method="post" action="'.WT_LOGIN_URL.'" onsubmit="return checkform(this);" autocomplete="off">
			<input type="hidden" name="action" value="register">
			<h4>', WT_I18N::translate('All fields must be completed.'), '</h4><hr>
			<div>
				<label for="user_realname">', WT_I18N::translate('Real name'), help_link('real_name'),
					'<input type="text" id="user_realname" name="user_realname" required maxlength="64" value="', WT_Filter::escapeHtml($user_realname), '" autofocus>
				</label>
			</div>
			<div>
				<label for="user_email">', WT_I18N::translate('Email address'), help_link('email'),
					'<input type="email" id="user_email" name="user_email" required maxlength="64" value="', WT_Filter::escapeHtml($user_email), '">
				</label>
			</div>
			<div>
				<label for="username">', WT_I18N::translate('Desired user name'), help_link('username'),
					'<input type="text" id="username" name="user_name" required maxlength="32" value="', WT_Filter::escapeHtml($user_name), '">
				</label>
			</div>
			<div>
				<label for="user_password01">', WT_I18N::translate('Desired password'), help_link('password'),
					'<input type="password" id="user_password01" name="user_password01" value="', WT_Filter::escapeHtml($user_password01), '" required placeholder="', /* I18N: placeholder text for new-password field */ WT_I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, WT_I18N::number(WT_MINIMUM_PASSWORD_LENGTH)), '" pattern="'. WT_REGEX_PASSWORD .'" onchange="form.user_password02.pattern = regex_quote(this.value);">
				</label>
			</div>
			<div>
				<label for="user_password02">', WT_I18N::translate('Confirm password'), help_link('password_confirm'),
					'<input type="password" id="user_password02" name="user_password02" value="', WT_Filter::escapeHtml($user_password02), '" required placeholder="', /* I18N: placeholder text for repeat-password field */ WT_I18N::translate('Type the password again.'), '" pattern="'. WT_REGEX_PASSWORD .'">
				</label>
			</div>
			<div>
				<label for="user_comments">', WT_I18N::translate('Comments'), help_link('register_comments'),
					'<textarea cols="50" rows="5" id="user_comments" name="user_comments" required placeholder="', /* I18N: placeholder text for registration-comments field */ WT_I18N::translate('Explain why you are requesting an account.'), '">',
						WT_Filter::escapeHtml($user_comments),
					'</textarea>
				</label>
			</div>
			<hr>
			<div id="registration-submit">
				<input type="submit" value="', WT_I18N::translate('continue'), '">
			</div>
		</form>
	</div>
	</div>';
	break;

case 'userverify':
	if (!WT_Site::getPreference('USE_REGISTRATION_MODULE')) {
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
		exit;
	}

	// Change to the new user’s language
	$user = User::findByIdentifier($user_name);

	WT_I18N::init($user->getPreference('language'));

	$controller->setPageTitle(WT_I18N::translate('User verification'));
	$controller->pageHeader();

	echo '<div id="login-register-page">
		<form id="verify-form" name="verify-form" method="post" action="', WT_LOGIN_URL, '">
			<input type="hidden" name="action" value="verify_hash">
			<h4>', WT_I18N::translate('User verification'), '</h4>
			<div>
				<label for="username">', WT_I18N::translate('Username'), '</label>
				<input type="text" id="username" name="user_name" value="', $user_name, '">
			</div>
			<div>
			<label for="user_password">', WT_I18N::translate('Password'), '</label>
			<input type="password" id="user_password" name="user_password" value="" autofocus>
			</div>
			<div>
			<label for="user_hashcode">', WT_I18N::translate('Verification code:'), '</label>
			<input type="text" id="user_hashcode" name="user_hashcode" value="', $user_hashcode, '">
			</div>
			<div>
				<input type="submit" value="', WT_I18N::translate('Send'), '">
			</div>
		</form>
	</div>';
	break;

case 'verify_hash':
	if (!WT_Site::getPreference('USE_REGISTRATION_MODULE')) {
		header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH);
		exit;
	}

	// switch language to webmaster settings
	$webmaster = User::find($WT_TREE->getPreference('WEBMASTER_USER_ID'));
	WT_I18N::init($webmaster->getPreference('language'));

	$user = User::findByIdentifier($user_name);
	$mail1_body =
		WT_I18N::translate('Hello administrator…') . WT_Mail::EOL . WT_Mail::EOL .
		/* I18N: %1$s is a real-name, %2$s is a username, %3$s is an email address */
		WT_I18N::translate(
			'A new user (%1$s) has requested an account (%2$s) and verified an email address (%3$s).',
			$user->getRealName(),
			$user->getUserName(),
			$user->getEmail()
		) . WT_Mail::EOL . WT_Mail::EOL;
	if ($REQUIRE_ADMIN_AUTH_REGISTRATION && !$user->getPreference('verified_by_admin')) {
		$mail1_body .= WT_I18N::translate('You now need to review the account details, and set the “approved” status to “yes”.');
	} else {
		$mail1_body .= WT_I18N::translate('You do not have to take any action; the user can now login.');
	}
	$mail1_body .=
		WT_Mail::EOL .
		'<a href="'. WT_SERVER_NAME.WT_SCRIPT_PATH."admin_users.php?filter=" . WT_Filter::escapeUrl($user->getUserName()) . '">' .
		WT_SERVER_NAME.WT_SCRIPT_PATH."admin_users.php?filter=" . WT_Filter::escapeUrl($user->getUserName()) .
		'</a>' .
		WT_Mail::auditFooter();

	$mail1_subject = /* I18N: %s is a server name/URL */ WT_I18N::translate('New user at %s', WT_SERVER_NAME . WT_SCRIPT_PATH . ' ' . $WT_TREE->tree_title);

	// Change to the new user’s language
	WT_I18N::init($user->getPreference('language'));

	$controller->setPageTitle(WT_I18N::translate('User verification'));
	$controller->pageHeader();

	echo '<div id="login-register-page">';
	echo '<h2>'.WT_I18N::translate('User verification').'</h2>';
	echo '<div id="user-verify">';
	echo WT_I18N::translate('The data for the user <b>%s</b> was checked.', $user_name);
	if ($user) {
		if ($user->checkPassword($user_password) && $user->getPreference('reg_hashcode') == $user_hashcode) {
			WT_Mail::send(
			// “From:” header
				$WT_TREE,
				// “To:” header
				$webmaster->getEmail(),
				$webmaster->getRealName(),
				// “Reply-To:” header
				$WEBTREES_EMAIL,
				$WEBTREES_EMAIL,
				// Message body
				$mail1_subject,
				$mail1_body
			);
			$mail1_method  = $webmaster->getPreference('CONTACT_METHOD');
			if ($mail1_method!='messaging3' && $mail1_method!='mailto' && $mail1_method!='none') {
				WT_DB::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
					->execute(array($user_name, $WT_REQUEST->getClientIp(), $webmaster->getUserId(), $mail1_subject, WT_Filter::unescapeHtml($mail1_body)));
			}

			$user
				->setPreference('verified', '1')
				->setPreference('reg_timestamp', date('U'))
				->deletePreference('reg_hashcode');

			if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) {
				set_user_setting($user_id, 'verified_by_admin', 1);
			}
			Log::addAuthenticationLog('User ' . $user_name . ' verified their email address');

			echo '<br><br>'.WT_I18N::translate('You have confirmed your request to become a registered user.').'<br><br>';
			if ($REQUIRE_ADMIN_AUTH_REGISTRATION && !$user->getPreference('verified_by_admin')) {
				echo WT_I18N::translate('The administrator has been informed.  As soon as he gives you permission to login, you can login with your user name and password.');
			} else {
				echo WT_I18N::translate('You can now login with your user name and password.');
			}
			echo '<br><br>';
		} else {
			Log::addAuthenticationLog('User ' . $user_name . ' failed to verify their email address');
			echo '<br><br>';
			echo '<span class="warning">';
			echo WT_I18N::translate('Data was not correct, please try again');
			echo '</span><br><br>';
		}
	} else {
		echo '<br><br>';
		echo '<span class="warning">';
		echo WT_I18N::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
		echo '</span>';
	}
	echo '</div>';
	echo '</div>';
	break;
}
