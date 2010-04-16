<?php
/**
 * Send a message to a user in the system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'message.php');
require './includes/session.php';

print_simple_header(i18n::translate('webtrees Message'));

$subject   =isset($_REQUEST['subject'   ]) ? $_REQUEST['subject'   ] : '';
$url       =isset($_REQUEST['url'       ]) ? $_REQUEST['url'       ] : '';
$method    =isset($_REQUEST['method'    ]) ? $_REQUEST['method'    ] : 'messaging2';
$body      =isset($_REQUEST['body'      ]) ? $_REQUEST['body'      ] : '';
$from_email=isset($_REQUEST['from_email']) ? $_REQUEST['from_email'] : '';
$from_name =isset($_REQUEST['from_name' ]) ? $_REQUEST['from_name' ] : '';
$to        =isset($_REQUEST['to'        ]) ? $_REQUEST['to'        ] : '';
$action    =isset($_REQUEST['action'    ]) ? $_REQUEST['action'    ] : 'compose';
$from      =isset($_REQUEST['from'      ]) ? $_REQUEST['from'      ] : '';
$time      =isset($_REQUEST['time'      ]) ? $_REQUEST['time'      ] : '';
$method    =isset($_REQUEST['method'    ]) ? $_REQUEST['method'    ] : '';

if (empty($to)) {
	print "<span class=\"error\">".i18n::translate('No recipient user was provided.  Cannot continue.')."</span><br />";
	print_simple_footer();
	exit;
}
if ($to=="all" && !WT_USER_IS_ADMIN) {
	print "<span class=\"error\">".i18n::translate('No recipient user was provided.  Cannot continue.')."</span><br />";
	print_simple_footer();
	exit;
}

if (($action=="send")&&(isset($_SESSION["good_to_send"]))&&($_SESSION["good_to_send"]===true)) {
	$_SESSION["good_to_send"] = false;
	if (!empty($from_email)) $from = $from_email;
	if (!get_user_id($from)) {
		$mt = preg_match("/(.+)@(.+)/", $from, $match);
		if ($mt>0) {
			$host = trim($match[2]);
			if (function_exists("checkdnsrr")) {
				$ip = checkdnsrr($host);
				if ($ip === false) {
					$host = "www.".$host;
					$ip = checkdnsrr($host);
					if ($ip === false) {
						print "<center><br /><span class=\"error\">".i18n::translate('Please enter a valid email address.')."</span>\n";
						print "<br /><br /></center>";
						$action="compose";
						//print_simple_footer();
						//exit;
					}
				}
			}
		} else {
			print "<center><br /><span class=\"error\">".i18n::translate('Please enter a valid email address.')."</span>\n";
			print "<br /><br /></center>";
			$action="compose";
		}
	}
	//-- check referer for possible spam attack
	if (!isset($_SERVER['HTTP_REFERER']) || stristr($_SERVER['HTTP_REFERER'],"message.php")===false) {
		print "<center><br /><span class=\"error\">Invalid page referer.</span>\n";
		print "<br /><br /></center>";
		AddToLog('Invalid page referer while trying to send a message.  Possible spam attack.', 'auth');
		$action="compose";
	}
	if ($action!="compose") {
		$toarray = array($to);
		if ($to == "all") {
			$toarray = get_all_users();
		}
		if ($to == "never_logged") {
			$toarray = array();
			foreach (get_all_users() as $user_id=>$user_name) {
				// SEE Bug [ 1827547 ] Message to inactive users sent to newcomers
				if (get_user_setting($user_id,'verified_by_admin')=="yes" && get_user_setting($user_id, 'reg_timestamp') > get_user_setting($user_id, 'sessiontime')) {
					$toarray[$user_id] = $user_name;
				}
			}
		}
		if ($to == "last_6mo") {
			$toarray = array();
			$sixmos = 60*60*24*30*6;	//-- timestamp for six months
			foreach (get_all_users() as $user_id=>$user_name) {
				// SEE Bug [ 1827547 ] Message to inactive users sent to newcomers
				if (get_user_setting($user_id,'sessiontime')>0 && (time() - get_user_setting($user_id, 'sessiontime') > $sixmos)) {
					$toarray[$user_id] = $user_name;
				}
				//-- not verified by registration past 6 months
				else if (get_user_setting($user_id, 'verified_by_admin')!="yes" && (time() - get_user_setting($user_id, 'reg_timestamp') > $sixmos)) {
					$toarray[$user_id] = $user_name;
				}
			}
		}
		$i = 0;
		foreach($toarray as $indexval => $to) {
			$message = array();
			$message["to"]=$to;
			$message["from"]=$from;
			if (!empty($from_name)) {
				$message["from_name"] = $from_name;
				$message["from_email"] = $from_email;
			}
			$message["subject"] = $subject;
			$url = preg_replace("/".session_name()."=.*/", "", $url);
			$message["body"] = $body;
			$message["created"] = $time;
			$message["method"] = $method;
			$message["url"] = $url.'&amp;ged='.$GEDCOM;
			if ($i>0) $message["no_from"] = true;
			if (addMessage($message)){
				$to_user_id=get_user_id($to);
				if ($to_user_id) {
					print i18n::translate('Message successfully sent to %s', "<b>".getUserFullName($to_user_id)."</b>");
					print "<br />";
				} else {
					AddToLog('Invalid TO user.'.$to.' Possible spam attack.', 'auth');
				}
			} else {
				AddToLog('Unable to send message.  TO:'.$to.' FROM:'.$from, 'error');
			}
			$i++;
		}
	}
}
else if ($action=="send") AddToLog('Invalid Compose Session while trying to send a message.  Possible spam attack.', 'auth');

