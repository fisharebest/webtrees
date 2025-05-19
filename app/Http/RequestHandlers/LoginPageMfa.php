<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Show a login form.
 */
class LoginPageMfa implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private TreeService $tree_service;

    /**
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->treeOptional();
        $user = Validator::attributes($request)->user();

        // Already logged in?
        if ($user instanceof User) {
            return redirect(route(UserPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : '']));
        }

        $url      = Validator::queryParams($request)->isLocalUrl()->string('url', route(HomePage::class));
        $username = Validator::queryParams($request)->string('username', '');
        
        

        // No tree?  perhaps we came here from a page without one.
        if ($tree === null) {
            $default = Site::getPreference('DEFAULT_GEDCOM');
            $tree    = $this->tree_service->all()->get($default) ?? $this->tree_service->all()->first();

            if ($tree instanceof Tree) {
                return redirect(route(self::class, ['tree' => $tree->name(), 'url' => $url]));
            }
        }

        $title = I18N::translate('Continue with MFA');
        $welcome = I18N::translate('Please enter your Google Authenticator code');
        $can_register = Site::getPreference('USE_REGISTRATION_MODULE') === '1';

        return $this->viewResponse('login-page-mfa', [
            'can_register' => $can_register,
            'title'        => $title,
            'url'          => $url,
            'tree'         => $tree,
            'username'     => $username,            
            'welcome'      => $welcome,
        ]);
    }
}
