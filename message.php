<?php
// Send a message to a user in the system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2007  John Finlay and Others
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
use WT\Log;
use WT\User;

define('WT_SCRIPT_NAME', 'message.php');
require './includes/session.php';

// Some variables are initialised from GET (so we can set initial values in URLs),
// but are submitted in POST so we can have long body text.

$subject    = WT_Filter::post('subject', null, WT_Filter::get('subject'));
$body       = WT_Filter::post('body');
$from_name  = WT_Filter::post('from_name');
$from_email = WT_Filter::post('from_email');
$action     = WT_Filter::post('action', 'compose|send', 'compose');
$to         = WT_Filter::post('to', null, WT_Filter::get('to'));
$method     = WT_Filter::post('method', 'messaging|messaging2|messaging3|mailto|none', WT_Filter::get('method', 'messaging|messaging2|messaging3|mailto|none', 'messaging2'));
$url        = WT_Filter::postUrl('url', WT_Filter::getUrl('url'));

$to_user = User::findByIdentifier($to);

$controller = new WT_Controller_Simple();
$controller
	->restrictAccess($to_user || Auth::isAdmin() && ($to === 'all' || $to === 'last_6mo' || $to === 'never_logged'))
	->setPageTitle(WT_I18N::translate('webtrees message'));

$errors = '';

// Is this message from a member or a visitor?
if (Auth::check()) {
	$from = Auth::user()->getUserName();
} else {
	// Visitors must provide a valid email address
	if ($from_email && (!preg_match("/(.+)@(.+)/", $from_email, $match) || function_exists('checkdnsrr') && checkdnsrr($match[2]) === false)) {
		$errors .= '<p class="ui-state-error">' . WT_I18N::translate('Please enter a valid email address.') . '</p>';
		$action = 'compose';
	}

	// Do not allow anonymous visitors to include links to external sites
	if (preg_match('/(?!' . preg_quote(WT_SERVER_NAME, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $subject . $body, $match)) {
		$errors .=
			'<p class="ui-state-error">' . WT_I18N::translate('You are not allowed to send messages that contain external links.') . '</p>' .
			'<p class="ui-state-highlight">' . /* I18N: e.g. ‘You should delete the “http://” from “http://www.example.com” and try again.’ */ WT_I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1]) . '</p>' .
			Log::addAuthenticationLog('Possible spam message from "' . $from_name . '"/"' . $from_email . '", subject="' . $subject . '", body="' . $body . '"');
		$action = 'compose';
	}
	$from = $from_email;
}

// Ensure the user always visits this page twice - once to compose it and again to send it.
// This makes it harder for spammers.
switch ($action) {
case 'compose':
	$WT_SESSION->good_to_send = true;
	break;
case 'send':
	// Only send messages if we've come straight from the compose page.
	if (!$WT_SESSION->good_to_send) {
		Log::addAuthenticationLog('Attempt to send a message without visiting the compose page.  Spam attack?');
		$action = 'compose';
	}
	if (!WT_Filter::checkCsrf()) {
		$action = 'compose';
	}
	unset($WT_SESSION->good_to_send);
	break;
}