if ($action=="compose") {
	print '<span class="subheaders">'.i18n::translate('Send Message').'</span>';
	$_SESSION["good_to_send"] = true;
	?>
	<script language="JavaScript" type="text/javascript">
		function validateEmail(email) {
			if (email.value.search("(.*)@(.*)")==-1) {
				alert('<?php print i18n::translate('Please enter a valid email address.'); ?>');
				email.focus();
				return false;
			}
			return checkForm(document.messageform);
		}
		function checkForm(frm) {
			if (frm.subject.value=="") {
				alert('<?php print i18n::translate('Please enter a message subject.'); ?>');
				document.messageform.subject.focus();
				return false;
			}
			if (frm.body.value=="") {
				alert('<?php print i18n::translate('Please enter some message text before sending.'); ?>');
				document.messageform.body.focus();
				return false;
			}
			return true;
		}
	</script>
	<?php
	if (!WT_USER_ID) {
		print "<br /><br />".i18n::translate('<b>Please Note:</b> Private information of living individuals will only be given to family relatives and close friends.  You will be asked to verify your relationship before you will receive any private data.  Sometimes information of dead persons may also be private.  If this is the case, it is because there is not enough information known about the person to determine whether they are alive or not and we probably do not have more information on this person.<br /><br />Before asking a question, please verify that you are inquiring about the correct person by checking dates, places, and close relatives.  If you are submitting changes to the genealogical data, please include the sources where you obtained the data.');
	}
	print "<br /><form name=\"messageform\" method=\"post\" action=\"message.php\" onsubmit=\"t = new Date(); document.messageform.time.value=t.toUTCString(); ";
	if (!WT_USER_ID) print "return validateEmail(document.messageform.from_email);";
	else print "return checkForm(this);";
	print "\">\n";
	print "<table>\n";
	$to_user_id=get_user_id($to);
	if ($to_user_id) {
		echo "<tr><td></td><td>".i18n::translate('This message will be sent to %s', "<b>".getUserFullName($to_user_id)."</b>")."<br />";
		echo i18n::translate('This user prefers to receive messages in %s', Zend_Locale::getTranslation(get_user_setting($to_user_id, 'language'), 'language', WT_LOCALE))."</td></tr>\n";
	}

	if (!WT_USER_ID){
		print "<tr><td valign=\"top\" width=\"15%\" align=\"right\">".i18n::translate('Your Name:')."</td>";
		print "<td><input type=\"text\" name=\"from_name\" size=\"40\" value=\"$from_name\" /></td></tr><tr><td valign=\"top\" align=\"right\">".i18n::translate('Email Address:')."</td><td><input type=\"text\" name=\"from_email\" size=\"40\" value=\"$from_email\" /><br />".i18n::translate('Please provide your email address so that we may contact you in response to this message.  If you do not provide your email address we will not be able to respond to your inquiry.  Your email address will not be used in any other way besides responding to this inquiry.')."<br /><br /></td></tr>\n";
	}
	print "<tr><td align=\"right\">".i18n::translate('Subject:')."</td>";
	print "<td>";
	if (WT_USER_ID){
		print "<input type=\"hidden\" name=\"from\" value=\"".WT_USER_NAME."\" />\n";
	}
	print "<input type=\"hidden\" name=\"action\" value=\"send\" />\n";
	print "<input type=\"hidden\" name=\"to\" value=\"$to\" />\n";
	print "<input type=\"hidden\" name=\"time\" value=\"\" />\n";
	print "<input type=\"hidden\" name=\"method\" value=\"$method\" />\n";
	print "<input type=\"hidden\" name=\"url\" value=\"$url\" />\n";
	print "<input type=\"text\" name=\"subject\" size=\"50\" value=\"$subject\" /><br /></td></tr>\n";
	print "<tr><td valign=\"top\" align=\"right\">".i18n::translate('Body:')."<br /></td><td><textarea name=\"body\" cols=\"50\" rows=\"7\">$body</textarea><br /></td></tr>\n";
	print "<tr><td></td><td><input type=\"submit\" value=\"".i18n::translate('Send')."\" /></td></tr>\n";
	print "</table>\n";
	print "</form>\n";
	if ($method=="messaging2") print i18n::translate('When you send this message you will receive a copy sent via email to the address you provided.');
}
else if ($action=="delete") {
	if (deleteMessage($id)) print i18n::translate('Message Deleted');
}
print "<center><br /><br /><a href=\"javascript:;\" onclick=\"if (window.opener.refreshpage) window.opener.refreshpage(); window.close();\">".i18n::translate('Close Window')."</a><br /></center>";

print_simple_footer();
?>
