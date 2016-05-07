<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

use Fisharebest\Webtrees\Controller\SimpleController;

define('WT_SCRIPT_NAME', 'message.php');
require './includes/session.php';

// Some variables are initialised from GET (so we can set initial values in URLs),
// but are submitted in POST so we can have long body text.

$subject    = Filter::post('subject', null, Filter::get('subject'));
$body       = Filter::post('body');
$from_name  = Filter::post('from_name');
$from_email = Filter::post('from_email');
$action     = Filter::post('action', 'compose|send', 'compose');
$to         = Filter::post('to', null, Filter::get('to'));
$method     = Filter::post('method', 'messaging|messaging2|messaging3|mailto|none', Filter::get('method', 'messaging|messaging2|messaging3|mailto|none', 'messaging2'));
$url        = Filter::postUrl('url', Filter::getUrl('url'));

$to_user = User::findByUserName($to);

$controller = new SimpleController;
$controller
	->restrictAccess($to_user || Auth::isAdmin() && ($to === 'all' || $to === 'last_6mo' || $to === 'never_logged'))
	->setPageTitle(I18N::translate('webtrees message'));

$errors = '';

// Is this message from a member or a visitor?
if (Auth::check()) {
	$from = Auth::user()->getUserName();
} else {
	// Visitors must provide a valid email address
	if ($from_email && (!preg_match("/(.+)@(.+)/", $from_email, $match) || function_exists('checkdnsrr') && checkdnsrr($match[2]) === false)) {
		$errors .= '<p class="ui-state-error">' . I18N::translate('Please enter a valid email address.') . '</p>';
		$action = 'compose';
	}

	// Do not allow anonymous visitors to include links to external sites
	if (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $subject . $body, $match)) {
		$errors .=
			'<p class="ui-state-error">' . I18N::translate('You are not allowed to send messages that contain external links.') . '</p>' .
			'<p class="ui-state-highlight">' . /* I18N: e.g. ‘You should delete the “http://” from “http://www.example.com” and try again.’ */ I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1]) . '</p>' .
			Log::addAuthenticationLog('Possible spam message from "' . $from_name . '"/"' . $from_email . '", subject="' . $subject . '", body="' . $body . '"');
		$action = 'compose';
	}
	$from = $from_email;
}

// Ensure the user always visits this page twice - once to compose it and again to send it.
// This makes it harder for spammers.
switch ($action) {
case 'compose':
	Session::put('good_to_send', true);
	break;
case 'send':
	// Only send messages if we've come straight from the compose page.
	if (!Session::get('good_to_send')) {
		Log::addAuthenticationLog('Attempt to send a message without visiting the compose page. Spam attack?');
		$action = 'compose';
	}
	if (!Filter::checkCsrf()) {
		$action = 'compose';
	}
	Session::forget('good_to_send');
	break;
}

