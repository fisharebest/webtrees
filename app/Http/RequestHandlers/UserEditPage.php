<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Edit a user.
 */
class UserEditPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private MessageService $message_service;

    private ModuleService $module_service;

    private UserService $user_service;

    private TreeService $tree_service;

    /**
     * @param MessageService $message_service
     * @param ModuleService  $module_service
     * @param TreeService    $tree_service
     * @param UserService    $user_service
     */
    public function __construct(
        MessageService $message_service,
        ModuleService $module_service,
        TreeService $tree_service,
        UserService $user_service
    ) {
        $this->message_service = $message_service;
        $this->module_service  = $module_service;
        $this->tree_service    = $tree_service;
        $this->user_service    = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $user_id = Validator::queryParams($request)->integer('user_id');
        $user    = $this->user_service->find($user_id);

        if ($user === null) {
            throw new HttpNotFoundException(I18N::translate('%s does not exist.', 'user_id:' . $user_id));
        }

        $languages = $this->module_service->findByInterface(ModuleLanguageInterface::class, true, true)
            ->mapWithKeys(static function (ModuleLanguageInterface $module): array {
                $locale = $module->locale();

                return [$locale->languageTag() => $locale->endonym()];
            });

        $roles = [
            /* I18N: Listbox entry; name of a role */
            UserInterface::ROLE_VISITOR   => I18N::translate('Visitor'),
            /* I18N: Listbox entry; name of a role */
            UserInterface::ROLE_MEMBER    => I18N::translate('Member'),
            /* I18N: Listbox entry; name of a role */
            UserInterface::ROLE_EDITOR    => I18N::translate('Editor'),
            /* I18N: Listbox entry; name of a role */
            UserInterface::ROLE_MODERATOR => I18N::translate('Moderator'),
            /* I18N: Listbox entry; name of a role */
            UserInterface::ROLE_MANAGER   => I18N::translate('Manager'),
        ];

        $theme_options = $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');

        return $this->viewResponse('admin/users-edit', [
            'contact_methods' => $this->message_service->contactMethods(),
            'languages'       => $languages->all(),
            'roles'           => $roles,
            'trees'           => $this->tree_service->all(),
            'theme_options'   => $theme_options,
            'title'           => I18N::translate('Edit the user'),
            'user'            => $user,
        ]);
    }
}