switch ($action) {
case 'compose':
	$controller
		->pageHeader()
		->addInlineJavascript('
		function checkForm(frm) {
			if (frm.subject.value === "") {
				alert("' . WT_I18N::translate('Please enter a message subject.') . '");
				document.messageform.subject.focus();
				return false;
			}
			if (frm.body.value === "") {
				alert("' . WT_I18N::translate('Please enter some message text before sending.') . '");
				document.messageform.body.focus();
				return false;
			}
			return true;
		}
	');
	echo '<span class="subheaders">', WT_I18N::translate('Send a message'), '</span>';
	echo $errors;

	if (!Auth::check()) {
		echo '<br><br>', WT_I18N::translate('<b>Please note:</b> Private information of living individuals will only be given to family relatives and close friends.  You will be asked to verify your relationship before you will receive any private data.  Sometimes information of dead individuals may also be private.  If this is the case, it is because there is not enough information known about the individual to determine whether they are alive or not and we probably do not have more information on this individual.<br><br>Before asking a question, please verify that you are inquiring about the correct individual by checking dates, places, and close relatives.  If you are submitting changes to the genealogical data, please include the sources where you obtained the data.');
	}
	echo '<br><form name="messageform" method="post" action="message.php" onsubmit="t = new Date(); document.messageform.time.value=t.toUTCString(); return checkForm(this);">';
	echo WT_Filter::getCsrf();
	echo '<table>';
	if ($to !== 'all' && $to !== 'last_6mo' && $to !== 'never_logged') {
		echo '<tr><td></td><td>', WT_I18N::translate('This message will be sent to %s', '<b>' . WT_Filter::escapeHtml($to_user->getRealName()) . '</b>'), '</td></tr>';
	}
	if (!Auth::check()) {
		echo '<tr><td valign="top" width="15%" align="right">', WT_I18N::translate('Your name:'), '</td>';
		echo '<td><input type="text" name="from_name" size="40" value="', WT_Filter::escapeHtml($from_name), '"></td></tr><tr><td valign="top" align="right">', WT_I18N::translate('Email address:'), '</td><td><input type="email" name="from_email" size="40" value="', WT_Filter::escapeHtml($from_email), '"><br>', WT_I18N::translate('Please provide your email address so that we may contact you in response to this message.  If you do not provide your email address we will not be able to respond to your inquiry.  Your email address will not be used in any other way besides responding to this inquiry.'), '<br><br></td></tr>';
	}
	echo '<tr><td align="right">', WT_I18N::translate('Subject:'), '</td>';
	echo '<td>';
	echo '<input type="hidden" name="action" value="send">';
	echo '<input type="hidden" name="to" value="', WT_Filter::escapeHtml($to), '">';
	echo '<input type="hidden" name="time" value="">';
	echo '<input type="hidden" name="method" value="', $method, '">';
	echo '<input type="hidden" name="url" value="', WT_Filter::escapeHtml($url), '">';
	echo '<input type="text" name="subject" size="50" value="', WT_Filter::escapeHtml($subject), '"><br></td></tr>';
	echo '<tr><td valign="top" align="right">', WT_I18N::translate('Body:'), '<br></td><td><textarea name="body" cols="50" rows="7">', WT_Filter::escapeHtml($body), '</textarea><br></td></tr>';
	echo '<tr><td></td><td><input type="submit" value="', WT_I18N::translate('Send'), '"></td></tr>';
	echo '</table>';
	echo '</form>';
	if ($method === 'messaging2') {
		echo WT_I18N::translate('When you send this message you will receive a copy sent via email to the address you provided.');
	}
	echo
		'<br><br><br><br>',  // TODO use margin-bottom instead of this
		'<p id="save-cancel">',
		'<input type="button" class="cancel" value="', WT_I18N::translate('close'), '" onclick="window.close();">',
		'</p>';
	break;

case 'send':
	if ($from_email) {
		$from = $from_email;
	}

	$toarray = array($to);
	if ($to === 'all') {
		$toarray = array();
		foreach (User::all() as $user) {
			$toarray[$user->getUserId()] = $user->getUserName();
		}
	}
	if ($to === 'never_logged') {
		$toarray = array();
		foreach (User::all() as $user) {
			if ($user->getPreference('verified_by_admin') && $user->getPreference('reg_timestamp') > $user->getPreference('sessiontime')) {
				$toarray[$user->getUserId()] = $user->getUserName();
			}
		}
	}
	if ($to === 'last_6mo') {
		$toarray = array();
		$sixmos  = 60 * 60 * 24 * 30 * 6; //-- timestamp for six months
		foreach (User::all() as $user) {
			if ($user->getPreference('sessiontime') > 0 && (WT_TIMESTAMP - $user->getPreference('sessiontime') > $sixmos)) {
				$toarray[$user->getUserId()] = $user->getUserName();
			} elseif (!$user->getPreference('verified_by_admin') && (WT_TIMESTAMP - $user->getPreference('reg_timestamp') > $sixmos)) {
				//-- not verified by registration past 6 months
				$toarray[$user->getUserId()] = $user->getUserName();
			}
		}
	}
	$i = 0;
	foreach ($toarray as $indexval => $to) {
		$message         = array();
		$message['to']   = $to;
		$message['from'] = $from;
		if (!empty($from_name)) {
			$message['from_name']  = $from_name;
			$message['from_email'] = $from_email;
		}
		$message['subject'] = $subject;
		$message['body']    = $body;
		$message['created'] = WT_TIMESTAMP;
		$message['method']  = $method;
		$message['url']     = $url;
		if ($i > 0) {
			$message['no_from'] = true;
		}
		if (addMessage($message)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('Message successfully sent to %s', WT_Filter::escapeHtml($to)));
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('Message was not sent'));
			Log::addErrorLog('Unable to send a message.  FROM:' . $from . ' TO:' . $to . ' (failed to send)');
		}
		$i++;
	}
	$controller
		->pageHeader()
		->addInlineJavascript('window.opener.location.reload(); window.close();');
	break;
}

