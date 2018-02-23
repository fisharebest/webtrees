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

namespace Fisharebest\Webtrees\Http\Controllers\Auth;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for email verification.
 */
class VerifyEmailController extends AbstractBaseController {
	/**
	 * Respond to a verification link that was emailed to a user.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
	public function verify(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$username = $request->get('username', '');
		$token    = $request->get('token', '');

		$title = I18N::translate('User verification');

		$user = User::findByUserName($username);

		if ($user !== null && $user->getPreference('reg_hashcode') === $token) {
			// switch language to webmaster settings
			$webmaster = User::find($tree->getPreference('WEBMASTER_USER_ID'));
			I18N::init($webmaster->getPreference('language'));

			// Create a dummy user, so we can send messages from the tree.
			$sender = new User(
				(object) [
					'user_id'   => null,
					'user_name' => '',
					'real_name' => $tree->getTitle(),
					'email'     => $tree->getPreference('WEBTREES_EMAIL'),
				]
			);

			Mail::send(
				$sender,
				$webmaster,
				$sender,
				/* I18N: %s is a server name/URL */
				I18N::translate('New user at %s', WT_BASE_URL . ' ' . $tree->getTitle()),
				view('emails/verify-notify-text', ['user' => $user]),
				view('emails/verify-notify-html', ['user' => $user])
			);

			$mail1_method = $webmaster->getPreference('CONTACT_METHOD');
			if ($mail1_method !== 'messaging3' && $mail1_method !== 'mailto' && $mail1_method !== 'none') {
				Database::prepare(
					"INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)"
				)->execute([
					$username,
					$request->getClientIp(),
					$webmaster->getUserId(),
					/* I18N: %s is a server name/URL */
					I18N::translate('New user at %s', WT_BASE_URL . ' ' . $tree->getTitle()),
					view('emails/verify-notify-text', ['user' => $user]),
				]);
			}
			I18N::init(WT_LOCALE);

			$user
				->setPreference('verified', '1')
				->setPreference('reg_timestamp', date('U'))
				->setPreference('reg_hashcode', '');

			Log::addAuthenticationLog('User ' . $username . ' verified their email address');

			return $this->viewResponse('verify-success-page', [
				'title' => $title,
			]);
		} else {
			return $this->viewResponse('verify-failure-page', [
				'title' => $title,
			]);
		}
	}
}
