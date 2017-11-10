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
use Symfony\Component\HttpFoundation\Request;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller->setPageTitle(I18N::translate('webtrees message'));

// Send the message.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$to         = Filter::post('to', null, '');
	$from_name  = Filter::post('from_name', null, '');
	$from_email = Filter::post('from_email');
	$subject    = Filter::post('subject', null, '');
	$body       = Filter::post('body', null, '');
	$url        = Filter::postUrl('url', 'index.php');

	// Only an administration can use the distribution lists.
	$controller->restrictAccess(!in_array($to, ['all', 'never_logged', 'last_6mo']) || Auth::isAdmin());

	$recipients = recipients($to);

	// Different validation for admin/user/visitor.
	$errors = !Filter::checkCsrf();
	if (Auth::check()) {
		$from_name  = Auth::user()->getRealName();
		$from_email = Auth::user()->getEmail();
	} elseif ($from_name === '' || $from_email === '') {
		$errors = true;
	} elseif (!preg_match('/@(.+)/', $from_email, $match) || function_exists('checkdnsrr') && !checkdnsrr($match[1])) {
		FlashMessages::addMessage(I18N::translate('Please enter a valid email address.'), 'danger');
		$errors = true;
	} elseif (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $subject . $body, $match)) {
		FlashMessages::addMessage(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . /* I18N: e.g. ‘You should delete the “http://” from “http://www.example.com” and try again.’ */ I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1]), 'danger');
		$errors = true;
	} elseif (empty($recipients)) {
		$errors = true;
	}

	if ($errors) {
		// Errors? Go back to the form.
		header(
			'Location: message.php' .
			'?to=' . rawurlencode($to) .
			'&from_name=' . rawurlencode($from_name) .
			'&from_email=' . rawurlencode($from_email) .
			'&subject=' . rawurlencode($subject) .
			'&body=' . rawurlencode($body) .
			'&url=' . rawurlencode($url)
		);
	} else {
		// No errors.  Send the message.
		foreach ($recipients as $recipient) {
			if (deliverMessage($WT_TREE, $from_email, $from_name, $recipient, $subject, $body, $url)) {
				FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', Html::escape($to)), 'info');
			} else {
				FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');
				Log::addErrorLog('Unable to send a message. FROM:' . $from_email . ' TO:' . $recipient->getEmail());
			}
		}

		header('Location: ' . $url);
	}

	return;
}

$to         = Filter::get('to', null, '');
$from_name  = Filter::get('from_name', null, '');
$from_email = Filter::get('from_email', '');
$subject    = Filter::get('subject', null, '');
$body       = Filter::get('body', null, '');
$url        = Filter::getUrl('url', 'index.php');

// Only an administration can use the distribution lists.
$controller->restrictAccess(!in_array($to, ['all', 'never_logged', 'last_6mo']) || Auth::isAdmin());
$controller->pageHeader();

$to_names = implode(I18N::$list_separator, array_map(function(User $user) {
	return $user->getRealName();
}, recipients($to)));

?>
<h2><?= I18N::translate('Send a message') ?></h2>

<form method="post">
	<?= Filter::getCsrf() ?>
	<input type="hidden" name="url" value="<?= Html::escape($url) ?>">

	<div class="form-group row">
		<div class="col-sm-3 col-form-label">
			<?= I18N::translate('To') ?>
		</div>
		<div class="col-sm-9">
			<input type="hidden" name="to" value="<?= Html::escape($to) ?>">
			<div class="form-control"><?= Html::escape($to_names) ?></div>
		</div>
	</div>

	<?php if (Auth::check()): ?>
		<div class="form-group row">
			<div class="col-sm-3 col-form-label">
				<?= I18N::translate('From') ?>
			</div>
			<div class="col-sm-9">
				<div class="form-control"><?= Html::escape(Auth::user()->getRealName()) ?></div>
			</div>
		</div>
	<?php else: ?>
		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="from-name">
				<?= I18N::translate('Your name') ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" id="from-name" type="text" name="from_name" value="<?= Html::escape($from_name) ?>" required>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="from-email">
				<?= I18N::translate('Email address') ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" id="from-email" type="text" name="from_email" value="<?= Html::escape($from_email) ?>" required>
			</div>
		</div>
	<?php endif ?>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="subject">
			<?= I18N::translate('Subject') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="subject" type="text" name="subject" value="<?= Html::escape($subject) ?>" required>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="body">
			<?= I18N::translate('Body') ?>
		</label>
		<div class="col-sm-9">
			<textarea class="form-control" id="body" type="text" name="body" required><?= Html::escape($body) ?></textarea>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-9 push-sm-3">
			<button type="submit" class="btn btn-primary">
				<?= I18N::translate('Send') ?>
			</button>
		</div>
	</div>
