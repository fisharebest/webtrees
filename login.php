<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

// If we are already logged in, then go to the “Home page”
if (Auth::check() && $WT_TREE) {
	header('Location: index.php');

	return;
}

$request = Request::createFromGlobals();

$controller = new PageController;

$action          = Filter::post('action');
$user_realname   = Filter::post('user_realname');
$user_email      = Filter::post('user_email');
$user_password01 = Filter::post('user_password01', WT_REGEX_PASSWORD);
$user_password02 = Filter::post('user_password02', WT_REGEX_PASSWORD);
$user_comments   = Filter::post('user_comments');
$user_password   = Filter::post('user_password');
$user_hashcode   = Filter::post('user_hashcode');
$url             = Filter::post('url'); // Not actually a URL - just a path
$username        = Filter::post('username');
$password        = Filter::post('password');

// These parameters may come from the URL which is emailed to users.
if (!$action) {
	$action = Filter::get('action');
}
if (!$username) {
	$username = Filter::get('username');
}
if (!$user_hashcode) {
	$user_hashcode = Filter::get('user_hashcode');
}
if (!$url) {
	$url = Filter::get('url');
}

$message = '';

switch ($action) {
	case 'login':
		try {
			if (!$_COOKIE) {
				Log::addAuthenticationLog('Login failed (no session cookies): ' . $username);
				throw new \Exception(I18N::translate('You cannot sign in because your browser does not accept cookies.'));
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
				throw new \Exception(I18N::translate('This account has not been verified. Please check your email for a verification message.'));
			}

			if (!$user->getPreference('verified_by_admin')) {
				Log::addAuthenticationLog('Login failed (not approved by admin): ' . $username);
				throw new \Exception(I18N::translate('This account has not been approved. Please wait for an administrator to approve it.'));
			}

			Auth::login($user);
			Log::addAuthenticationLog('Login: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
			Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);

			Session::put('locale', Auth::user()->getPreference('language'));
			Session::put('theme_id', Auth::user()->getPreference('theme'));
			I18N::init(Auth::user()->getPreference('language'));

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
					" WHERE setting_name = 'gedcomid' AND user_id = :user_id" .
					" ORDER BY gedcom_id = :tree_id DESC"
				)->execute([
					'user_id' => Auth::user()->getUserId(),
					'tree_id' => $WT_TREE->getTreeId(),
				])->fetchOne();
				$url .= '&ged=' . rawurlencode($tree);
			}

			// Redirect to the target URL
			header('Location: ' . $url);

			return;
		} catch (\Exception $ex) {
			DebugBar::addThrowable($ex);

			$message = $ex->getMessage();
		}
		// No break;

	default:
		$controller
			->setPageTitle(I18N::translate('Sign in'))
			->pageHeader()
			->addInlineJavascript('
			$("#new_passwd_form").hide();
			$("#passwd_click").click(function() {
				$("#new_passwd_form").slideToggle(100, function() {
					$("#new_passwd_username").focus()
				});
				return false;
			});
		');

		echo '<div id="login-page">';
		echo '<div id="login-text">';

		echo '<p class="center"><strong>' . I18N::translate('Welcome to this genealogy website') . '</strong></p>';

		switch (Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
			case 1:
				echo '<p>' . I18N::translate('Anyone with a user account can access this website.') . ' ' . I18N::translate('You can apply for an account using the link below.') . '</p>';
				break;
			case 2:
				echo '<p>' . I18N::translate('You need to be an authorized user to access this website.') . ' ' . I18N::translate('You can apply for an account using the link below.') . '</p>';
				break;
			case 3:
				echo '<p>' . I18N::translate('You need to be a family member to access this website.') . ' ' . I18N::translate('You can apply for an account using the link below.') . '</p>';
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
		<input type="hidden" name="url" value="', Html::escape($url), '">';
		echo '<div>
			<label for="username">', I18N::translate('Username'),
			'<input type="text" id="username" name="username" value="', Html::escape($username), '" class="formField" autofocus>
			</label>
		</div>
		<div>
			<label for="password">', I18N::translate('Password'),
				'<input type="password" id="password" name="password" class="formField">
			</label>
		</div>
		<div>
			<input type="submit" value="', /* I18N: A button label. */ I18N::translate('sign in'), '">
		</div>
		';
		// Emails are sent from a TREE, not from a SITE. Therefore if there is no
		// tree available (initial setup or all trees private), then we can't send email.
		if ($WT_TREE) {
			echo '
			<div>
				<a href="#" id="passwd_click">', I18N::translate('Forgot password?'), '</a>
			</div>';
			if (Site::getPreference('USE_REGISTRATION_MODULE') === '1') {
				echo '<div><a href="' . WT_LOGIN_URL . '?action=register">', I18N::translate('Request a new user account'), '</a></div>';
			}
		}
	echo '</form>';

	// hidden New Password block
	echo '<div id="new_passwd">
		<form id="new_passwd_form" name="new_passwd_form" action="' . WT_LOGIN_URL . '" method="post">
		<input type="hidden" name="action" value="requestpw">
		<h4>', I18N::translate('Request a new password'), '</h4>
		<div>
			<label for="new_passwd_username">', I18N::translate('Username or email address'),
				'<input type="text" id="new_passwd_username" name="new_passwd_username" value="">
			</label>
		</div>
		<div><input type="submit" value="', /* I18N: A button label. */ I18N::translate('continue'), '"></div>
		</form>
	</div>';
	echo '</div>';

	echo '</div>';
	break;

	case 'requestpw':
		$username = Filter::post('new_passwd_username');
		$user     = User::findByIdentifier($username);

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

			$sender = new User(
				(object) [
					'user_id'   => null,
					'user_name' => '',
					'real_name' => $WT_TREE->getTitle(),
					'email'     => $WT_TREE->getPreference('WEBTREES_EMAIL'),
				]
			);

			Mail::send(
				$sender,
				$user,
				$sender,
				I18N::translate('Lost password request'),
				View::make('emails/password-reset-text', ['user' => $user, 'new_password' => $user_new_pw]),
				View::make('emails/password-reset-html', ['user' => $user, 'new_password' => $user_new_pw])
			);

			FlashMessages::addMessage(I18N::translate('A new password has been created and emailed to %s. You can change this password after you sign in.', Html::escape($username)), 'success');
		} else {
			FlashMessages::addMessage(I18N::translate('There is no account with the username or email “%s”.', Html::escape($username)), 'danger');
		}
		header('Location: login.php');

		return;
		break;

	case 'register':
		if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
			header('Location: index.php');

			return;
		}

		$controller->setPageTitle(I18N::translate('Request a new user account'));

		// The form parameters are mandatory, and the validation errors are shown in the client.
		if (Session::get('good_to_send') && $username && $user_password01 && $user_password01 == $user_password02 && $user_realname && $user_email && $user_comments) {

			// These validation errors cannot be shown in the client.
			if (User::findByUserName($username)) {
				FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
			} elseif (User::findByEmail($user_email)) {
				FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
			} elseif (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $user_comments, $match)) {
				FlashMessages::addMessage(
					I18N::translate('You are not allowed to send messages that contain external links.') . ' ' .
					I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1])
				);
				Log::addAuthenticationLog('Possible spam registration from "' . $username . '"/"' . $user_email . '" comments="' . $user_comments . '"');
			} else {
				// Everything looks good - create the user
				$controller->pageHeader();
				Log::addAuthenticationLog('User registration requested for: ' . $username);

				$user = User::create($username, $user_realname, $user_email, $user_password01);
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

				// Create a dummy user, so we can send messages from the tree.
				$sender = new User(
					(object) [
						'user_id'   => null,
						'user_name' => '',
						'real_name' => $WT_TREE->getTitle(),
						'email'     => $WT_TREE->getPreference('WEBTREES_EMAIL'),
					]
				);

				// Send a verification message to the user.
				Mail::send(
					$sender,
					$user,
					$sender,
					/* I18N: %s is a server name/URL */ I18N::translate('Your registration at %s', WT_BASE_URL),
					View::make('emails/register-user-text', ['tree' => $WT_TREE, 'user' => $user]),
					View::make('emails/register-user-html', ['tree' => $WT_TREE, 'user' => $user])
				);

				// Tell the genealogy contact about the registration.
				$webmaster = User::find($WT_TREE->getPreference('WEBMASTER_USER_ID'));
				I18N::init($webmaster->getPreference('language'));

				Mail::send(
					$sender,
					$webmaster,
					$user,
					/* I18N: %s is a server name/URL */ I18N::translate('New registration at %s', WT_BASE_URL . ' ' . $WT_TREE->getTitle()),
					View::make('emails/register-notify-text', ['tree' => $WT_TREE, 'user' => $user, 'comments' => $user_comments]),
					View::make('emails/register-notify-html', ['tree' => $WT_TREE, 'user' => $user, 'comments' => $user_comments])
				);

				$mail1_method = $webmaster->getPreference('contact_method');
				if ($mail1_method !== 'messaging3' && $mail1_method !== 'mailto' && $mail1_method !== 'none') {
					Database::prepare(
						"INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)"
					)->execute([
						$user->getEmail(),
						$request->getClientIp(),
						$webmaster->getUserId(),
						/* I18N: %s is a server name/URL */ I18N::translate('New registration at %s', $WT_TREE->getTitle()),
						View::make('emails/register-notify-text', ['tree' => $WT_TREE, 'user' => $user, 'comments' => $user_comments]),
					]);
				}
				I18N::init(WT_LOCALE);

				echo '<div id="login-register-page">';
				echo '<div class="confirm"><p>', I18N::translate('Hello %s…<br>Thank you for your registration.', $user->getRealNameHtml()), '</p>';
				echo '<p>', I18N::translate('We will now send a confirmation email to the address <b>%s</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically. You will have to apply again.<br><br>After you have followed the instructions in the confirmation email, the administrator still has to approve your request before your account can be used.<br><br>To sign in to this website, you will need to know your username and password.', $user->getEmail()), '</p>';
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
		<h2><?= $controller->getPageTitle() ?></h2>

		<?php if (Site::getPreference('SHOW_REGISTER_CAUTION') === '1'): ?>
			<div id="register-text">
			<?= I18N::translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living individuals listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our website.</li></ul></div>') ?>
			</div>
		<?php endif ?>
		<div id="register-box">
			<form id="register-form" name="register-form" method="post" onsubmit="return checkform(this);" autocomplete="off">
				<input type="hidden" name="action" value="register">
				<h4><?= I18N::translate('All fields must be completed.') ?></h4>
				<hr>

				<div>
					<label for="user_realname">
						<?= I18N::translate('Real name') ?>
							<input type="text" id="user_realname" name="user_realname" required maxlength="64" value="<?= Html::escape($user_realname) ?>" autofocus>
					</label>
					<p class="small text-muted">
						<?= I18N::translate('This is your real name, as you would like it displayed on screen.') ?>
					</p>
				</div>

				<div>
					<label for="user_email">
						<?= I18N::translate('Email address') ?>
							<input type="email" id="user_email" name="user_email" required maxlength="64" value="<?= Html::escape($user_email) ?>">
					</label>
					<p class="small text-muted">
						<?= I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.') ?>
					</p>
				</div>

				<div>
					<label for="username">
						<?= I18N::translate('Username') ?>
							<input type="text" id="username" name="username" required maxlength="32" value="<?php Html::escape($username) ?>">
					</label>
					<p class="small text-muted">
						<?= I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.') ?>
					</p>
				</div>

				<div>
					<label for="user_password01">
						<?= I18N::translate('Password') ?>
						<input required
							type="password"
							id="user_password01" name="user_password01"
							value="<?= Html::escape($user_password01) ?>"
							placeholder="<?= /* I18N: placeholder text for new-password field */ I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, I18N::number(WT_MINIMUM_PASSWORD_LENGTH)) ?>"
							pattern="<?=  WT_REGEX_PASSWORD ?>"
							onchange="form.user_password02.pattern = regex_quote(this.value);"
						>
					</label>
					<p class="small text-muted">
						<?= I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.') ?>
					</p>
				</div>

				<div>
					<label for="user_password02">
						<?= I18N::translate('Confirm password') ?>
						<input required
							type="password"
							id="user_password02" name="user_password02"
							value="<?= Html::escape($user_password02) ?>"
							placeholder="<?= /* I18N: placeholder text for repeat-password field */ I18N::translate('Type the password again.') ?>"
							pattern="<?= WT_REGEX_PASSWORD ?>"
						>
					</label>
					<p class="small text-muted">
						<?= I18N::translate('Type your password again, to make sure you have typed it correctly.') ?>
					</p>
				</div>

				<div>
					<label for="user_comments">
						<?= I18N::translate('Comments') ?>
						<textarea required
							cols="50" rows="5"
							id="user_comments" name="user_comments"
							placeholder="<?php /* I18N: placeholder text for registration-comments field */ I18N::translate('Explain why you are requesting an account.') ?>"
						><?= Html::escape($user_comments) ?></textarea>
					</label>
					<p class="small text-muted">
						<?= I18N::translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site. You can also use this to enter any other comments you may have for the site administrator.') ?>
					</p>
				</div>

				<hr>

				<div id="registration-submit">
					<input type="submit" value="<?= I18N::translate('continue') ?>">
				</div>
			</form>
		</div>
	</div>
	<?php
		break;

	case 'userverify':
		if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
			header('Location: index.php');

			return;
		}

		$controller->setPageTitle(I18N::translate('User verification'));
		$controller->pageHeader();

		if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
			header('Location: index.php');

			return;
		}

		$user = User::findByUserName($username);

		if ($user && $user->getPreference('reg_hashcode') === $user_hashcode) {
			// switch language to webmaster settings
			$webmaster = User::find($WT_TREE->getPreference('WEBMASTER_USER_ID'));
			I18N::init($webmaster->getPreference('language'));

			// Create a dummy user, so we can send messages from the tree.
			$sender = new User(
				(object) [
					'user_id'   => null,
					'user_name' => '',
					'real_name' => $WT_TREE->getTitle(),
					'email'     => $WT_TREE->getPreference('WEBTREES_EMAIL'),
				]
			);

			Mail::send(
				$sender,
				$webmaster,
				$sender,
				/* I18N: %s is a server name/URL */ I18N::translate('New user at %s', WT_BASE_URL . ' ' . $WT_TREE->getTitle()),
				View::make('emails/verify-notify-text', ['user' => $user]),
				View::make('emails/verify-notify-html', ['user' => $user])
			);

			$mail1_method = $webmaster->getPreference('CONTACT_METHOD');
			if ($mail1_method !== 'messaging3' && $mail1_method !== 'mailto' && $mail1_method !== 'none') {
				Database::prepare(
					"INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)"
				)->execute([
					$username,
					$request->getClientIp(),
					$webmaster->getUserId(),
					/* I18N: %s is a server name/URL */ I18N::translate('New user at %s', WT_BASE_URL . ' ' . $WT_TREE->getTitle()),
					View::make('emails/verify-notify-text', ['user' => $user])
				]);
			}
			I18N::init(WT_LOCALE);

			$user
				->setPreference('verified', '1')
				->setPreference('reg_timestamp', date('U'))
				->setPreference('reg_hashcode', '');

			Log::addAuthenticationLog('User ' . $username . ' verified their email address');

			echo '<div id="login-register-page">';
			echo '<h2>' . I18N::translate('User verification') . '</h2>';
			echo '<div id="user-verify">';
			echo '<p>', I18N::translate('You have confirmed your request to become a registered user.'), '</p>';
			echo '<p>', I18N::translate('The administrator has been informed. As soon as they give you permission to sign in, you can sign in with your username and password.'), '</p>';
		} else {
			echo '<p class="warning">';
			echo I18N::translate('Could not verify the information you entered. Please try again or contact the site administrator for more information.');
			echo '</p>';
		}
		echo '</div>';
		echo '</div>';
		break;
}
