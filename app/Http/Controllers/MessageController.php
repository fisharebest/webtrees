<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Send messages to users and groups of users.
 */
class MessageController extends AbstractBaseController {
	/**
	 * A form to compose a message from a member.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function broadcastPage(Request $request): Response {
		/** @var User $user */
		$user = $request->attributes->get('user');

		$referer = $request->headers->get('referer', '');

		$body    = $request->get('body', '');
		$subject = $request->get('subject', '');
		$to      = $request->get('to', '');
		$url     = $request->get('url', $referer);

		$to_users = $this->recipientUsers($to);
		$to_names = array_map(function (User $user) {
			return $user->getRealName();
		}, $to_users);


		$title = $this->recipientDescription($to);

		$this->layout = 'layouts/administration';

		return $this->viewResponse('broadcast-page', [
			'body'     => $body,
			'from'     => $user,
			'subject'  => $subject,
			'title'    => $title,
			'to'       => $to,
			'to_names' => $to_names,
			'url'      => $url,
		]);
	}

	/**
	 * Send a message.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function broadcastAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$body    = $request->get('body', '');
		$subject = $request->get('subject', '');
		$to      = $request->get('to', '');
		$url     = $request->get('url', '');

		$ip       = $request->getClientIp();
		$to_users = $this->recipientUsers($to);

		if ($body === '' || $subject === '') {
			return new RedirectResponse(route('broadcast', [
				'body'        => $body,
				'subject'     => $subject,
				'to'          => $to,
				'tree'        => $tree,
				'url'         => $url,
			]));
		}

		$errors = false;

		foreach ($to_users as $to_user) {
			if ($this->deliverMessage($tree, $user->getEmail(), $user->getRealName(), $to_user, $subject, $body, $url, $ip)) {
				FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->getRealName())), 'success');
			} else {
				$errors = true;
			}
		}

		if ($errors) {
			FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');
		}

		return new RedirectResponse(route('admin-control-panel'));
	}

	/**
	 * A form to compose a message from a visitor.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function contactPage(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$referer = $request->headers->get('referer', '');

		$body       = $request->get('body', '');
		$from_email = $request->get('from_email', '');
		$from_name  = $request->get('from_name', '');
		$subject    = $request->get('subject', '');
		$to         = $request->get('to', '');
		$url        = $request->get('url', $referer);

		$to_user = User::findByUserName($to);

		if (!in_array($to_user, $this->validContacts($tree))) {
			throw new AccessDeniedHttpException('Invalid contact user id');
		}

		$to_name = $to_user->getRealName();

		$title = I18N::translate('Send a message');

		return $this->viewResponse('contact-page', [
			'body'       => $body,
			'from_email' => $from_email,
			'from_name'  => $from_name,
			'subject'    => $subject,
			'title'      => $title,
			'to'         => $to,
			'to_name'    => $to_name,
			'url'        => $url,
		]);
	}

	/**
	 * Send a message.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function contactAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$body       = $request->get('body', '');
		$from_email = $request->get('from_email', '');
		$from_name  = $request->get('from_name', '');
		$subject    = $request->get('subject', '');
		$to         = $request->get('to', '');
		$url        = $request->get('url', '');

		$to_user = User::findByUserName($to);
		$ip      = $request->getClientIp();

		if (!in_array($to_user, $this->validContacts($tree))) {
			throw new AccessDeniedHttpException('Invalid contact user id');
		}

		$errors = $body !== '' && $subject !== '' && $from_email !== '' && $from_name !== '';

		if (!preg_match('/^[^@]+@([^@]+)$/', $from_email, $match) || !checkdnsrr($match[1])) {
			FlashMessages::addMessage(I18N::translate('Please enter a valid email address.'), 'danger');
			$errors = true;
		}

		if (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $subject . $body, $match)) {
			FlashMessages::addMessage(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . /* I18N: e.g. ‘You should delete the “http://” from “http://www.example.com” and try again.’ */
				I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1]), 'danger');
			$errors = true;
		}

		if ($errors) {
			return new RedirectResponse(route('contact', [
				'body'       => $body,
				'from_email' => $from_email,
				'from_name'  => $from_name,
				'subject'    => $subject,
				'to'         => $to,
				'tree'       => $tree,
				'url'        => $url,
			]));
		}

		if ($this->deliverMessage($tree, $from_email, $from_name, $to_user, $subject, $body, $url, $ip)) {
			FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->getRealName())), 'success');

			$url = $url ?: route('home-page', ['ged' => $tree->getName()]);

			return new RedirectResponse($url);
		} else {
			FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

			$redirect_url = route('contact', [
				'body'       => $body,
				'from_email' => $from_email,
				'from_name'  => $from_name,
				'subject'    => $subject,
				'to'         => $to,
				'url'        => $url,
			]);

			return new RedirectResponse($redirect_url);
		}
	}

	/**
	 * A form to compose a message from a member.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function messagePage(Request $request): Response {
		/** @var User $user */
		$user = $request->attributes->get('user');

		$referer = $request->headers->get('referer', '');

		$body    = $request->get('body', '');
		$subject = $request->get('subject', '');
		$to      = $request->get('to', '');
		$url     = $request->get('url', $referer);

		$to_user = User::findByUserName($to);

		if ($to_user === null || $to_user->getPreference('contactmethod') === 'none') {
			throw new AccessDeniedHttpException('Invalid contact user id');
		}

		$title = I18N::translate('Send a message');

		return $this->viewResponse('message-page', [
			'body'    => $body,
			'from'    => $user,
			'subject' => $subject,
			'title'   => $title,
			'to'      => $to_user,
			'url'     => $url,
		]);
	}

	/**
	 * Send a message.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function messageAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$body    = $request->get('body', '');
		$subject = $request->get('subject', '');
		$to      = $request->get('to', '');
		$url     = $request->get('url', '');

		$to_user = User::findByUserName($to);
		$ip      = $request->getClientIp();

		if ($to_user === null || $to_user->getPreference('contactmethod') === 'none') {
			throw new AccessDeniedHttpException('Invalid contact user id');
		}

		if ($body === '' || $subject === '') {
			return new RedirectResponse(route('message', [
				'body'    => $body,
				'subject' => $subject,
				'to'      => $to,
				'tree'    => $tree,
				'url'     => $url,
			]));
		}

		if ($this->deliverMessage($tree, $user->getEmail(), $user->getRealName(), $to_user, $subject, $body, $url, $ip)) {
			FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->getRealName())), 'success');

			$url = $url ?: route('home-page', ['ged' => $tree->getName()]);

			return new RedirectResponse($url);
		} else {
			FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

			$redirect_url = route('contact', [
				'body'    => $body,
				'subject' => $subject,
				'to'      => $to,
				'url'     => $url,
			]);

			return new RedirectResponse($redirect_url);
		}
	}

	/**
	 * Contact messages can only be sent to the designated contacts
	 *
	 * @param Tree $tree
	 *
	 * @return User[]
	 */
	private function validContacts(Tree $tree) {
		$contacts = [
			User::find((int) $tree->getPreference('CONTACT_USER_ID')),
			User::find((int) $tree->getPreference('WEBMASTER_USER_ID')),
		];

		return array_filter($contacts);
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
	 * @param string $ip
	 *
	 * @return bool
	 */
	private function deliverMessage(Tree $tree, string $sender_email, string $sender_name, User $recipient, string $subject, string $body, string $url, string $ip): bool {
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

		$success = true;

		// Temporarily switch to the recipient's language
		I18N::init($recipient->getPreference('language'));

		// Send via the internal messaging system.
		if ($this->sendInternalMessage($recipient)) {
			Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
				->execute([
					Auth::check() ? Auth::user()->getEmail() : $sender_email,
					$ip,
					$recipient->getUserId(),
					$subject,
					view('emails/message-user-text', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url]),
				]);
		}

		// Send via email
		if ($this->sendEmail($recipient)) {
			$success = $success && Mail::send(
					$from,
					$recipient,
					$sender,
					I18N::translate('webtrees message') . ' - ' . $subject,
					view('emails/message-user-text', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url]),
					view('emails/message-user-html', ['sender' => $sender, 'recipient' => $recipient, 'message' => $body, 'url' => $url])
				);
		}

		I18N::init(WT_LOCALE);

		return $success;
	}

	/**
	 * Should we send messages to this user via internal messaging?
	 *
	 * @param User $user
	 *
	 * @return bool
	 */
	private function sendInternalMessage(User $user): bool {
		return in_array($user->getPreference('contactmethod'), ['messaging', 'messaging2', 'mailto', 'none']);
	}

	/**
	 * Should we send messages to this user via email?
	 *
	 * @param User $user
	 *
	 * @return bool
	 */
	private function sendEmail(User $user): bool {
		return in_array($user->getPreference('contactmethod'), ['messaging2', 'messaging3', 'mailto', 'none']);
	}

	/**
	 * Convert a username (or mailing list name) into an array of recipients.
	 *
	 * @param $to
	 *
	 * @return User[]
	 */
	private function recipientUsers(string $to): array {
		switch ($to) {
			default:
			case 'all':
				return User::all();
			case 'never_logged':
				return array_filter(User::all(), function (User $user) {
					return $user->getPreference('verified_by_admin') && $user->getPreference('reg_timestamp') > $user->getPreference('sessiontime');
				});
			case 'last_6mo':
				return array_filter(User::all(), function (User $user) {
					return $user->getPreference('sessiontime') > 0 && WT_TIMESTAMP - $user->getPreference('sessiontime') > 60 * 60 * 24 * 30 * 6;
				});
		}
	}

	/**
	 * @param string $to
	 *
	 * @return string
	 */
	private function recipientDescription(string $to): string {
		switch ($to) {
			default:
			case 'all':
				return I18N::translate('Send a message to all users');
			case 'never_logged':
				return I18N::translate('Send a message to users who have never signed in');
			case 'last_6mo':
				return I18N::translate('Send a message to users who have not signed in for 6 months');
		}
	}
}
