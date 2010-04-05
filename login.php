<?php
/**
 * Login Page.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Display
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'login.php');
require './includes/session.php';

// Extract query parameters
$url         =safe_POST('url',      WT_REGEX_URL);
$type        =safe_POST('type',     array('full', 'simple'));
$action      =safe_POST('action');
$username    =safe_POST('username', WT_REGEX_USERNAME);
$password    =safe_POST('password', WT_REGEX_PASSWORD);
$usertime    =safe_POST('usertime');
$pid         =safe_POST('pid',      WT_REGEX_XREF);
$ged         =safe_POST('ged',      get_all_gedcoms(), $GEDCOM);
$help_message=safe_GET('help_message');

// Some variables can come from the URL as well as the form
if (!$url)    $url   =safe_GET('url',  WT_REGEX_URL);
if (!$type)   $type  =safe_GET('type', array('full', 'simple'), 'full');
if (!$action) $action=safe_GET('action');

if (empty($url)) {
	// If we came here by means of a URL like http://mysite.com/foo/login.php
	// we don't have a proper login URL, and session cookies haven't been set yet
	// We'll re-load the page to properly determine cookie support
	header("Location: login.php?url=index.php?ctype=user");
}

$message='';

if ($action=='login') {
	if ($user_id=authenticateUser($username, $password)) {
		if ($usertime) {
			$_SESSION['usertime']=@strtotime($usertime);
		} else {
			$_SESSION['usertime']=time();
		}
		$_SESSION['timediff']=time()-$_SESSION['usertime'];
		$_SESSION['locale']=get_user_setting($user_id, 'language');

		// If we have no access rights to the current gedcom, switch to one where we do
		if (!userIsAdmin($user_id)) {
			if (!userCanAccess($user_id, WT_GED_ID)) {
				foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
					if (userCanAccess($user_id, $ged_id)) {
						$ged=$ged_name;
						$url='index.php';
						break;
					}
				}
			}
		}

		//-- section added based on UI feedback
		// $url is set to individual.php below if a URL is not passed in... it will then be resent as "individual.php" when the user attempts to login
		if ($url=='individual.php') {
			foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
				if (get_user_gedcom_setting($user_id, $ged_id, 'gedcomid')) {
					$pid = get_user_gedcom_setting($user_id, $ged_id, 'gedcomid');
					$ged = $ged_name;
					break;
				}
			}
			if ($pid) {
				$url = "individual.php?pid=".$pid;
			} else {
				//-- user does not have a pid?  Go to My Page
				$url = "index.php?ctype=user";
			}
		}

		session_write_close();

		// If we've clicked login from the login page, we don't want to go back there.
		if (substr($url, 0, 9)=='login.php') {
			$url='index.php';
		}

		$url = str_replace("logout=1", "", $url);
		$url .= "&";	// Simplify the preg_replace following
		$url = preg_replace('/(&|\?)ged=.*&/', "$1", html_entity_decode(rawurldecode($url),ENT_COMPAT,'UTF-8'));	// Remove any existing &ged= parameter
		if (substr($url, -1)=="&") $url = substr($url, 0, -1);
		$url .= "&ged=".$ged;
		$url = str_replace(array("&&", ".php&", ".php?&"), array("&", ".php?", ".php?"), $url);

		header("Location: ".encode_url($url, false));
		exit;
	} else {
		$message = i18n::translate('Unable to authenticate user.');
	}
} else {
	$tSERVER_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $SERVER_URL);
	$tLOGIN_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $LOGIN_URL);
	if (empty($url)) {
		if ((isset($_SERVER['HTTP_REFERER'])) && ((stristr($_SERVER['HTTP_REFERER'],$tSERVER_URL)!==false)||(stristr($_SERVER['HTTP_REFERER'],$tLOGIN_URL)!==false))) {
			$url = basename($_SERVER['HTTP_REFERER']);
			if (stristr($url, ".php")===false) {
				$url = "index.php?ctype=gedcom&ged=$GEDCOM";
			}
		}
		else {
			if (isset($url)) {
				if (stristr($url,$SERVER_URL)!==false) $url = $SERVER_URL;
			}
			//else $url = $SERVER_URL;
			/* - commented out based on UI feedback
			else $url = "index.php?ctype=user";
			*/
			else $url = "individual.php";
		}
	}
	else if (stristr($url, "index.php")&&!stristr($url, "ctype=")) {
		$url.="&ctype=gedcom";
	}
}

