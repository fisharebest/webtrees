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

namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Upgrade the database schema from version 0 (empty database) to version 1.
 */
class Migration0 implements MigrationInterface
{
    public function upgrade(): void
    {
        DB::schema()->create('gedcom', static function (Blueprint $table): void {
            $table->integer('gedcom_id', true);
            $table->string('gedcom_name', 255);
            $table->integer('sort_order')->default(0);

            $table->unique('gedcom_name');
            $table->index('sort_order');
        });

        DB::schema()->create('site_setting', static function (Blueprint $table): void {
            $table->string('setting_name', 32);
            $table->string('setting_value', 2000);

            $table->primary('setting_name');
        });

        DB::schema()->create('gedcom_setting', static function (Blueprint $table): void {
            $table->integer('gedcom_id');
            $table->string('setting_name', 32);
            $table->string('setting_value', 255);

            $table->primary(['gedcom_id', 'setting_name']);

            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('user', static function (Blueprint $table): void {
            $table->integer('user_id', true);
            $table->string('user_name', 32);
            $table->string('real_name', 64);
            $table->string('email', 64);
            $table->string('password', 128);

            $table->unique('user_name');
            $table->unique('email');
        });

        DB::schema()->create('user_setting', static function (Blueprint $table): void {
            $table->integer('user_id');
            $table->string('setting_name', 32);
            $table->string('setting_value', 255);

            $table->primary(['user_id', 'setting_name']);

            $table->foreign('user_id')->references('user_id')->on('user');
        });

        DB::schema()->create('user_gedcom_setting', static function (Blueprint $table): void {
            $table->integer('user_id');
            $table->integer('gedcom_id');
            $table->string('setting_name', 32);
            $table->string('setting_value', 255);

            // Default constraint names are too long for MySQL.
            $key = DB::prefix($table->getTable() . '_primary');

            $table->primary(['user_id', 'gedcom_id', 'setting_name'], $key);
            $table->index('gedcom_id');

            $table->foreign('user_id')->references('user_id')->on('user');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('log', static function (Blueprint $table): void {
            $table->integer('log_id', true);
            $table->timestamp('log_time')->useCurrent();
            $table->enum('log_type', ['auth', 'config', 'debug', 'edit', 'error', 'media', 'search']);
            $table->longText('log_message');
            $table->ipAddress('ip_address');
            $table->integer('user_id')->nullable();
            $table->integer('gedcom_id')->nullable();

            $table->index('log_time');
            $table->index('log_type');
            $table->index('ip_address');
            $table->index('user_id');
            $table->index('gedcom_id');

            $table->foreign('user_id')->references('user_id')->on('user');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('change', static function (Blueprint $table): void {
            $table->integer('change_id', true);
            $table->timestamp('change_time')->useCurrent();
            $table->enum('status', ['accepted', 'pending', 'rejected'])->default('pending');
            $table->integer('gedcom_id');
            $table->string('xref', 20);
            $table->longText('old_gedcom');
            $table->longText('new_gedcom');
            $table->integer('user_id');

            $table->index(['gedcom_id', 'status', 'xref']);
            $table->index('user_id');

            $table->foreign('user_id')->references('user_id')->on('user');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('message', static function (Blueprint $table): void {
            $table->integer('message_id', true);
            $table->string('sender', 64);
            $table->ipAddress('ip_address');
            $table->integer('user_id');
            $table->string('subject', 255);
            $table->longText('body');
            $table->timestamp('created')->useCurrent();

            $table->index('user_id');

            $table->foreign('user_id')->references('user_id')->on('user');
        });

        DB::schema()->create('default_resn', static function (Blueprint $table): void {
            $table->integer('default_resn_id', true);
            $table->integer('gedcom_id');
            $table->string('xref', 20)->nullable();
            $table->string('tag_type', 15)->nullable();
            $table->enum('resn', ['none', 'privacy', 'confidential', 'hidden']);
            $table->string('comment', 255)->nullable();
            $table->timestamp('updated')->useCurrent();

            $table->unique(['gedcom_id', 'xref', 'tag_type']);

            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('individuals', static function (Blueprint $table): void {
            $table->string('i_id', 20);
            $table->integer('i_file');
            $table->string('i_rin', 20);
            $table->enum('i_sex', ['U', 'M', 'F']);
            $table->longText('i_gedcom');

            $table->primary(['i_id', 'i_file']);
            $table->unique(['i_file', 'i_id']);
        });

        DB::schema()->create('families', static function (Blueprint $table): void {
            $table->string('f_id', 20);
            $table->integer('f_file');
            $table->string('f_husb', 20)->nullable();
            $table->string('f_wife', 20)->nullable();
            $table->longText('f_gedcom');
            $table->integer('f_numchil');

            $table->primary(['f_id', 'f_file']);
            $table->unique(['f_file', 'f_id']);
            $table->index('f_husb');
            $table->index('f_wife');
        });

        DB::schema()->create('places', static function (Blueprint $table): void {
            $table->integer('p_id', true);
            $table->string('p_place', 150);
            $table->integer('p_parent_id')->nullable();
            $table->integer('p_file');
            $table->longText('p_std_soundex')->nullable();
            $table->longText('p_dm_soundex')->nullable();

            $table->index(['p_file', 'p_place']);
            $table->unique(['p_parent_id', 'p_file', 'p_place']);
        });

        DB::schema()->create('placelinks', static function (Blueprint $table): void {
            $table->integer('pl_p_id');
            $table->string('pl_gid', 20);
            $table->integer('pl_file');

            $table->primary(['pl_p_id', 'pl_gid', 'pl_file']);
            $table->index('pl_p_id');
            $table->index('pl_gid');
            $table->index('pl_file');
        });

        DB::schema()->create('dates', static function (Blueprint $table): void {
            $table->tinyInteger('d_day');
            $table->char('d_month', 5)->nullable();
            $table->tinyInteger('d_mon');
            $table->smallInteger('d_year');
            $table->mediumInteger('d_julianday1');
            $table->mediumInteger('d_julianday2');
            $table->string('d_fact', 15);
            $table->string('d_gid', 20);
            $table->integer('d_file');
            $table->enum('d_type', ['@#DGREGORIAN@', '@#DJULIAN@', '@#DHEBREW@', '@#DFRENCH R@', '@#DHIJRI@', '@#DROMAN@', '@#DJALALI@']);

            $table->index('d_day');
            $table->index('d_month');
            $table->index('d_mon');
            $table->index('d_year');
            $table->index('d_julianday1');
            $table->index('d_julianday2');
            $table->index('d_gid');
            $table->index('d_file');
            $table->index('d_type');
            $table->index(['d_fact', 'd_gid']);
        });

        DB::schema()->create('media', static function (Blueprint $table): void {
            $table->string('m_id', 20);
            $table->string('m_ext', 6)->nullable();
            $table->string('m_type', 20)->nullable();
            $table->string('m_titl', 248)->nullable();
            $table->string('m_filename', 248)->nullable();
            $table->integer('m_file');
            $table->longText('m_gedcom')->nullable();

            $table->primary(['m_file', 'm_id']);
            $table->unique(['m_id', 'm_file']);
            // Originally, this migration created an index on m_ext and m_type,
            // but we drop those columns in migration 37.
        });

        DB::schema()->create('next_id', static function (Blueprint $table): void {
            $table->integer('gedcom_id');
            $table->string('record_type', 15);
            $table->decimal('next_id', 20, 0);

            $table->primary(['gedcom_id', 'record_type']);

            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('other', static function (Blueprint $table): void {
            $table->string('o_id', 20);
            $table->integer('o_file');
            $table->string('o_type', 15);
            $table->longText('o_gedcom');

            $table->primary(['o_id', 'o_file']);
            $table->unique(['o_file', 'o_id']);
        });

        DB::schema()->create('sources', static function (Blueprint $table): void {
            $table->string('s_id', 20);
            $table->integer('s_file');
            $table->string('s_name', 255);
            $table->longText('s_gedcom');

            $table->primary(['s_id', 's_file']);
            $table->unique(['s_file', 's_id']);
            $table->index('s_name');
        });

        DB::schema()->create('link', static function (Blueprint $table): void {
            $table->integer('l_file');
            $table->string('l_from', 20);
            $table->string('l_type', 15);
            $table->string('l_to', 20);

            $table->primary(['l_from', 'l_file', 'l_type', 'l_to']);
            $table->unique(['l_to', 'l_file', 'l_type', 'l_from']);
        });

        DB::schema()->create('name', static function (Blueprint $table): void {
            $table->integer('n_file');
            $table->string('n_id', 20);
            $table->integer('n_num');
            $table->string('n_type', 15);
            $table->string('n_sort', 255);
            $table->string('n_full', 255);
            $table->string('n_surname', 255)->nullable();
            $table->string('n_surn', 255)->nullable();
            $table->string('n_givn', 255)->nullable();
            $table->string('n_soundex_givn_std', 255)->nullable();
            $table->string('n_soundex_surn_std', 255)->nullable();
            $table->string('n_soundex_givn_dm', 255)->nullable();
            $table->string('n_soundex_surn_dm', 255)->nullable();

            $table->primary(['n_id', 'n_file', 'n_num']);
            $table->index(['n_full', 'n_id', 'n_file']);
            $table->index(['n_surn', 'n_file', 'n_type', 'n_id']);
            $table->index(['n_givn', 'n_file', 'n_type', 'n_id']);
        });

        DB::schema()->create('module', static function (Blueprint $table): void {
            $table->string('module_name', 32);
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            $table->integer('tab_order')->nullable();
            $table->integer('menu_order')->nullable();
            $table->integer('sidebar_order')->nullable();

            $table->primary('module_name');
        });

        DB::schema()->create('module_setting', static function (Blueprint $table): void {
            $table->string('module_name', 32);
            $table->string('setting_name', 32);
            $table->longText('setting_value');

            $table->primary(['module_name', 'setting_name']);

            $table->foreign('module_name')->references('module_name')->on('module');
        });

        DB::schema()->create('module_privacy', static function (Blueprint $table): void {
            $table->string('module_name', 32);
            $table->integer('gedcom_id');
            $table->enum('component', ['block', 'chart', 'menu', 'report', 'sidebar', 'tab', 'theme']);
            $table->tinyInteger('access_level');

            // Default constraint names are too long for MySQL.
            $key0 = DB::prefix($table->getTable() . '_primary');
            $key1 = DB::prefix($table->getTable() . '_ix1');

            $table->primary(['module_name', 'gedcom_id', 'component'], $key0);
            $table->unique(['gedcom_id', 'module_name', 'component'], $key1);

            $table->foreign('module_name')->references('module_name')->on('module');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('block', static function (Blueprint $table): void {
            $table->integer('block_id', true);
            $table->integer('gedcom_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('xref', 20)->nullable();
            $table->enum('location', ['main', 'side'])->nullable();
            $table->integer('block_order');
            $table->string('module_name', 32);

            $table->index('module_name');
            $table->index('gedcom_id');
            $table->index('user_id');

            $table->foreign('module_name')->references('module_name')->on('module');
            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
            $table->foreign('user_id')->references('user_id')->on('user');
        });

        DB::schema()->create('block_setting', static function (Blueprint $table): void {
            $table->integer('block_id');
            $table->string('setting_name', 32);
            $table->longText('setting_value');

            $table->primary(['block_id', 'setting_name']);

            $table->foreign('block_id')->references('block_id')->on('block');
        });

        DB::schema()->create('hit_counter', static function (Blueprint $table): void {
            $table->integer('gedcom_id');
            $table->string('page_name', 32);
            $table->string('page_parameter', 32);
            $table->integer('page_count');

            // Default constraint names are too long for MySQL.
            $key = DB::prefix($table->getTable() . '_primary');

            $table->primary(['gedcom_id', 'page_name', 'page_parameter'], $key);

            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });

        DB::schema()->create('session', static function (Blueprint $table): void {
            $table->string('session_id', 256);
            $table->timestamp('session_time')->useCurrent();
            $table->integer('user_id');
            $table->ipAddress('ip_address');
            $table->longText('session_data');

            $table->primary('session_id');
            $table->index('session_time');
            $table->index(['user_id', 'ip_address']);
        });

        DB::schema()->create('gedcom_chunk', static function (Blueprint $table): void {
            $table->integer('gedcom_chunk_id', true);
            $table->integer('gedcom_id');
            $table->longText('chunk_data');
            $table->boolean('imported')->default(0);

            $table->index(['gedcom_id', 'imported']);

            $table->foreign('gedcom_id')->references('gedcom_id')->on('gedcom');
        });
    }
}
