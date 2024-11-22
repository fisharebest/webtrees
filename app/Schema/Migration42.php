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

namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 42 to version 43.
 */
class Migration42 implements MigrationInterface
{
    private const array COMPONENT_TO_INTERFACE = [
        'block'   => ModuleBlockInterface::class,
        'chart'   => ModuleChartInterface::class,
        'list'    => ModuleListInterface::class,
        'menu'    => ModuleMenuInterface::class,
        'report'  => ModuleReportInterface::class,
        'sidebar' => ModuleSidebarInterface::class,
        'tab'     => ModuleTabInterface::class,
        'theme'   => ModuleThemeInterface::class,
    ];

    public function upgrade(): void
    {
        // doctrine/dbal cannot modify tables containing ENUM fields
        $data = DB::table('module_privacy')->get();

        DB::schema()->drop('module_privacy');

        DB::schema()->create('module_privacy', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('module_name', 32);
            $table->integer('gedcom_id');
            $table->string('interface');
            $table->tinyInteger('access_level');

            // Default constraint names are too long for MySQL.
            $key1 = DB::prefix($table->getTable() . '_ix1');
            $key2 = DB::prefix($table->getTable() . '_ix2');

            $table->unique(['gedcom_id', 'module_name', 'interface'], $key1);
            $table->unique(['module_name', 'gedcom_id', 'interface'], $key2);

            $table->foreign('module_name')->references('module_name')->on('module')->onDelete('cascade');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom')->onDelete('cascade');
        });

        foreach ($data as $datum) {
            DB::table('module_privacy')->insert([
                'module_name'  => $datum->module_name,
                'gedcom_id'    => $datum->gedcom_id,
                'interface'    => self::COMPONENT_TO_INTERFACE[$datum->component],
                'access_level' => $datum->access_level,
            ]);
        }
    }
}
