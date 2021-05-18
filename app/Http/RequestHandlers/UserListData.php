<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function e;

/**
 * List of users.
 */
class UserListData implements RequestHandlerInterface
{
    private DatatablesService $datatables_service;

    private ModuleService $module_service;

    private UserService $user_service;

    /**
     * UserListData constructor.
     *
     * @param DatatablesService $datatables_service
     * @param ModuleService     $module_service
     * @param UserService       $user_service
     */
    public function __construct(
        DatatablesService $datatables_service,
        ModuleService $module_service,
        UserService $user_service
    ) {
        $this->datatables_service = $datatables_service;
        $this->module_service     = $module_service;
        $this->user_service       = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $languages = $this->module_service->findByInterface(ModuleLanguageInterface::class, true)
            ->mapWithKeys(static function (ModuleLanguageInterface $module): array {
                $locale = $module->locale();

                return [$locale->languageTag() => $locale->endonym()];
            });

        $query = DB::table('user')
            ->leftJoin('user_setting AS us1', static function (JoinClause $join): void {
                $join
                    ->on('us1.user_id', '=', 'user.user_id')
                    ->where('us1.setting_name', '=', 'language');
            })
            ->leftJoin('user_setting AS us2', static function (JoinClause $join): void {
                $join
                    ->on('us2.user_id', '=', 'user.user_id')
                    ->where('us2.setting_name', '=', UserInterface::PREF_TIMESTAMP_REGISTERED);
            })
            ->leftJoin('user_setting AS us3', static function (JoinClause $join): void {
                $join
                    ->on('us3.user_id', '=', 'user.user_id')
                    ->where('us3.setting_name', '=', UserInterface::PREF_TIMESTAMP_ACTIVE);
            })
            ->leftJoin('user_setting AS us4', static function (JoinClause $join): void {
                $join
                    ->on('us4.user_id', '=', 'user.user_id')
                    ->where('us4.setting_name', '=', UserInterface::PREF_IS_EMAIL_VERIFIED);
            })
            ->leftJoin('user_setting AS us5', static function (JoinClause $join): void {
                $join
                    ->on('us5.user_id', '=', 'user.user_id')
                    ->where('us5.setting_name', '=', UserInterface::PREF_IS_ACCOUNT_APPROVED);
            })
            ->where('user.user_id', '>', '0')
            ->select([
                'user.user_id AS edit_menu', // Hidden column
                'user.user_id',
                'user_name',
                'real_name',
                'email',
                'us1.setting_value AS language',
                'us2.setting_value AS registered_at_sort', // Hidden column
                'us2.setting_value AS registered_at',
                'us3.setting_value AS active_at_sort', // Hidden column
                'us3.setting_value AS active_at',
                'us4.setting_value AS verified',
                'us5.setting_value AS verified_by_admin',
            ]);

        $search_columns = ['user_name', 'real_name', 'email'];
        $sort_columns   = [];

        $callback = function (stdClass $row) use ($languages, $user): array {
            $row_user = $this->user_service->find((int) $row->user_id);
            $datum = [
                view('admin/users-table-options', ['row' => $row, 'self' => $user, 'user' => $row_user]),
                $row->user_id,
                '<span dir="auto">' . e($row->user_name) . '</span>',
                '<span dir="auto">' . e($row->real_name) . '</span>',
                '<a href="mailto:' . e($row->email) . '">' . e($row->email) . '</a>',
                $languages->get($row->language, $row->language),
                $row->registered_at,
                $row->registered_at ? view('components/datetime-diff', ['timestamp' => Carbon::createFromTimestamp((int) $row->registered_at)]) : '',
                $row->active_at,
                $row->active_at ? view('components/datetime-diff', ['timestamp' => Carbon::createFromTimestamp((int) $row->active_at)]) : I18N::translate('Never'),
                $row->verified ? I18N::translate('yes') : I18N::translate('no'),
                $row->verified_by_admin ? I18N::translate('yes') : I18N::translate('no'),
            ];

            // Highlight old registrations.
            if (!$datum[10] && date('U') - $datum[6] > 604800) {
                $datum[7] = '<span class="text-danger">' . $datum[7] . '</span>';
            }

            return $datum;
        };

        return $this->datatables_service->handleQuery($request, $query, $search_columns, $sort_columns, $callback);
    }
}
