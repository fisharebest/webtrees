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

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller->setPageTitle(I18N::translate('webtrees message'));

// Send the message.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$to         = Filter::post('to', null, '');
	$from_name  = Filter::post('from_name', null, '');
	$from_email = Filter::postEmail('from_email');
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
			'Location: ' . WT_BASE_URL . 'message.php' .
			'?to=' . Filter::escapeUrl($to) .
			'&from_name=' . Filter::escapeUrl($from_name) .
			'&from_email=' . Filter::escapeUrl($from_email) .
			'&subject=' . Filter::escapeUrl($subject) .
			'&body=' . Filter::escapeUrl($body) .
			'&url=' . Filter::escapeUrl($url)
		);
	} else {
		// No errors.  Send the message.
		foreach ($recipients as $recipient) {
			if (deliverMessage($WT_TREE, $from_email, $from_name, $recipient, $subject, $body, $url)) {
				FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', Filter::escapeHtml($to)), 'info');
			} else {
				FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');
				Log::addErrorLog('Unable to send a message. FROM:' . $from_email . ' TO:' . $recipient->getEmail());
			}
		}

		header('Location: ' . WT_BASE_URL . $url);
	}

	return;
}

$to         = Filter::get('to', null, '');
$from_name  = Filter::get('from_name', null, '');
$from_email = Filter::getEmail('from_email', '');
$subject    = Filter::get('subject', null, '');
$body       = Filter::get('body', null, '');
$url        = Filter::getUrl('url', 'index.php');

// Only an administration can use the distribution lists.
$controller->restrictAccess(!in_array($to, ['all', 'never_logged', 'last_6mo']) || Auth::isAdmin());
$controller->pageHeader();

$to_names = implode(I18N::$list_separator, array_map(function(User $user) { return $user->getRealName(); }, recipients($to)));

?>
<h2><?= I18N::translate('Send a message') ?></h2>

<form method="post">
	<?= Filter::getCsrf() ?>
	<input type="hidden" name="url" value="<?= Filter::escapeHtml($url) ?>">

	<div class="form-group row">
		<div class="col-sm-3 col-form-label">
			<?= I18N::translate('To') ?>
		</div>
		<div class="col-sm-9">
			<input type="hidden" name="to" value="<?= Filter::escapeHtml($to) ?>">
			<div class="form-control"><?= Filter::escapeHtml($to_names) ?></div>
		</div>
	</div>

	<?php if (Auth::check()): ?>
		<div class="form-group row">
			<div class="col-sm-3 col-form-label">
				<?= I18N::translate('From') ?>
			</div>
			<div class="col-sm-9">
				<div class="form-control"><?= Filter::escapeHtml(Auth::user()->getRealName()) ?></div>
			</div>
		</div>
	<?php else: ?>
		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="from-name">
				<?= I18N::translate('Your name') ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" id="from-name" type="text" name="from_name" value="<?= Filter::escapeHtml($from_name) ?>" required>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="from-email">
				<?= I18N::translate('Email address') ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" id="from-email" type="text" name="from_email" value="<?= Filter::escapeHtml($from_email) ?>" required>
			</div>
		</div>
	<?php endif ?>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="subject">
			<?= I18N::translate('Subject') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="subject" type="text" name="subject" value="<?= Filter::escapeHtml($subject) ?>" required>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="body">
			<?= I18N::translate('Body') ?>
		</label>
		<div class="col-sm-9">
			<textarea class="form-control" id="body" type="text" name="body" required><?= Filter::escapeHtml($body) ?></textarea>
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
 * @reutrn User[]
 */
function recipients($to) {
	if ($to === 'all') {
		$recipients =	User::all();
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
	$success = true;
	$hr      = '--------------------------------------------------';
	$body    = nl2br($body, false);
	$body_cc = I18N::translate('You sent the following message to a webtrees user:') . ' ' . $recipient->getRealNameHtml() . Mail::EOL . $hr . Mail::EOL . $body;

	I18N::init($recipient->getPreference('language', WT_LOCALE));

	$body = /* I18N: %s is a person's name */ I18N::translate('%s sent you the following message.', $sender_email) . Mail::EOL . Mail::EOL . $body;

	if ($url !== 'index.php') {
		$body .= Mail::EOL . $hr . Mail::EOL . I18N::translate('This message was sent while viewing the following URL: ') . $url . Mail::EOL;

	}

	// Send via the internal messaging system.
	if (in_array($recipient->getPreference('contactmethod'), ['messaging', 'messaging2', 'mailto', 'none'])) {
		Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
			->execute([
				Auth::check() ? Auth::user()->getEmail() : $sender_email,
				WT_CLIENT_IP,
				$recipient->getUserId(),
				$subject,
				str_replace('<br>', '', $body),
			]);
	}

	// CC to the author via the internal messaging system.
	if (Auth::check() && in_array(Auth::user()->getPreference('contactmethod'), ['messaging', 'messaging2', 'mailto', 'none'])) {
		Database::prepare(
			"INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)"
		)->execute([
			Auth::user()->getEmail(),
			WT_CLIENT_IP,
			$recipient->getUserId(),
			$subject,
			str_replace('<br>', '', $body_cc),
		]);
	}

	// Send via email
	if (in_array($recipient->getPreference('contactmethod'), ['messaging2', 'messaging3', 'mailto', 'none'])) {
		$success = $success && Mail::send(
			// “From:” header
			$tree,
			// “To:” header
			$sender_email,
			$sender_name,
			// “Reply-To:” header
			Site::getPreference('SMTP_FROM_NAME'),
			$tree->getPreference('title'),
			// Message body
			I18N::translate('webtrees message') . ' - ' . $subject,
			$body
		);
	}

	I18N::init(WT_LOCALE);

	return $success;
}
