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

namespace Fisharebest\Webtrees\Schema;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 42 to version 43.
 */
class Migration42 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {        
        //apparently not possible to change enum column directly via laravel
        $data = DB::table('module_privacy')->get();
        DB::schema()->drop('module_privacy');

        DB::schema()->create('module_privacy', function (Blueprint $table): void {
            $table->string('module_name', 32);
            $table->integer('gedcom_id');
            $table->enum('component', ['block', 'chart', 'list', 'menu', 'report', 'sidebar', 'tab', 'theme']);
            $table->tinyInteger('access_level');

            $table->primary(['module_name', 'gedcom_id', 'component']);
            $table->unique(['gedcom_id', 'module_name', 'component']);

            $table->foreign('module_name')->references('module_name')->on('module');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });
        
        $rows = $data->toArray();
        foreach ($rows as $row) {
          DB::table('module_privacy')->insert([
              'module_name' => $row->module_name,
              'gedcom_id'   => $row->gedcom_id,
              'component'   => $row->component,
              'access_level'   => $row->access_level,
          ]);
        }
    }
}
