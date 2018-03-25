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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Send messages to users and groups of users.
 */
class MessageController extends AbstractBaseController {
	/**
	 * A form to compose a message.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function page(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$body       = $request->get('body');
		$from_email = $request->get('from_email');
		$from_name  = $request->get('from_name');
		$subject    = $request->get('subject');
		$to         = $request->get('to');
		$url        = $request->get('url');

		return $this->viewResponse('message-page', [
			'body'       => $body,
			'from_email' => $from_email,
			'from_name'  => $from_name,
			'subject'    => $subject,
			'to'         => $to,
			'tree'       => $tree,
			'url'        => $url,
		]);
	}

	/**
	 * Send a message.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function send(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');
	}
}
