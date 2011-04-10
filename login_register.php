<?php
// Register as a new User or request new password if it is lost
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// @version $Id$

define('WT_SCRIPT_NAME', 'login_register.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$REQUIRE_ADMIN_AUTH_REGISTRATION=get_site_setting('REQUIRE_ADMIN_AUTH_REGISTRATION');

$action         =safe_POST('action');
$user_realname  =safe_POST('user_realname');
$url            =safe_POST('url',             WT_REGEX_URL, 'index.php');
$time           =safe_POST('time');
$user_name      =safe_POST('user_name',       WT_REGEX_USERNAME);
$user_email     =safe_POST('user_email',      WT_REGEX_EMAIL);
$user_password01=safe_POST('user_password01', WT_REGEX_PASSWORD);
$user_password02=safe_POST('user_password02', WT_REGEX_PASSWORD);
$user_language  =safe_POST('user_language', array_keys(WT_I18N::installed_languages()), WT_LOCALE);
$user_gedcomid  =safe_POST('user_gedcomid');
$user_comments  =safe_POST('user_comments');
$user_password  =safe_POST('user_password');
$user_hashcode  =safe_POST('user_hashcode');
if (empty($action)) $action = safe_GET('action');
if (empty($user_name)) $user_name = safe_GET('user_name', WT_REGEX_USERNAME);
if (empty($user_hashcode)) $user_hashcode = safe_GET('user_hashcode');

$message="";

switch ($action) {
	case "pwlost" :
		print_header(WT_I18N::translate('Lost password request'));
		?>
		<script type="text/javascript">
		<!--
			function checkform(frm) {
				/*
				if (frm.user_email.value == "") {
					alert("<?php echo WT_I18N::translate('You must enter an email address.'); ?>");
					frm.user_email.focus();
					return false;
				}
				*/
				return true;
			}
		//-->
		</script>
		<div class="center">
			<form name="requestpwform" action="login_register.php" method="post" onsubmit="t = new Date(); document.requestpwform.time.value=t.toUTCString(); return checkform(this);">
			<input type="hidden" name="time" value="" />
			<input type="hidden" name="action" value="requestpw" />
			<span class="warning"><?php echo $message; ?></span>
			<table class="center facts_table width25">
				<tr><td class="topbottombar" colspan="2"><?php echo WT_I18N::translate('Lost password request'), help_link('pls_note11'); ?></td></tr>
				<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="username"><?php echo WT_I18N::translate('User name'); ?></label></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" id="username" name="user_name" value="" /></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php echo WT_I18N::translate('Lost password request'); ?>" /></td></tr>
			</table>
			</form>
		</div>
		<script type="text/javascript">
			document.requestpwform.user_name.focus();
		</script>
		<?php
		break;

	case "requestpw" :
		print_header(WT_I18N::translate('Lost password request'));
		echo "<div class=\"center\">";
		$user_id=get_user_id($user_name);
		if (!$user_id) {
			AddToLog("New password requests for user ".$user_name." that does not exist", 'auth');
			echo "<span class=\"warning\">";
			echo WT_I18N::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
			echo "</span><br />";
		} else {
			if (getUserEmail($user_id)=='') {
				AddToLog("Unable to send password to user ".$user_name." because they do not have an email address", 'auth');
				echo "<span class=\"warning\">";
				echo WT_I18N::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
				echo "</span><br />";
			} else {
				$passchars = "abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$user_new_pw = "";
				$max = strlen($passchars)-1;
				for ($i=0; $i<8; $i++) {
					$index = rand(0,$max);
					$user_new_pw .= $passchars{$index};
				}

				set_user_password($user_id, crypt($user_new_pw));
				set_user_setting($user_id, 'pwrequested', 1);

				// switch language to user settings
				WT_I18N::init(get_user_setting($user_id, 'language'));
				$newuserName=getUserFullName($user_id);

				$mail_body = "";
				$mail_body .= WT_I18N::translate('Hello %s ...', $newuserName) . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('A new password was requested for your user name.') . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('User name') . ": " . $user_name . "\r\n";

				$mail_body .= WT_I18N::translate('Password') . ": " . $user_new_pw . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('Recommendation:') . "\r\n";
				$mail_body .= WT_I18N::translate('Please click on the link below or paste it into your browser, login with the new password, and change it immediately to keep the integrity of your data secure.') . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('After you have logged in, select the «My Account» link under the «My Page» menu and fill in the password fields to change your password.') . "\r\n\r\n";

				if ($TEXT_DIRECTION=="rtl") $mail_body .= "<a href=\"".WT_SERVER_NAME.WT_SCRIPT_PATH."\">".WT_SERVER_NAME.WT_SCRIPT_PATH."</a>";
				else $mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH;

				require_once WT_ROOT.'includes/functions/functions_mail.php';
				webtreesMail(getUserEmail($user_id), $WEBTREES_EMAIL, WT_I18N::translate('Data request at %s', WT_SERVER_NAME.WT_SCRIPT_PATH), $mail_body);

				?>
				<table class="center facts_table">
				<tr><td class="wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo WT_I18N::translate('Hello...<br /><br />An email with your new password was sent to the address we have on file for <b>%s</b>.<br /><br />Please check your email account; you should receive our message soon.<br /><br />Recommendation:<br />You should login to this site with your new password as soon as possible, and you should change your password to maintain your data\'s security.', $user_name); ?></td></tr>
				</table>
				<?php
				AddToLog("Password request was sent to user: ".$user_name, 'auth');

				WT_I18N::init(WT_LOCALE);   // Reset language
			}
		}
		echo "</div>";
		break;

	case "register" :
		$_SESSION["good_to_send"] = true;
		if (!get_site_setting('USE_REGISTRATION_MODULE')) {
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
			exit;
		}
		$message = "";
		if (!$user_name) {
			$message .= WT_I18N::translate('You must enter a user name.')."<br />";
			$user_name_false = true;
		}
		else $user_name_false = false;

		if (!$user_password01) {
			$message .= WT_I18N::translate('You must enter a password.')."<br />";
			$user_password01_false = true;
		}
		else $user_password01_false = false;

		if (!$user_password02) {
			$message .= WT_I18N::translate('You must confirm the password.')."<br />";
			$user_password02_false = true;
		}
		else $user_password02_false = false;

		if ($user_password01 != $user_password02) {
			$message .= WT_I18N::translate('Passwords do not match.')."<br />";
			$password_mismatch = true;
		}
		else $password_mismatch = false;

		if (!$user_realname) $user_realname_false = true;
		else $user_realname_false = false;

		if (!$user_email) $user_email_false = true;
		else $user_email_false = false;

		if (!$user_language) $user_language_false = true;
		else $user_language_false = false;

		if (!$user_comments) $user_comments_false = true;
		else $user_comments_false = false;

		if ($user_name_false == false && $user_password01_false == false && $user_password02_false == false && $user_realname_false == false && $user_email_false == false && $user_language_false == false && $user_comments_false == false && $password_mismatch == false) $action = "registernew";
		else {
			print_header(WT_I18N::translate('Request new user account'));
			// Empty user array in case any details might be left
			// and faulty users are requested and created
			$user = array();

			?>
			<script type="text/javascript">
			<!--
				function checkform(frm) {
					if (frm.user_name.value == "") {
						alert("<?php echo WT_I18N::translate('You must enter a user name.'); ?>");
						frm.user_name.focus();
						return false;
					}
					if (frm.user_password01.value == "") {
						alert("<?php echo WT_I18N::translate('You must enter a password.'); ?>");
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_password02.value == "") {
						alert("<?php echo WT_I18N::translate('You must confirm the password.'); ?>");
						frm.user_password02.focus();
						return false;
					}
					if (frm.user_password01.value != frm.user_password02.value) {
						alert("<?php echo WT_I18N::translate('Passwords do not match.'); ?>");
						frm.user_password01.value = "";
						frm.user_password02.value = "";
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_password01.value.length < 6) {
						alert("<?php echo WT_I18N::translate('Passwords must contain at least 6 characters.'); ?>");
						frm.user_password01.value = "";
						frm.user_password02.value = "";
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_realname.value == "") {
						alert("<?php echo WT_I18N::translate('You must enter your real name.'); ?>");
						frm.user_realname.focus();
						return false;
					}
					if ((frm.user_email.value == "")||(frm.user_email.value.indexOf('@')==-1)) {
						alert("<?php echo WT_I18N::translate('You must enter an email address.'); ?>");
						frm.user_email.focus();
						return false;
					}
					if (frm.user_comments.value == "") {
						alert("<?php echo WT_I18N::translate('Please enter your relationship to the data in the Comments field.'); ?>");
						frm.user_comments.focus();
						return false;
					}
					return true;
				}

			var pastefield;
			function paste_id(value) {
				pastefield.value=value;
			}
			//-->
			</script>
			<?php
				if ($SHOW_REGISTER_CAUTION) {
					echo "<center><table class=\"width50 ", $TEXT_DIRECTION, "\"><tr><td>";
					echo WT_I18N::translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living people listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div>');
					echo "<br />";
					echo "</td></tr></table></center>";
				}
			?>
			<div class="center">
				<form name="registerform" method="post" action="login_register.php" onsubmit="t = new Date(); document.registerform.time.value=t.toUTCString(); return checkform(this);">
					<input type="hidden" name="action" value="register" />
					<input type="hidden" name="time" value="" />
					<table class="center facts_table width50">
						<tr><td class="topbottombar" colspan="2"><?php echo WT_I18N::translate('Request new user account'); ?><br /><?php if (strlen($message) > 0) echo $message; ?></td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_realname"><?php echo WT_I18N::translate('Real name'), '</label>', help_link('new_user_realname'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" id="user_realname" name="user_realname" value="<?php if (!$user_realname_false) echo $user_realname; ?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_email"><?php echo WT_I18N::translate('Email address'), '</label>', help_link('edituser_email'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" size="30" id="user_email" name="user_email" value="<?php if (!$user_email_false) echo $user_email; ?>"  /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="username"><?php echo WT_I18N::translate('Desired user name'), '</label>', help_link('username'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" id="username" name="user_name" value="<?php if (!$user_name_false) echo $user_name; ?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_password01"><?php echo WT_I18N::translate('Desired password'), '</label>', help_link('edituser_password'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="password" id="user_password01" name="user_password01" value="" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_password02"><?php echo WT_I18N::translate('Confirm password'), '</label>', help_link('edituser_conf_password'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="password" id="user_password02" name="user_password02" value="" /> *</td></tr>
						<?php
						echo "<tr><td class=\"descriptionbox wrap ", $TEXT_DIRECTION, "\">";
						echo '<label for="user_language">', WT_I18N::translate('Change language'), '</label>', help_link('edituser_change_lang');
						echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
						echo edit_field_language('user_language', WT_LOCALE);
						echo '</td></tr>';
						if ($REQUIRE_AUTHENTICATION && $SHOW_LIVING_NAMES>=WT_PRIV_PUBLIC) { ?>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_gedcomid"><?php echo WT_I18N::translate('Individual record'), '</label>', help_link('register_gedcomid'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>" valign="top" ><input type="text" size="10" name="user_gedcomid" id="user_gedcomid" value="" /><?php print_findindi_link("user_gedcomid",""); ?></td></tr>
						<?php } ?>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_comments"><?php echo WT_I18N::translate('Comments'), '</label>', help_link('register_comments'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>" valign="top" ><textarea cols="50" rows="5" id="user_comments" name="user_comments"><?php if (!$user_comments_false) echo $user_comments; ?></textarea> *</td></tr>
						<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php echo WT_I18N::translate('Request new user account'); ?>" /></td></tr>
						<tr><td align="left" colspan="2" ><?php echo WT_I18N::translate('Fields marked with * are mandatory.'); ?></td></tr>
					</table>
				</form>
			</div>
			<script type="text/javascript">
				document.registerform.user_realname.focus();
			</script>
			<?php
			break;
		}

	case "registernew" :
		if (!get_site_setting('USE_REGISTRATION_MODULE')) {
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
			exit;
		}
		if ((stripos($user_name, "SUNTZU")!==false) || (stripos($user_email, "SUNTZU")!==false)) {
			AddToLog("SUNTZU hacker", 'auth');
			echo "Go Away!";
			exit;
		}

			//-- check referer for possible spam attack
			if (!isset($_SERVER['HTTP_REFERER']) || stristr($_SERVER['HTTP_REFERER'],"login_register.php")===false) {
				echo "<center><br /><span class=\"error\">Invalid page referer.</span>";
				echo "<br /><br /></center>";
				AddToLog('Invalid page referer while trying to register a user.  Possible spam attack.', 'auth');
				exit;
			}

			if ((!isset($_SESSION["good_to_send"]))||($_SESSION["good_to_send"]!==true)) {
				AddToLog('Invalid session reference while trying to register a user.  Possible spam attack.', 'auth');
				exit;
			}
			$_SESSION["good_to_send"] = false;

		if (isset($user_name)) {
			print_header(WT_I18N::translate('New Account confirmation'));
			echo "<div class=\"center\">";
			$user_created_ok = false;

			AddToLog("User registration requested for: ".$user_name, 'auth');

			if (get_user_id($user_name)) {
				echo "<span class=\"warning\">".WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.')."</span><br /><br />";
				echo "<a href=\"javascript:history.back()\">".WT_I18N::translate('Back')."</a><br />";
			} elseif (get_user_by_email($user_email)) {
				echo "<span class=\"warning\">".WT_I18N::translate('Duplicate email address.  A user with that email already exists.')."</span><br /><br />";
				echo "<a href=\"javascript:history.back()\">".WT_I18N::translate('Back')."</a><br />";
			} elseif ($user_password01 == $user_password02) {
				if ($user_id=create_user($user_name, $user_realname, $user_email, crypt($user_password01))) {
					set_user_setting($user_id, 'language',            $user_language);
					set_user_setting($user_id, 'verified',             0);
					set_user_setting($user_id, 'verified_by_admin',    !$REQUIRE_ADMIN_AUTH_REGISTRATION);
					set_user_setting($user_id, 'reg_timestamp',        date('U'));
					set_user_setting($user_id, 'reg_hashcode',         md5(crypt($user_name)));
					set_user_setting($user_id, 'contactmethod',        "messaging2");
					set_user_setting($user_id, 'defaulttab',           get_gedcom_setting(WT_GED_ID, 'GEDCOM_DEFAULT_TAB'));
					set_user_setting($user_id, 'visibleonline',        1);
					set_user_setting($user_id, 'editaccount',          1);
					set_user_setting($user_id, 'auto_accept',          0);
					set_user_setting($user_id, 'canadmin',             0);
					set_user_setting($user_id, 'sessiontime',          0);
					if (!empty($user_gedcomid)) {
						set_user_gedcom_setting($user_id, $GEDCOM, 'gedcomid', $user_gedcomid);
						set_user_gedcom_setting($user_id, $GEDCOM, 'rootid',   $user_gedcomid);
					}
					$user_created_ok = true;
				} else {
					echo "<span class=\"warning\">".WT_I18N::translate('Unable to add user.  Please try again.')."<br /></span>";
					echo "<a href=\"javascript:history.back()\">".WT_I18N::translate('Back')."</a><br />";
				}
			} else {
				echo "<span class=\"warning\">".WT_I18N::translate('Passwords do not match.')."</span><br />";
				echo "<a href=\"javascript:history.back()\">".WT_I18N::translate('Back')."</a><br />";
			}
			if ($user_created_ok) {
				// switch to the user's language
				WT_I18N::init($user_language);
				$mail_body = "";
				$mail_body .= WT_I18N::translate('Hello %s ...', $user_realname) . "\r\n\r\n";
				$mail_body .= /* I18N: %s placeholders are the site URL and an email address */ WT_I18N::translate('A request was received at %s to create a webtrees account with your email address %s.', WT_SERVER_NAME.WT_SCRIPT_PATH, $user_email) . "  ";
				$mail_body .= WT_I18N::translate('Information about the request is shown under the link below.') . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('Please click on the following link and fill in the requested data to confirm your request and email address.') . "\r\n\r\n";
				if ($TEXT_DIRECTION=="rtl") {
					$mail_body .= "<a href=\"";
					$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH . "login_register.php?user_name=".urlencode($user_name)."&user_hashcode=".urlencode(get_user_setting($user_id, 'reg_hashcode'))."&action=userverify\">";
				}
				$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH . "login_register.php?user_name=".urlencode($user_name)."&user_hashcode=".urlencode(get_user_setting($user_id, 'reg_hashcode'))."&action=userverify";
				if ($TEXT_DIRECTION=="rtl") $mail_body .= "</a>";
				$mail_body .= "\r\n";
				$mail_body .= WT_I18N::translate('User name') . " " . $user_name . "\r\n";
				$mail_body .= WT_I18N::translate('Verification code:') . " " . get_user_setting($user_id, 'reg_hashcode') . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('Comments').": " . $user_comments . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('If you didn\'t request an account, you can just delete this message.') . "  ";
				$mail_body .= WT_I18N::translate('You won\'t get any more email from this site, because the account request will be deleted automatically after seven days.') . "\r\n";
				require_once WT_ROOT.'includes/functions/functions_mail.php';
				webtreesMail($user_email, $WEBTREES_EMAIL, WT_I18N::translate('Your registration at %s', WT_SERVER_NAME.WT_SCRIPT_PATH), $mail_body);

				// switch language to webmaster settings
				$webmaster_user_id=get_gedcom_setting(WT_GED_ID, 'WEBMASTER_USER_ID');
				WT_I18N::init(get_user_setting($webmaster_user_id, 'language'));

				$mail_body = "";
				$mail_body .= WT_I18N::translate('Hello Administrator ...') . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('A prospective user has registered with webtrees at %s.', WT_SERVER_NAME.WT_SCRIPT_PATH) . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('User name') . " " . $user_name . "\r\n";
				$mail_body .= WT_I18N::translate('Real name') . " " . $user_realname . "\r\n";
				$mail_body .= WT_I18N::translate('Email Address:') . " " . $user_email . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('Comments').": " . $user_comments . "\r\n\r\n";
				$mail_body .= WT_I18N::translate('The user has been sent an e-mail with the information necessary to confirm the access request') . "\r\n\r\n";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) $mail_body .= WT_I18N::translate('You will be informed by e-mail when this prospective user has confirmed the request.  You can then complete the process by activating the user name.  The new user will not be able to login until you activate the account.') . "\r\n";
				else $mail_body .= WT_I18N::translate('You will be informed by e-mail when this prospective user has confirmed the request.  After this, the user will be able to login without any action on your part.') . "\r\n";

				$message = array();
				$message["to"]=get_user_name($webmaster_user_id);
				$message["from"]=$user_name;
				$message["subject"] = WT_I18N::translate('New registration at %s', WT_SERVER_NAME.WT_SCRIPT_PATH);
				$message["body"] = $mail_body;
				$message["created"] = $time;
				$message["method"] = get_user_setting($webmaster_user_id, 'contact_method');
				addMessage($message);

				// switch language to user's settings
				WT_I18N::init($user_language);
				?>
				<table class="center facts_table">
					<tr><td class="wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo WT_I18N::translate('Hello %s ...<br />Thank you for your registration.', $user_realname); ?><br /><br />
					<?php
					if ($REQUIRE_ADMIN_AUTH_REGISTRATION) echo WT_I18N::translate('We will now send a confirmation email to the address <b>%s</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br /><br />After you have followed the instructions in the confirmation email, the administrator still has to approve your request before your account can be used.<br /><br />To login to this site, you will need to know your user name and password.', $user_email);
					else echo WT_I18N::translate('We will now send a confirmation email to the address <b>%s</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br /><br />After you have followed the instructions in the confirmation email, you can login.  To login to this site, you will need to know your user name and password.', $user_email);
					?>
					</td></tr>
				</table>
				<?php
				WT_I18N::init(WT_LOCALE); // Reset language
			}
			echo "</div>";
		} else {
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php');
			exit;
		}
		break;

	case "userverify" :
		if (!get_site_setting('USE_REGISTRATION_MODULE')) {
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
			exit;
		}

		// Change to the new user's language
		$user_id=get_user_id($user_name);
		WT_I18N::init(get_user_setting($user_id, 'language'));

		print_header(WT_I18N::translate('User verification'));
		echo "<div class=\"center\">";
		?>
		<form name="verifyform" method="post" action="" onsubmit="t = new Date(); document.verifyform.time.value=t.toUTCString();">
			<input type="hidden" name="action" value="verify_hash" />
			<input type="hidden" name="time" value="" />
			<table class="center facts_table width25">
				<tr><td class="topbottombar" colspan="2"><?php echo WT_I18N::translate('User verification'), help_link('pls_note07'); ?></td></tr>
				<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="username"><?php echo WT_I18N::translate('User name'); ?></label></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" id="username" name="user_name" value="<?php echo $user_name; ?>" /></td></tr>
				<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_password"><?php echo WT_I18N::translate('Password'); ?></label></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="password" id="user_password" name="user_password" value="" /></td></tr>
				<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><label for="user_hashcode"><?php echo WT_I18N::translate('Verification code:'); ?></label></td><td class="facts_value <?php echo $TEXT_DIRECTION; ?>"><input type="text" id="user_hashcode" name="user_hashcode" value="<?php echo $user_hashcode; ?>" /></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php echo WT_I18N::translate('Send'); ?>" /></td></tr>
			</table>
		</form>
		</div>
		<script type="text/javascript">
			document.verifyform.user_password.focus();
		</script>
		<?php
		break;

	case "verify_hash" :
		if (!get_site_setting('USE_REGISTRATION_MODULE')) {
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
			exit;
		}
		AddToLog("User attempted to verify hashcode: ".$user_name, 'auth');

		// Change to the new user's language
		$user_id=get_user_id($user_name);
		WT_I18N::init(get_user_setting($user_id, 'language'));

		print_header(WT_I18N::translate('User verification')); // <-- better verification of authentication code
		echo "<div class=\"center\">";
		echo "<table class=\"center facts_table wrap ".$TEXT_DIRECTION."\">";
		echo "<tr><td class=\"topbottombar\">".WT_I18N::translate('User verification')."</td></tr>";
		echo "<tr><td class=\"optionbox\">";
		echo WT_I18N::translate('The data for the user <b>%s</b> was checked.', $user_name);
		if ($user_id) {
			$pw_ok = (get_user_password($user_id) == crypt($user_password, get_user_password($user_id)));
			$hc_ok = (get_user_setting($user_id, 'reg_hashcode') == $user_hashcode);
			if (($pw_ok) && ($hc_ok)) {
				set_user_setting($user_id, 'verified', 1);
				set_user_setting($user_id, 'pwrequested', null);
				set_user_setting($user_id, 'reg_timestamp', date("U"));
				set_user_setting($user_id, 'reg_hashcode', null);
				if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) {
					set_user_setting($user_id, 'verified_by_admin', 1);
				}
				AddToLog("User verified: ".$user_name, 'auth');

				// switch language to webmaster settings
				$webmaster_user_id=get_gedcom_setting(WT_GED_ID, 'WEBMASTER_USER_ID');
				WT_I18N::init(get_user_setting($webmaster_user_id, 'language'));

				$mail_body = "";
				$mail_body .= WT_I18N::translate('Hello Administrator ...') . "\r\n\r\n";
				$mail_body .= /* I18N: User <login-id> (<real name>) has ... */ WT_I18N::translate('User %s (%s) has confirmed their request for an account.', $user_name, getUserFullName($user_id)) . "\r\n\r\n";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) $mail_body .= WT_I18N::translate('Please click on the link below to login to your site.  You must Edit the user to activate the account so that he can login to your site.') . "\r\n";
				else $mail_body .= WT_I18N::translate('You do not have to take any action; the user can now login.') . "\r\n";

				if ($TEXT_DIRECTION=="rtl") {
					$mail_body .= "<a href=\"";
					$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH."admin_users.php?action=edituser&username=" . urlencode($user_name) . "\">";
				}
				$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH."admin_users.php?action=edituser&username=" . urlencode($user_name);
				if ($TEXT_DIRECTION=="rtl") $mail_body .= "</a>";
				$mail_body .= "\r\n";

				$message = array();
				$message["to"]=get_user_name($webmaster_user_id);
				$message["from"]=$user_name;
				$message["subject"] = WT_I18N::translate('New user at %s', WT_SERVER_NAME.WT_SCRIPT_PATH);
				$message["body"] = $mail_body;
				$message["created"] = $time;
				$message["method"] = get_user_setting($webmaster_user_id, 'CONTACT_METHOD');
				addMessage($message);

				WT_I18N::init(WT_LOCALE); // Reset language

				echo "<br /><br />".WT_I18N::translate('You have confirmed your request to become a registered user.')."<br /><br />";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) echo WT_I18N::translate('The Administrator has been informed.  As soon as he gives you permission to login, you can login with your user name and password.');
				else echo WT_I18N::translate('You can now login with your user name and password.');
				echo "<br /><br /></td></tr>";
			} else {
				echo "<br /><br />";
				echo "<span class=\"warning\">";
				echo WT_I18N::translate('Data was not correct, please try again');
				echo "</span><br /><br /></td></tr>";
			}
		} else {
			echo "<br /><br />";
			echo "<span class=\"warning\">";
			echo WT_I18N::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
			echo "</span><br /><br /></td></tr>";
		}
		echo "</table>";
		echo "</div>";
		break;

	default :
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.$url);
		break;
}

print_footer();