switch ($action) {
case 'compose':
	$controller
		->pageHeader()
		->addInlineJavascript('
		function checkForm(frm) {
			if (frm.subject.value === "") {
				alert("' . I18N::translate('Please enter a message subject.') . '");
				document.messageform.subject.focus();
				return false;
			}
			if (frm.body.value === "") {
				alert("' . I18N::translate('Please enter some message text before sending.') . '");
				document.messageform.body.focus();
				return false;
			}
			return true;
		}
	');
	echo '<span class="subheaders">', I18N::translate('Send a message'), '</span>';
	echo $errors;

	if (!Auth::check()) {
		echo '<br><br>', I18N::translate('<b>Please note:</b> Private information of living individuals will only be given to family relatives and close friends. You will be asked to verify your relationship before you will receive any private data. Sometimes information of dead individuals may also be private. If this is the case, it is because there is not enough information known about the individual to determine whether they are alive or not and we probably do not have more information on this individual.<br><br>Before asking a question, please verify that you are inquiring about the correct individual by checking dates, places, and close relatives. If you are submitting changes to the genealogy data, please include the sources where you obtained the data.');
	}
	echo '<br><form name="messageform" method="post" action="message.php" onsubmit="t = new Date(); document.messageform.time.value=t.toUTCString(); return checkForm(this);">';
	echo Filter::getCsrf();
	echo '<table>';
	if ($to !== 'all' && $to !== 'last_6mo' && $to !== 'never_logged') {
		echo '<tr><td></td><td>', I18N::translate('This message will be sent to %s', '<b>' . $to_user->getRealNameHtml() . '</b>'), '</td></tr>';
	}
	if (!Auth::check()) {
		echo '<tr style="vertical-align:top;"><td width="15%">', I18N::translate('Your name'), '</td>';
		echo '<td><input type="text" name="from_name" size="40" value="', Filter::escapeHtml($from_name), '"></td></tr><tr style="vertical-align:top;"><td>', I18N::translate('Email address'), '</td><td><input type="email" name="from_email" size="40" value="', Filter::escapeHtml($from_email), '"><br>', I18N::translate('Please provide your email address so that we may contact you in response to this message. If you do not provide your email address we will not be able to respond to your inquiry. Your email address will not be used in any other way besides responding to this inquiry.'), '<br><br></td></tr>';
	}
	echo '<tr style="vertical-align:top;"><td>', I18N::translate('Subject'), '</td>';
	echo '<td>';
	echo '<input type="hidden" name="action" value="send">';
	echo '<input type="hidden" name="to" value="', Filter::escapeHtml($to), '">';
	echo '<input type="hidden" name="time" value="">';
	echo '<input type="hidden" name="method" value="', $method, '">';
	echo '<input type="hidden" name="url" value="', Filter::escapeHtml($url), '">';
	echo '<input type="text" name="subject" size="50" value="', Filter::escapeHtml($subject), '"><br></td></tr>';
	echo '<tr style="vertical-align:top;"><td>', I18N::translate('Body'), '<br></td><td><textarea name="body" cols="50" rows="7">', Filter::escapeHtml($body), '</textarea><br></td></tr>';
	echo '<tr><td></td><td><input type="submit" value="', I18N::translate('Send'), '"></td></tr>';
	echo '</table>';
	echo '</form>';
	if ($method === 'messaging2') {
		echo I18N::translate('When you send this message you will receive a copy sent via email to the address you provided.');
	}
	echo
		'<br><br><br><br>',
		'<p id="save-cancel">',
		'<input type="button" class="cancel" value="', I18N::translate('close'), '" onclick="window.close();">',
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
		$message['body']    = nl2br($body, false);
		$message['created'] = WT_TIMESTAMP;
		$message['method']  = $method;
		$message['url']     = $url;
		if ($i > 0) {
			$message['no_from'] = true;
		}
		if (addMessage($message)) {
			FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', Filter::escapeHtml($to)));
		} else {
			FlashMessages::addMessage(I18N::translate('The message was not sent.'));
			Log::addErrorLog('Unable to send a message. FROM:' . $from . ' TO:' . $to . ' (failed to send)');
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
	global $WT_TREE;

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
			I18N::init($sender->getPreference('language'));
		}

		$copy_email = $message['body'];
		if (!empty($message['url'])) {
			$copy_email .=
				Mail::EOL . Mail::EOL . '--------------------------------------' . Mail::EOL .
				I18N::translate('This message was sent while viewing the following URL: ') . $message['url'] . Mail::EOL;
		}

		if ($sender) {
			// Message from a signed-in user
			$copy_email = I18N::translate('You sent the following message to a webtrees user:') . ' ' . $recipient->getRealNameHtml() . Mail::EOL . Mail::EOL . $copy_email;
		} else {
			// Message from a visitor
			$copy_email = I18N::translate('You sent the following message to a webtrees administrator:') . Mail::EOL . Mail::EOL . Mail::EOL . $copy_email;
		}

		$success = $success && Mail::send(
			// “From:” header
				$WT_TREE,
				// “To:” header
				$sender_email,
				$sender_real_name,
				// “Reply-To:” header
				Site::getPreference('SMTP_FROM_NAME'),
				$WT_TREE->getPreference('title'),
				// Message body
				I18N::translate('webtrees message') . ' - ' . $message['subject'],
				$copy_email
			);
	}

	// Switch to the recipient’s language.
	I18N::init($recipient->getPreference('language'));
	if (isset($message['from_name'])) {
		$message['body'] =
			I18N::translate('Your name') . ' ' . $message['from_name'] . Mail::EOL .
			I18N::translate('Email address') . ' ' . $message['from_email'] . Mail::EOL . Mail::EOL .
			$message['body'];
	}

	// Add another footer - unless we are an admin
	if (!Auth::isAdmin()) {
		if (!empty($message['url'])) {
			$message['body'] .=
				Mail::EOL . Mail::EOL .
				'--------------------------------------' . Mail::EOL .
				I18N::translate('This message was sent while viewing the following URL: ') . $message['url'] . Mail::EOL;
		}
	}

	if (empty($message['created'])) {
		$message['created'] = gmdate("D, d M Y H:i:s T");
	}

	if ($message['method'] !== 'messaging3' && $message['method'] !== 'mailto' && $message['method'] !== 'none') {
		Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
			->execute(array(
				$message['from'],
				WT_CLIENT_IP,
				$recipient->getUserId(),
				$message['subject'],
				str_replace('<br>', '', $message['body']), // Remove the <br> that we added for the external email. Perhaps create different messages
			));
	}
	if ($message['method'] !== 'messaging') {
		if ($sender) {
			$original_email = I18N::translate('The following message has been sent to your webtrees user account from ');
			$original_email .= $sender->getRealNameHtml();
		} else {
			$original_email = I18N::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message['from_name'])) {
				$original_email .= $message['from_name'];
			} else {
				$original_email .= $message['from'];
			}
		}
		$original_email .= Mail::EOL . Mail::EOL . $message['body'];

		$success = $success && Mail::send(
			// “From:” header
				$WT_TREE,
				// “To:” header
				$recipient->getEmail(),
				$recipient->getRealName(),
				// “Reply-To:” header
				$sender_email,
				$sender_real_name,
				// Message body
				I18N::translate('webtrees message') . ' - ' . $message['subject'],
				$original_email
			);
	}

	I18N::init(WT_LOCALE); // restore language settings if needed

	return $success;
}
