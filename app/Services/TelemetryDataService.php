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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Support\Collection;
use PDO;

use function ini_get;
use function php_uname;

/**
 * Assemble telemetry data payload for submission.
 */
class TelemetryDataService
{
    public function __construct(
        private readonly ModuleService $module_service,
        private readonly TreeService $tree_service,
        private readonly UserService $user_service,
    ) {
    }

    /**
     * Assemble the complete telemetry payload as an associative array.
     *
     * @return array<string,mixed>
     */
    public function assemblePayload(): array
    {
        return [
            'p_site_uuid' => $this->siteUuid(),
            'p_metrics'   => $this->metrics(),
        ];
    }

    private function siteUuid(): string
    {
        return Site::getUuid();
    }

    /**
     * @return array<string,mixed>
     */
    private function metrics(): array
    {
        return [
            'php_version'              => PHP_VERSION,
            'php_memory_limit'         => ini_get('memory_limit') ?: 'unknown',
            'db_type'                  => DB::driverName(),
            'db_version'               => $this->databaseVersion(),
            'os_type'                  => php_uname('s'),
            'webtrees_version'         => Webtrees::VERSION,
            'default_language'         => Site::getPreference('LANGUAGE'),
            'changes_count'            => $this->changesCount(),
            'trees'                    => $this->treesData(),
            'default_theme'            => Site::getPreference('THEME_DIR'),
            'users_count'              => $this->user_service->all()->count(),
            'user_settings'            => $this->userSettings(),
            'enabled_standard_modules' => $this->enabledStandardModules(),
            'custom_modules'           => $this->customModules(),
        ];
    }

    private function databaseVersion(): string
    {
        return DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    private function changesCount(): int
    {
        return (int) DB::table('change')
            ->count();
    }

    /**
     * @return array<int,array<string,int>>
     */
    private function treesData(): array
    {
        $trees = $this->tree_service->all();
        $data  = [];

        foreach ($trees as $tree) {
            $treeId = $tree->id();

            $roleCounts = DB::table('user_gedcom_setting')
                ->where('gedcom_id', '=', $treeId)
                ->where('setting_name', '=', UserInterface::PREF_TREE_ROLE)
                ->selectRaw('setting_value, COUNT(*) as total')
                ->groupBy('setting_value')
                ->pluck('total', 'setting_value')
                ->map(static fn (string $count): int => (int) $count)
                ->all();

            $data[] = [
                'individuals_count'      => (int) DB::table('individuals')->where('i_file', '=', $treeId)->count(),
                'families_count'         => (int) DB::table('families')->where('f_file', '=', $treeId)->count(),
                'sources_count'          => (int) DB::table('sources')->where('s_file', '=', $treeId)->count(),
                'repositories_count'     => (int) DB::table('other')->where('o_file', '=', $treeId)->where('o_type', '=', 'REPO')->count(),
                'media_count'            => (int) DB::table('media')->where('m_file', '=', $treeId)->count(),
                'notes_count'            => (int) DB::table('other')->where('o_file', '=', $treeId)->where('o_type', '=', 'NOTE')->count(),
                'places_count'           => (int) DB::table('places')->where('p_file', '=', $treeId)->count(),
                'user_permissions_count' => $roleCounts,
            ];
        }

        return $data;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function userSettings(): array
    {
        $users    = $this->user_service->all();
        $now      = time();
        $settings = [];

        foreach ($users as $user) {
            $registeredTimestamp = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);
            $lastLoginTimestamp  = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_ACTIVE);

            $settings[] = [
                'language'            => $user->getPreference(UserInterface::PREF_LANGUAGE),
                'last_login_age_days' => $lastLoginTimestamp > 0 ? (int) (($now - $lastLoginTimestamp) / 86400) : -1,
                'account_age_days'    => $registeredTimestamp > 0 ? (int) (($now - $registeredTimestamp) / 86400) : -1,
            ];
        }

        return $settings;
    }

    /**
     * @return array<int,string>
     */
    private function enabledStandardModules(): array
    {
        return $this->module_service->all()
            ->filter(static fn (ModuleInterface $module): bool => !$module instanceof ModuleCustomInterface)
            ->map(static fn (ModuleInterface $module): string => $module->name())
            ->values()
            ->all();
    }

    /**
     * @return array<int,string>
     */
    private function customModules(): array
    {
        return $this->module_service->findByInterface(ModuleCustomInterface::class)
            ->map(static fn (ModuleCustomInterface $module): string => $module::class)
            ->values()
            ->all();
    }
}
