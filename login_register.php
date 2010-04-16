<?php
/**
 * Register as a new User or request new password if it is lost
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * This Page Is Valid XHTML 1.0 Transitional! > 29 August 2005
 *
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'login_register.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$action         =safe_POST('action');
$user_realname  =safe_POST('user_realname');
$url            =safe_POST('url',             WT_REGEX_URL, 'index.php');
$time           =safe_POST('time');
$user_name      =safe_POST('user_name',       WT_REGEX_USERNAME);
$user_email     =safe_POST('user_email',      WT_REGEX_EMAIL);
$user_password01=safe_POST('user_password01', WT_REGEX_PASSWORD);
$user_password02=safe_POST('user_password02', WT_REGEX_PASSWORD);
$user_language  =safe_POST('user_language', array_keys(i18n::installed_languages()), WT_LOCALE);
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
		print_header(i18n::translate('Lost password request'));
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
			function checkform(frm) {
				/*
				if (frm.user_email.value == "") {
					alert("<?php print i18n::translate('You must enter an email address.'); ?>");
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
			<span class="warning"><?php print $message?></span>
			<table class="center facts_table width25">
				<tr><td class="topbottombar" colspan="2"><?php echo i18n::translate('Lost password request'), help_link('pls_note11'); ?></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print i18n::translate('User name')?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_name" value="" /></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php print i18n::translate('Lost password request'); ?>" /></td></tr>
			</table>
			</form>
		</div>
		<script language="JavaScript" type="text/javascript">
			document.requestpwform.user_name.focus();
		</script>
		<?php
		break;

	case "requestpw" :
		$QUERY_STRING = "";
		print_header(i18n::translate('Lost password request'));
		print "<div class=\"center\">";
		$user_id=get_user_id($user_name);
		if (!$user_id) {
			AddToLog("New password requests for user ".$user_name." that does not exist", 'auth');
			print "<span class=\"warning\">";
			echo i18n::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
			print "</span><br />";
		} else {
			if (getUserEmail($user_id)=='') {
				AddToLog("Unable to send password to user ".$user_name." because they do not have an email address", 'auth');
				print "<span class=\"warning\">";
				echo i18n::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
				print "</span><br />";
			} else {
				$passchars = "abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$user_new_pw = "";
				$max = strlen($passchars)-1;
				for($i=0; $i<8; $i++) {
					$index = rand(0,$max);
					$user_new_pw .= $passchars{$index};
				}

				set_user_password($user_id, crypt($user_new_pw));
				set_user_setting($user_id, 'pwrequested', 1);

				// switch language to user settings
				i18n::init(get_user_setting($user_id, 'language'));
				$newuserName=getUserFullName($user_id);

				$mail_body = "";
				$mail_body .= i18n::translate('Hello %s ...', $newuserName) . "\r\n\r\n";
				$mail_body .= i18n::translate('A new password was requested for your user name.') . "\r\n\r\n";
				$mail_body .= i18n::translate('User name') . ": " . $user_name . "\r\n";

				$mail_body .= i18n::translate('Password') . ": " . $user_new_pw . "\r\n\r\n";
				$mail_body .= i18n::translate('Recommendation:') . "\r\n";
				$mail_body .= i18n::translate('Please click on the link below or paste it into your browser, login with the new password, and change it immediately to keep the integrity of your data secure.') . "\r\n\r\n";
				$mail_body .= i18n::translate('After you have logged in, select the «My Account» link under the «My Page» menu and fill in the password fields to change your password.') . "\r\n\r\n";

				if ($TEXT_DIRECTION=="rtl") $mail_body .= "<a href=\"".WT_SERVER_NAME.WT_SCRIPT_PATH."\">".WT_SERVER_NAME.WT_SCRIPT_PATH."</a>";
				else $mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH;

				require_once WT_ROOT.'includes/functions/functions_mail.php';
				pgvMail(getUserEmail($user_id), $WEBTREES_EMAIL, i18n::translate('Data request at %s', WT_SERVER_NAME.WT_SCRIPT_PATH), $mail_body);

				?>
				<table class="center facts_table">
				<tr><td class="wrap <?php print $TEXT_DIRECTION; ?>"><?php print i18n::translate('Hello...<br /><br />An email with your new password was sent to the address we have on file for <b>%s</b>.<br /><br />Please check your email account; you should receive our message soon.<br /><br />Recommendation:<br />You should login to this site with your new password as soon as possible, and you should change your password to maintain your data\'s security.', $user_name);?></td></tr>
				</table>
				<?php
				AddToLog("Password request was sent to user: ".$user_name, 'auth');

				i18n::init(WT_LOCALE);   // Reset language
			}
		}
		print "</div>";
		break;

	case "register" :
		$_SESSION["good_to_send"] = true;
		if (!$USE_REGISTRATION_MODULE) {
		header("Location: index.php");
		exit;
	}
	$message = "";
		if (!$user_name) {
			$message .= i18n::translate('You must enter a user name.')."<br />";
			$user_name_false = true;
		}
		else $user_name_false = false;

		if (!$user_password01) {
			$message .= i18n::translate('You must enter a password.')."<br />";
			$user_password01_false = true;
		}
		else $user_password01_false = false;

		if (!$user_password02) {
			$message .= i18n::translate('You must confirm the password.')."<br />";
			$user_password02_false = true;
		}
		else $user_password02_false = false;

		if ($user_password01 != $user_password02) {
			$message .= i18n::translate('Passwords do not match.')."<br />";
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
			print_header(i18n::translate('Request new user account'));
			// Empty user array in case any details might be left
			// and faulty users are requested and created
			$user = array();

			?>
			<script language="JavaScript" type="text/javascript">
			<!--
				function checkform(frm) {
					if (frm.user_name.value == "") {
						alert("<?php print i18n::translate('You must enter a user name.'); ?>");
						frm.user_name.focus();
						return false;
					}
					if (frm.user_password01.value == "") {
						alert("<?php print i18n::translate('You must enter a password.'); ?>");
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_password02.value == "") {
						alert("<?php print i18n::translate('You must confirm the password.'); ?>");
						frm.user_password02.focus();
						return false;
					}
					if (frm.user_password01.value != frm.user_password02.value) {
						alert("<?php print i18n::translate('Passwords do not match.'); ?>");
						frm.user_password01.value = "";
						frm.user_password02.value = "";
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_password01.value.length < 6) {
						alert("<?php print i18n::translate('Passwords must contain at least 6 characters.'); ?>");
						frm.user_password01.value = "";
						frm.user_password02.value = "";
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_realname.value == "") {
						alert("<?php print i18n::translate('You must enter your real name.'); ?>");
						frm.user_realname.focus();
						return false;
					}
					if ((frm.user_email.value == "")||(frm.user_email.value.indexOf('@')==-1)) {
						alert("<?php print i18n::translate('You must enter an email address.'); ?>");
						frm.user_email.focus();
						return false;
					}
					if (frm.user_comments.value == "") {
						alert("<?php print i18n::translate('Please enter your relationship to the data in the Comments field.'); ?>");
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
					echo i18n::translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living people listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div>');
					echo "<br />";
					echo "</td></tr></table></center>";
				}
			?>
			<div class="center">
				<form name="registerform" method="post" action="login_register.php" onsubmit="t = new Date(); document.registerform.time.value=t.toUTCString(); return checkform(this);">
					<input type="hidden" name="action" value="register" />
					<input type="hidden" name="time" value="" />
					<table class="center facts_table width50">
					<?php $i = 1;?>
						<tr><td class="topbottombar" colspan="2"><?php echo i18n::translate('Request new user account'), help_link('register_info_0'.$WELCOME_TEXT_AUTH_MODE); ?><br /><?php if (strlen($message) > 0) echo $message; ?></td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Real name'), help_link('new_user_realname'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" name="user_realname" value="<?php if (!$user_realname_false) echo $user_realname;?>" tabindex="<?php echo $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Email Address'), help_link('edituser_email'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" size="30" name="user_email" value="<?php if (!$user_email_false) echo $user_email;?>" tabindex="<?php echo $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Desired user name'), help_link('username'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="text" name="user_name" value="<?php if (!$user_name_false) echo $user_name;?>" tabindex="<?php echo $i;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Desired password'), help_link('edituser_password'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="password" name="user_password01" value="" tabindex="<?php echo $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Confirm Password'), help_link('edituser_conf_password'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>"><input type="password" name="user_password02" value="" tabindex="<?php echo $i++;?>" /> *</td></tr>
						<?php
						echo "<tr><td class=\"descriptionbox wrap ", $TEXT_DIRECTION, "\">";
						echo i18n::translate('Change Language'), help_link('edituser_change_lang');
						echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
						echo edit_field_language('user_language', WT_LOCALE, $extra='tabindex="'.(++$i).'"');
						echo '</td></tr>';
						if ($REQUIRE_AUTHENTICATION && $SHOW_LIVING_NAMES>=WT_PRIV_PUBLIC) { ?>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('GEDCOM INDI record ID'), help_link('register_gedcomid'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>" valign="top" ><input type="text" size="10" name="user_gedcomid" id="user_gedcomid" value="" tabindex="<?php echo $i++;?>" /><?php print_findindi_link("user_gedcomid",""); ?></td></tr>
						<?php } ?>
						<tr><td class="descriptionbox wrap <?php echo $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Comments'), help_link('register_comments'); ?></td><td class="optionbox <?php echo $TEXT_DIRECTION; ?>" valign="top" ><textarea cols="50" rows="5" name="user_comments" tabindex="<?php echo $i++;?>"><?php if (!$user_comments_false) echo $user_comments;?></textarea> *</td></tr>
						<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php echo i18n::translate('Request new user account'); ?>" tabindex="<?php echo $i++;?>" /></td></tr>
						<tr><td align="left" colspan="2" ><?php echo i18n::translate('Fields marked with * are mandatory.');?></td></tr>
					</table>
				</form>
			</div>
			<script language="JavaScript" type="text/javascript">
				document.registerform.user_name.focus();
			</script>
			<?php
			break;
		}

	case "registernew" :
		if (!$USE_REGISTRATION_MODULE) {
			header("Location: index.php");
			exit;
		}
		if ((stripos($user_name, "SUNTZU")!==false) || (stripos($user_email, "SUNTZU")!==false)) {
			AddToLog("SUNTZU hacker", 'auth');
			print "Go Away!";
			exit;
		}

			//-- check referer for possible spam attack
			if (!isset($_SERVER['HTTP_REFERER']) || stristr($_SERVER['HTTP_REFERER'],"login_register.php")===false) {
				print "<center><br /><span class=\"error\">Invalid page referer.</span>\n";
				print "<br /><br /></center>";
				AddToLog('Invalid page referer while trying to register a user.  Possible spam attack.', 'auth');
				exit;
			}

			if ((!isset($_SESSION["good_to_send"]))||($_SESSION["good_to_send"]!==true)) {
				AddToLog('Invalid session reference while trying to register a user.  Possible spam attack.', 'auth');
				exit;
			}
			$_SESSION["good_to_send"] = false;

		$QUERY_STRING = "";
		if (isset($user_name)) {
			print_header(i18n::translate('New Account confirmation'));
			print "<div class=\"center\">";
			$user_created_ok = false;

			AddToLog("User registration requested for: ".$user_name, 'auth');

			if (get_user_id($user_name)) {
				print "<span class=\"warning\">".i18n::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.')."</span><br /><br />";
				print "<a href=\"javascript:history.back()\">".i18n::translate('Back')."</a><br />";
			}
			else if ($user_password01 == $user_password02) {
				if ($user_id=create_user($user_name, $user_realname, $user_email, crypt($user_password01))) {
					set_user_setting($user_id, 'language',            $user_language);
					set_user_setting($user_id, 'verified',            'no');
					set_user_setting($user_id, 'verified_by_admin',    $REQUIRE_ADMIN_AUTH_REGISTRATION ? 'no' : 'yes');
					set_user_setting($user_id, 'reg_timestamp',        date('U'));
					set_user_setting($user_id, 'reg_hashcode',         md5(crypt($user_name)));
					set_user_setting($user_id, 'contactmethod',        "messaging2");
					set_user_setting($user_id, 'defaulttab',           $GEDCOM_DEFAULT_TAB);
					set_user_setting($user_id, 'visibleonline',        'Y');
					set_user_setting($user_id, 'editaccount',          'Y');
					set_user_setting($user_id, 'relationship_privacy', $USE_RELATIONSHIP_PRIVACY ? 'Y' : 'N');
					set_user_setting($user_id, 'max_relation_path',    $MAX_RELATION_PATH_LENGTH);
					set_user_setting($user_id, 'auto_accept',          'N');
					set_user_setting($user_id, 'canadmin',             'N');
					set_user_setting($user_id, 'loggedin',             'N');
					set_user_setting($user_id, 'sessiontime',          '0');
					if (!empty($user_gedcomid)) {
						set_user_gedcom_setting($user_id, $GEDCOM, 'gedcomid', $user_gedcomid);
						set_user_gedcom_setting($user_id, $GEDCOM, 'rootid',   $user_gedcomid);
					}
					$user_created_ok = true;
				} else {
					print "<span class=\"warning\">".i18n::translate('Unable to add user.  Please try again.')."<br /></span>";
					print "<a href=\"javascript:history.back()\">".i18n::translate('Back')."</a><br />";
				}
			} else {
				print "<span class=\"warning\">".i18n::translate('Passwords do not match.')."</span><br />";
				print "<a href=\"javascript:history.back()\">".i18n::translate('Back')."</a><br />";
			}
			if ($user_created_ok) {
				// switch to the user's language
				i18n::init($user_language);
				$mail_body = "";
				$mail_body .= i18n::translate('Hello %s ...', $user_realname) . "\r\n\r\n";
				$mail_body .= i18n::translate('A request was received at %s to create a webtrees account with your email address %s.', WT_SERVER_NAME.WT_SCRIPT_PATH, $user_email) . "  ";
				$mail_body .= i18n::translate('Information about the request is shown under the link below.') . "\r\n\r\n";
				$mail_body .= i18n::translate('Please click on the following link and fill in the requested data to confirm your request and email address.') . "\r\n\r\n";
				if ($TEXT_DIRECTION=="rtl") {
					$mail_body .= "<a href=\"";
					$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH . "login_register.php?user_name=".urlencode($user_name)."&user_hashcode=".urlencode(get_user_setting($user_id, 'reg_hashcode'))."&action=userverify\">";
				}
				$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH . "login_register.php?user_name=".urlencode($user_name)."&user_hashcode=".urlencode(get_user_setting($user_id, 'reg_hashcode'))."&action=userverify";
				if ($TEXT_DIRECTION=="rtl") $mail_body .= "</a>";
				$mail_body .= "\r\n";
				$mail_body .= i18n::translate('User name') . " " . $user_name . "\r\n";
				$mail_body .= i18n::translate('Verification code:') . " " . get_user_setting($user_id, 'reg_hashcode') . "\r\n\r\n";
				$mail_body .= i18n::translate('Comments').": " . $user_comments . "\r\n\r\n";
				$mail_body .= i18n::translate('If you didn\'t request an account, you can just delete this message.') . "  ";
				$mail_body .= i18n::translate('You won\'t get any more email from this site, because the account request will be deleted automatically after seven days.') . "\r\n";
				require_once WT_ROOT.'includes/functions/functions_mail.php';
				pgvMail($user_email, $WEBTREES_EMAIL, i18n::translate('Your registration at %s', WT_SERVER_NAME.WT_SCRIPT_PATH), $mail_body);

				// switch language to webmaster settings
				i18n::init(get_user_setting($WEBMASTER_EMAIL, 'language'));

				$mail_body = "";
				$mail_body .= i18n::translate('Hello Administrator ...') . "\r\n\r\n";
				$mail_body .= i18n::translate('A prospective user registered himself with webtrees at %s.', WT_SERVER_NAME.WT_SCRIPT_PATH) . "\r\n\r\n";
				$mail_body .= i18n::translate('User name') . " " . $user_name . "\r\n";
				$mail_body .= i18n::translate('Real name') . " " . $user_realname . "\r\n\r\n";
				$mail_body .= i18n::translate('Comments').": " . $user_comments . "\r\n\r\n";
				$mail_body .= i18n::translate('The user received an email with the information necessary to confirm his access request.') . "\r\n\r\n";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) $mail_body .= i18n::translate('You will be informed by email when this prospective user has confirmed his request.  You can then complete the process by activating the user name.  The new user will not be able to login until you activate the account.') . "\r\n";
				else $mail_body .= i18n::translate('You will be informed by email when this prospective user has confirmed his request.  After this, the user will be able to login without any action on your part.') . "\r\n";

				$message = array();
				$message["to"]=$WEBMASTER_EMAIL;
				$message["from"]=$user_email;
				$message["subject"] = i18n::translate('New registration at %s', WT_SERVER_NAME.WT_SCRIPT_PATH);
				$message["body"] = $mail_body;
				$message["created"] = $time;
				$message["method"] = $SUPPORT_METHOD;
				$message["no_from"] = true;
				addMessage($message);

				// switch language to user's settings
				i18n::init($user_language);
				?>
				<table class="center facts_table">
					<tr><td class="wrap <?php print $TEXT_DIRECTION; ?>"><?php print i18n::translate('Hello %s ...<br />Thank you for your registration.', $user_realname); ?><br /><br />
					<?php
					if ($REQUIRE_ADMIN_AUTH_REGISTRATION) print i18n::translate('We will now send a confirmation email to the address <b>%s</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br /><br />After you have followed the instructions in the confirmation email, the administrator still has to approve your request before your account can be used.<br /><br />To login to this site, you will need to know your user name and password.', $user_email);
					else print i18n::translate('We will now send a confirmation email to the address <b>%s</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br /><br />After you have followed the instructions in the confirmation email, you can login.  To login to this site, you will need to know your user name and password.', $user_email);
					?>
					</td></tr>
				</table>
				<?php
				i18n::init(WT_LOCALE); // Reset language
			}
			print "</div>";
		} else {
			header("Location: login.php");
			exit;
		}
		break;

	case "userverify" :
		if (!$USE_REGISTRATION_MODULE) {
			header("Location: index.php");
			exit;
		}

		// Change to the new user's language
		$user_id=get_user_id($user_name);
		i18n::init(get_user_setting($user_id, 'language'));

		print_header(i18n::translate('User verification'));
		print "<div class=\"center\">";
		?>
		<form name="verifyform" method="post" action="" onsubmit="t = new Date(); document.verifyform.time.value=t.toUTCString();">
			<input type="hidden" name="action" value="verify_hash" />
			<input type="hidden" name="time" value="" />
			<table class="center facts_table width25">
				<tr><td class="topbottombar" colspan="2"><?php echo i18n::translate('User verification'), help_link('pls_note07'); ?></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php echo i18n::translate('User name'); ?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_name" value="<?php print $user_name; ?>" /></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Password'); ?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="password" name="user_password" value="" /></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php echo i18n::translate('Verification code:'); ?></td><td class="facts_value <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_hashcode" value="<?php print $user_hashcode; ?>" /></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php echo i18n::translate('Send'); ?>" /></td></tr>
			</table>
		</form>
		</div>
		<script language="JavaScript" type="text/javascript">
			document.verifyform.user_name.focus();
		</script>
		<?php
		break;

	case "verify_hash" :
		if (!$USE_REGISTRATION_MODULE) {
			header("Location: index.php");
			exit;
		}
		$QUERY_STRING = "";
		AddToLog("User attempted to verify hashcode: ".$user_name, 'auth');

		// Change to the new user's language
		$user_id=get_user_id($user_name);
		i18n::init(get_user_setting($user_id, 'language'));

		print_header(i18n::translate('User verification')); // <-- better verification of authentication code
		print "<div class=\"center\">";
		print "<table class=\"center facts_table wrap ".$TEXT_DIRECTION."\">";
		print "<tr><td class=\"topbottombar\">".i18n::translate('User verification')."</td></tr>";
		print "<tr><td class=\"optionbox\">";
		print i18n::translate('The data for the user <b>%s</b> was checked.', $user_name);
		if ($user_id) {
			$pw_ok = (get_user_password($user_id) == crypt($user_password, get_user_password($user_id)));
			$hc_ok = (get_user_setting($user_id, 'reg_hashcode') == $user_hashcode);
			if (($pw_ok) && ($hc_ok)) {
				set_user_setting($user_id, 'verified', 'yes');
				set_user_setting($user_id, 'pwrequested', '');
				set_user_setting($user_id, 'reg_timestamp', date("U"));
				set_user_setting($user_id, 'reg_hashcode', '');
				if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) {
					set_user_setting($user_id, 'verified_by_admin', 'yes');
				}
				AddToLog("User verified: ".$user_name, 'auth');

				// switch language to webmaster settings
				i18n::init(get_user_setting($WEBMASTER_EMAIL, 'language'));

				$mail_body = "";
				$mail_body .= i18n::translate('Hello Administrator ...') . "\r\n\r\n";
				$mail_body .= i18n::translate('User %s (%s) has confirmed his request for an account.', $user_name, getUserFullName($user_id)) . "\r\n\r\n";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) $mail_body .= i18n::translate('Please click on the link below to login to your site.  You must Edit the user to activate the account so that he can login to your site.') . "\r\n";
				else $mail_body .= i18n::translate('You do not have to take any action; the user can now login.') . "\r\n";

				if ($TEXT_DIRECTION=="rtl") {
					$mail_body .= "<a href=\"";
					$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH."useradmin.php?action=edituser&username=" . urlencode($user_name) . "\">";
				}
				$mail_body .= WT_SERVER_NAME.WT_SCRIPT_PATH."useradmin.php?action=edituser&username=" . urlencode($user_name);
				if ($TEXT_DIRECTION=="rtl") $mail_body .= "</a>";
				$mail_body .= "\r\n";

				$message = array();
				$message["to"]=$WEBMASTER_EMAIL;
				$message["from"]=$WEBTREES_EMAIL;
				$message["subject"] = i18n::translate('New user at %s', WT_SERVER_NAME.WT_SCRIPT_PATH);
				$message["body"] = $mail_body;
				$message["created"] = $time;
				$message["method"] = $SUPPORT_METHOD;
				$message["no_from"] = true;
				addMessage($message);

				i18n::init(WT_LOCALE); // Reset language

				print "<br /><br />".i18n::translate('You have confirmed your request to become a registered user.')."<br /><br />";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) print i18n::translate('The Administrator has been informed.  As soon as he gives you permission to login, you can login with your user name and password.');
				else print i18n::translate('You can now login with your user name and password.');
				print "<br /><br /></td></tr>";
			} else {
				print "<br /><br />";
				print "<span class=\"warning\">";
				print i18n::translate('Data was not correct, please try again');
				print "</span><br /><br /></td></tr>";
			}
		} else {
			print "<br /><br />";
			print "<span class=\"warning\">";
			print i18n::translate('Could not verify the information you entered.  Please try again or contact the site administrator for more information.');
			print "</span><br /><br /></td></tr>";
		}
		print "</table>";
		print "</div>";
		break;

	default :
		header("Location: ".encode_url($url));
		break;
}

print_footer();
?>