if ($type=="full") {
	print_header(i18n::translate('webtrees user login'));
} else {
	print_simple_header(i18n::translate('webtrees user login'));
}
echo "<div class=\"center\">\n";

echo "<table class=\"center width60 ltr\"><tr><td>";
switch ($WELCOME_TEXT_AUTH_MODE){
case 1:
	echo i18n::translate('<center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to every visitor who has a user account.<br /><br />If you have a user account, you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your application, the site administrator will activate your account.  You will receive an email when your application has been approved.');
	break;
case 2:
	i18n::translate('<center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>authorized</u> users only.<br /><br />If you have a user account you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.');
	break;
case 3:
	i18n::translate('<center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>family members only</u>.<br /><br />If you have a user account you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.');
	break;
case 4:
	echo i18n::translate('<center><b>Welcome to this Genealogy website</b></center><br />Access is permitted to users who have an account and a password for this website.');
	if ($WELCOME_TEXT_CUST_HEAD) {
		echo embed_globals($WELCOME_TEXT_AUTH_MODE_4);
	}
	break;
}
echo '</td></tr></table><br /><br />';
$tab=0;		// initialize tab index
	?>
	<form name="loginform" method="post" action="<?php print $LOGIN_URL; ?>" onsubmit="t = new Date(); document.loginform.usertime.value=t.getFullYear()+'-'+(t.getMonth()+1)+'-'+t.getDate()+' '+t.getHours()+':'+t.getMinutes()+':'+t.getSeconds(); return true;">
		<input type="hidden" name="action" value="login" />
		<input type="hidden" name="url" value="<?php print htmlentities($url,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="ged" value="<?php if (isset($ged)) print htmlentities($ged,ENT_COMPAT,'UTF-8'); else print htmlentities($GEDCOM,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="pid" value="<?php if (isset($pid)) print htmlentities($pid,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="type" value="<?php print htmlentities($type,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="usertime" value="" />
		<?php
		if (!empty($message)) print "<span class='error'><br /><b>$message</b><br /><br /></span>\r\n";
		?>
		<!--table-->
		<table class="center facts_table width50">
			<tr><td class="topbottombar" colspan="2"><?php print i18n::translate('Login'); ?></td></tr>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php echo i18n::translate('User name'), help_link('username'); ?></td>
				<td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" tabindex="<?php echo ++$tab; ?>" name="username" value="<?php print htmlentities($username,ENT_COMPAT,'UTF-8'); ?>" size="20" class="formField" /></td>
			</tr>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php echo i18n::translate('Password'), help_link('password'); ?></td>
				<td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="password" tabindex="<?php echo ++$tab; ?>" name="password" size="20" class="formField" /></td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="2">
					<input type="submit" tabindex="<?php echo ++$tab; ?>" value="<?php print i18n::translate('Login'); ?>" />
					<?php
					if ($SHOW_CONTEXT_HELP) {
						if ($REQUIRE_AUTHENTICATION) {
							echo help_link('login_buttons_aut');
						} else {
							echo help_link('login_buttons');
						}
					}
					?>
				</td>
			</tr>
		</table>
</form><br /><br />
<?php

$sessname = session_name();
if (!isset($_COOKIE[$sessname])) print "<center><div class=\"error width50\">".i18n::translate('This site uses cookies to keep track of your login status.<br /><br />Cookies do not appear to be enabled in your browser. You must enable cookies for this site before you can login.  You can consult your browser\'s help documentation for information on enabling cookies.')."</div></center><br /><br />";

if ($USE_REGISTRATION_MODULE) { ?>
	<table class="center facts_table width50">
	<tr><td class="topbottombar" colspan="2"><?php print i18n::translate('Account Information'); ?></td></tr>
	<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php echo i18n::translate('No account?'), help_link('new_user'); ?></td>
	<td class="optionbox <?php print $TEXT_DIRECTION; ?> wrap"><a href="login_register.php?action=register"><?php echo i18n::translate('Request new user account'); ?></a></td></tr>
	<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php echo i18n::translate('Lost your password?'), help_link('new_password'); ?></td>
	<td class="optionbox <?php print $TEXT_DIRECTION; ?> wrap"><a href="login_register.php?action=pwlost"><?php print i18n::translate('Request new password'); ?></a></td></tr>
	<tr><td class="topbottombar ltr" colspan="2">&nbsp;</td></tr>
	</table>
<?php
}
print "</div><br /><br />";
?>
<script language="JavaScript" type="text/javascript">
	document.loginform.username.focus();
</script>
<?php
if ($type=="full") print_footer();
else print_simple_footer();
?>
