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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * User actions
 */
class UserController extends AbstractBaseController
{
    /**
     * @var UserService
     */
    private $user_service;

    /**
     * UserController constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * Delete a user.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $user_id = (int) $request->get('user_id');

        $user = $this->user_service->find($user_id);

        if ($user && Auth::isAdmin() && Auth::user() !== $user) {
            Log::addAuthenticationLog('Deleted user: ' . $user->userName());
            $this->user_service->delete($user);
        }

        return response();
    }

    /**
     * Select a language.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function language(ServerRequestInterface $request): ResponseInterface
    {
        $language = $request->get('language', '');

        I18N::init($language);
        Session::put('locale', $language);
        Auth::user()->setPreference('language', $language);

        return response();
    }

    /**
     * Masquerade as another user.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function masquerade(ServerRequestInterface $request): ResponseInterface
    {
        $user_id = (int) $request->get('user_id');

        $user = $this->user_service->find($user_id);

        if ($user !== null && Auth::isAdmin() && Auth::user() !== $user) {
            Log::addAuthenticationLog('Masquerade as user: ' . $user->userName());
            Auth::login($user);
            Session::put('masquerade', '1');
        }

        return response();
    }

    /**
     * Select a theme.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function theme(ServerRequestInterface $request): ResponseInterface
    {
        $theme = $request->get('theme', '');
        Session::put('theme_id', $theme);
        Auth::user()->setPreference('theme', $theme);

        return response();
    }
}
