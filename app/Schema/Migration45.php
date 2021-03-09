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

namespace Fisharebest\Webtrees\Schema;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;
use PDOException;

/**
 * Upgrade the database schema from version 45 to version 46.
 */
class Migration45 implements MigrationInterface
{
    /**
     * Upgrade to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        // transfer site setting to place hierarchy module
        $use_maps = DB::table('site_setting')
            ->where('setting_name', '=', 'map-provider')
            ->pluck('setting_value')
            ->first();

        DB::table('module_setting')
            ->insert([
                'module_name'   => 'places_list',
                'setting_name'  => 'hierarchy_map',
                'setting_value' => $use_maps === 'osm' ? 1 : 0,
            ]);

        DB::table('site_setting')
            ->where('setting_name', '=', 'map-provider')
            ->delete();

        // Add tables and data for Openstreetmap
        if (!DB::schema()->hasTable('map_names')) {
            DB::schema()->create('map_names', static function (Blueprint $table): void {
                $table->increments('id');
                $table->integer('provider_id')->nullable()->unsigned();
                $table->string('key_name', 32);
                $table->string('display_name', 32);
                $table->index(['id', 'key_name']);
                $table->index(['provider_id', 'key_name']);
            });

            DB::schema()->table('map_names', static function (Blueprint $table): void {
                $table->foreign(['provider_id'])
                    ->references(['id'])
                    ->on('map_names')
                    ->onDelete('CASCADE')
                    ->onUpdate('NO ACTION');
            });

            DB::table('map_names')
                ->insert([
                    ['provider_id' => null, 'key_name' => 'openstreetmap', 'display_name' => 'OpenStreetMap™'],
                    ['provider_id' => 1, 'key_name' => 'deutsch', 'display_name' => 'Deutsch'],
                    ['provider_id' => 1, 'key_name' => 'francais', 'display_name' => 'Français'],
                    ['provider_id' => 1, 'key_name' => 'mapnik', 'display_name' => 'Mapnik'],
                ]);
        }


        if (!DB::schema()->hasTable('map_parameters')) {
            DB::schema()->create('map_parameters', static function (Blueprint $table): void {
                $table->integer('parent_id')->unsigned();
                $table->enum('type', ['user', 'common', 'style']);
                $table->string('parameter_name', 32);
                $table->string('parameter_value', 256);
                $table->primary(['parent_id', 'parameter_name']);
            });

            DB::schema()->table('map_parameters', static function (Blueprint $table): void {
                $table->foreign(['parent_id'])
                    ->references(['id'])
                    ->on('map_names')
                    ->onDelete('CASCADE')
                    ->onUpdate('NO ACTION');
            });

            DB::table('map_parameters')
                ->insert([
                    ['parent_id' => 2, 'type' => 'style', 'parameter_name' => 'attribution', 'parameter_value' => 's:187:"Map data &copy; <a href="https://www.openstreetmap.org/">Karte hergestellt aus OpenStreetMap-Daten</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>";'],
                    ['parent_id' => 2, 'type' => 'style', 'parameter_name' => 'max_zoom', 'parameter_value' => 'i:18;'],
                    ['parent_id' => 2, 'type' => 'style', 'parameter_name' => 'url', 'parameter_value' => 's:61:"https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png";'],
                    ['parent_id' => 3, 'type' => 'style', 'parameter_name' => 'attribution', 'parameter_value' => 's:243:"<a href="https://www.openstreetmap.fr/association">&copy;Openstreetmap France</a> | Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>";'],
                    ['parent_id' => 3, 'type' => 'style', 'parameter_name' => 'max_zoom', 'parameter_value' => 'i:20;'],
                    ['parent_id' => 3, 'type' => 'style', 'parameter_name' => 'url', 'parameter_value' => 's:55:"https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png";'],
                    ['parent_id' => 4, 'type' => 'style', 'parameter_name' => 'attribution', 'parameter_value' => 's:159:"Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>";'],
                    ['parent_id' => 4, 'type' => 'style', 'parameter_name' => 'max_zoom', 'parameter_value' => 'i:19;'],
                    ['parent_id' => 4, 'type' => 'style', 'parameter_name' => 'url', 'parameter_value' => 's:50:"https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";'],
                ]);
        }
    }
}
