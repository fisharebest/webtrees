<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Show a registration page.
 */
class RegisterPage extends AbstractBaseController
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->checkRegistrationAllowed();

        $comments = $request->getQueryParams()['comments'] ?? '';
        $email    = $request->getQueryParams()['email'] ?? '';
        $realname = $request->getQueryParams()['realname'] ?? '';
        $username = $request->getQueryParams()['username'] ?? '';

        $show_caution = Site::getPreference('SHOW_REGISTER_CAUTION') === '1';

        $title = I18N::translate('Request a new user account');

        return $this->viewResponse('register-page', [
            'comments'     => $comments,
            'email'        => $email,
            'realname'     => $realname,
            'show_caution' => $show_caution,
            'title'        => $title,
            'username'     => $username,
        ]);
    }

    /**
     * Check that visitors are allowed to register on this site.
     *
     * @return void
     * @throws NotFoundHttpException
     */
    private function checkRegistrationAllowed(): void
    {
        if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
            throw new NotFoundHttpException();
        }
    }
}
