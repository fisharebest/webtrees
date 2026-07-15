<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Factories\LanguageFactory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_combine;

final class AccountEdit implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly LanguageFactory $language_factory,
        private readonly MessageService $message_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->treeOptional();
        $user = Validator::attributes($request)->user();

        if ($tree instanceof Tree) {
            $my_individual_record = Registry::individualFactory()->make($tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF), $tree);
            $default_individual   = Registry::individualFactory()->make($tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_DEFAULT_XREF), $tree);
        } else {
            $my_individual_record = null;
            $default_individual   = null;
        }

        $show_delete_option = $user->getPreference(UserInterface::PREF_IS_ADMINISTRATOR) !== '1';
        $timezone_ids       = DateTimeZone::listIdentifiers();
        $timezones          = array_combine($timezone_ids, $timezone_ids);
        $title              = I18N::translate('My account');

        return $this->viewResponse('edit-account-page', [
            'contact_methods'      => $this->message_service->contactMethods(),
            'default_individual'   => $default_individual,
            'languages'            => I18N::allLanguages(),
            'my_individual_record' => $my_individual_record,
            'show_delete_option'   => $show_delete_option,
            'timezones'            => $timezones,
            'title'                => $title,
            'tree'                 => $tree,
            'user'                 => $user,
        ]);
    }
}