</form>

<?php

/**
 * Convert a username (or mailing list name) into an array of recipients.
 *
 * @param $to
 *
 * @return User[]
 */
function recipients($to) {
	if ($to === 'all') {
		$recipients = User::all();
	} elseif ($to === 'last_6mo') {
		$recipients = array_filter(User::all(), function(User $user) {
			return $user->getPreference('sessiontime') > 0 && WT_TIMESTAMP - $user->getPreference('sessiontime') > 60 * 60 * 24 * 30 * 6;
		});
	} elseif ($to === 'never_logged') {
		$recipients = array_filter(User::all(), function(User $user) {
			return $user->getPreference('verified_by_admin') && $user->getPreference('reg_timestamp') > $user->getPreference('sessiontime');
		});
	} else {
		$recipients = array_filter([User::findByUserName($to)]);
	}

	return $recipients;
}

/**
 * Add a message to a user's inbox, send it to them via email, or both.
 *
 * @param Tree   $tree
 * @param string $sender_name
 * @param string $sender_email
 * @param User   $recipient
 * @param string $subject
 * @param string $body
 * @param string $url
 *
 * @return bool
 */
function deliverMessage(Tree $tree, $sender_email, $sender_name, User $recipient, $subject, $body, $url) {
	// Create a dummy user, so we can send messages from the tree.
	$from = new User(
		(object) [
			'user_id'   => null,
			'user_name' => '',
			'real_name' => $tree->getTitle(),
			'email'     => $tree->getPreference('WEBTREES_EMAIL'),
		]
	);

	// Create a dummy user, so we can reply to visitors.
	$sender = new User(
		(object) [
			'user_id'   => null,
			'user_name' => '',
			'real_name' => $sender_name,
			'email'     => $sender_email,
		]
	);

	$request = Request::createFromGlobals();

	$success = true;

	// Switch to the recipient's language
	I18N::init($recipient->getPreference('language'));

	// Send via the internal messaging system.
	if (in_array($recipient->getPreference('contactmethod'), ['messaging', 'messaging2', 'mailto', 'none'])) {
		Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
			->execute([
				Auth::check() ? Auth::user()->getEmail() : $sender_email,
				$request->getClientIp(),
				$recipient->getUserId(),
				$subject,
				View::make('emails/message-user-text', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url]),
			]);
	}

	// Send via email
	if (in_array($recipient->getPreference('contactmethod'), ['messaging2', 'messaging3', 'mailto', 'none'])) {
		$success = $success && Mail::send(
				$from,
				$recipient,
				$sender,
				I18N::translate('webtrees message') . ' - ' . $subject,
				View::make('emails/message-user-text', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url]),
				View::make('emails/message-user-html', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url])
			);
	}

	I18N::init(WT_LOCALE);

	// Copy the message back to the user
	if (Auth::check() && in_array(Auth::user()->getPreference('contactmethod'), ['messaging', 'messaging2', 'mailto', 'none'])) {
		Database::prepare(
			"INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)"
		)->execute([
			Auth::user()->getEmail(),
			$request->getClientIp(),
			$recipient->getUserId(),
			$subject,
			View::make('emails/message-copy-text', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url])
		]);
	}

	return $success;
}