/**
 * Add a message to a user's inbox
 *
 * @param string[] $message
 *
 * @return bool
 */
function addMessage($message) {
	global $WT_TREE, $WT_REQUEST;

	$success = true;

	$sender    = User::findByIdentifier($message['from']);
	$recipient = User::findByIdentifier($message['to']);

	// Sender may not be a webtrees user
	if ($sender) {
		$sender_email     = $sender->getEmail();
		$sender_real_name = $sender->getRealName();
	} else {
		$sender_email     = $message['from'];
		$sender_real_name = $message['from_name'];
	}

	// Send a copy of the copy message back to the sender.
	if ($message['method'] !== 'messaging') {
		// Switch to the sender’s language.
		if ($sender) {
			WT_I18N::init($sender->getPreference('language'));
		}

		$copy_email = $message['body'];
		if (!empty($message['url'])) {
			$copy_email .=
				WT_Mail::EOL . WT_Mail::EOL . '--------------------------------------' . WT_Mail::EOL .
				WT_I18N::translate('This message was sent while viewing the following URL: ') . $message['url'] . WT_Mail::EOL;
		}
		$copy_email .= WT_Mail::auditFooter();

		if ($sender) {
			// Message from a logged-in user
			$copy_email = WT_I18N::translate('You sent the following message to a webtrees user:') . ' ' . $recipient->getRealName() . WT_Mail::EOL . WT_Mail::EOL . $copy_email;
		} else {
			// Message from a visitor
			$copy_email = WT_I18N::translate('You sent the following message to a webtrees administrator:') . WT_Mail::EOL . WT_Mail::EOL . WT_Mail::EOL . $copy_email;
		}

		$success = $success && WT_Mail::send(
			// “From:” header
				$WT_TREE,
				// “To:” header
				$sender_email,
				$sender_real_name,
				// “Reply-To:” header
				WT_Site::getPreference('SMTP_FROM_NAME'),
				$WT_TREE->getPreference('title'),
				// Message body
				WT_I18N::translate('webtrees message') . ' - ' . $message['subject'],
				$copy_email
			);
	}

	// Switch to the recipient’s language.
	WT_I18N::init($recipient->getPreference('language'));
	if (isset($message['from_name'])) {
		$message['body'] =
			WT_I18N::translate('Your name:') . ' ' . $message['from_name'] . WT_Mail::EOL .
			WT_I18N::translate('Email address:') . ' ' . $message['from_email'] . WT_Mail::EOL . WT_Mail::EOL .
			$message['body'];
	}

	// Add another footer - unless we are an admin
	if (!Auth::isAdmin()) {
		if (!empty($message['url'])) {
			$message['body'] .=
				WT_Mail::EOL . WT_Mail::EOL .
				'--------------------------------------' . WT_Mail::EOL .
				WT_I18N::translate('This message was sent while viewing the following URL: ') . $message['url'] . WT_Mail::EOL;
		}
		$message['body'] .= WT_Mail::auditFooter();
	}

	if (empty($message['created'])) {
		$message['created'] = gmdate("D, d M Y H:i:s T");
	}

	if ($message['method'] !== 'messaging3' && $message['method'] !== 'mailto' && $message['method'] !== 'none') {
		WT_DB::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
			->execute(array(
				$message['from'],
				$WT_REQUEST->getClientIp(),
				$recipient->getUserId(),
				$message['subject'],
				str_replace('<br>', '', $message['body']) // Remove the <br> that we added for the external email.  TODO: create different messages
			));
	}
	if ($message['method'] !== 'messaging') {
		if ($sender) {
			$original_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			$original_email .= $sender->getRealName();
		} else {
			$original_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message['from_name'])) {
				$original_email .= $message['from_name'];
			} else {
				$original_email .= $message['from'];
			}
		}
		$original_email .= WT_Mail::EOL . WT_Mail::EOL . $message['body'];

		$success = $success && WT_Mail::send(
			// “From:” header
				$WT_TREE,
				// “To:” header
				$recipient->getEmail(),
				$recipient->getRealName(),
				// “Reply-To:” header
				$sender_email,
				$sender_real_name,
				// Message body
				WT_I18N::translate('webtrees message') . ' - ' . $message['subject'],
				$original_email
			);
	}

	WT_I18N::init(WT_LOCALE); // restore language settings if needed

	return $success;
}
