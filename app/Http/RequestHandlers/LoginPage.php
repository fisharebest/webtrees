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

final class LoginPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly TreeService $tree_service,
    ) {
    }

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

        $title = I18N::translate('Sign in');

        switch (Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
            case '1':
            default:
                $welcome = I18N::translate('Anyone with a user account can access this website.');
                break;
            case '2':
                $welcome = I18N::translate('You need to be an authorized user to access this website.');
                break;
            case '3':
                $welcome = I18N::translate('You need to be a family member to access this website.');
                break;
            case '4':
                $welcome = Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . I18N::languageTag());

                if ($welcome === '') {
                    // No value for this language?  Try the default language.
                    $language = Site::getPreference('LANGUAGE');
                    $welcome  = Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . $language);
                }

                break;
        }

        if (Site::getPreference('USE_REGISTRATION_MODULE') === '1') {
            $welcome .= '<br>' . I18N::translate('You can apply for an account using the link below.');
        }

        $can_register = Site::getPreference('USE_REGISTRATION_MODULE') === '1';

        return $this->viewResponse('login-page', [
            'can_register' => $can_register,
            'title'        => $title,
            'url'          => $url,
            'tree'         => $tree,
            'username'     => $username,
            'welcome'      => $welcome,
        ]);
    }
}
