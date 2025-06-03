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

use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\QrcodeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_combine;

/**
 * Edit user account details.
 */
class AccountEdit implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private MessageService $message_service;

    private ModuleService $module_service;

    private QrcodeService $qrcode_service;

    /**
     * @param MessageService $message_service
     * @param ModuleService  $module_service
     * @param QrcodeService  $qrcode_service
     */
    public function __construct(MessageService $message_service, ModuleService $module_service, QrcodeService $qrcode_service)
    {
        $this->message_service = $message_service;
        $this->module_service = $module_service;
        $this->qrcode_service = $qrcode_service;
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

        if ($tree instanceof Tree) {
            $my_individual_record = Registry::individualFactory()->make($tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF), $tree);
            $default_individual   = Registry::individualFactory()->make($tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_DEFAULT_XREF), $tree);
        } else {
            $my_individual_record = null;
            $default_individual   = null;
        }

        $languages = $this->module_service->findByInterface(ModuleLanguageInterface::class, true, true)
            ->mapWithKeys(static function (ModuleLanguageInterface $module): array {
                $locale = $module->locale();

                return [$locale->languageTag() => $locale->endonym()];
            });

        $show_delete_option = $user->getPreference(UserInterface::PREF_IS_ADMINISTRATOR) !== '1';
        $show_2fa = Site::getPreference('SHOW_2FA_OPTION') === '1';
        $timezone_ids       = DateTimeZone::listIdentifiers();
        $timezones          = array_combine($timezone_ids, $timezone_ids);
        $title              = I18N::translate('My account');

        return $this->viewResponse('edit-account-page', [
            'contact_methods'      => $this->message_service->contactMethods(),
            'default_individual'   => $default_individual,
            'languages'            => $languages->all(),
            'my_individual_record' => $my_individual_record,
            'show_delete_option'   => $show_delete_option,
            'show_2fa'             => $show_2fa,
            'qrcode'               => $this->qrcode_service->genQRcode(),
            'timezones'            => $timezones,
            'title'                => $title,
            'tree'                 => $tree,
            'user'                 => $user,
        ]);
    }
}
