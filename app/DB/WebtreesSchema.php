<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2024 webtrees development team
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

namespace Fisharebest\Webtrees\DB;

use Doctrine\DBAL\Schema\Schema;
use Fisharebest\Webtrees\DB;

/**
 * Definitions for the webtrees database.
 */
class WebtreesSchema
{
    /**
     * @return void
     */
    public function historicSchemaVersions(): void
    {
        switch ('webtrees_schema') {
            case 1: // webtrees 1.0.0 - 1.0.3
            case 2: // webtrees 1.0.4
            case 3:
            case 4: // webtrees 1.0.5
            case 5: // webtrees 1.0.6
            case 6:
            case 7:
            case 8:
            case 9: // webtrees 1.1.0 - 1.1.1
            case 10: // webtrees 1.1.2
            case 11: // webtrees 1.2.0
            case 12: // webtrees 1.2.1 - 1.2.3
            case 13:
            case 14:
            case 15: // webtrees 1.2.4 - 1.2.5
            case 16: // webtrees 1.2.7
            case 17:
            case 18: // webtrees 1.3.0
            case 19: // webtrees 1.3.1
            case 20: // webtrees 1.3.2
            case 21:
            case 22:
            case 23: // webtrees 1.4.0 - 1.4.1
            case 24:
            case 25: // webtrees 1.4.2 - 1.4.4, 1.5.0
            case 26: // webtrees 1.4.5 - 1.4.6
            case 27: // webtrees 1.5.1 - 1.6.0
            case 28:
            case 29: // webtrees 1.6.1 - 1.6.2
            case 30:
            case 31: // webtrees 1.7.0 - 1.7.1
            case 32: // webtrees 1.7.2
            case 33:
            case 34: // webtrees 1.7.3 - 1.7.4
            case 35:
            case 36: // webtrees 1.7.5 - 1.7.7
            case 37: // webtrees 1.7.8 - 2.0.0
            case 38:
            case 39:
            case 40: // webtrees 2.0.1 - 2.1.15
        }
    }

    public static function tableBlock(): Table
    {
        return new Table(
            'block',
            DB::integer(name: 'block_id')->autoincrement(),
            DB::integer(name: 'gedcom_id')->nullable(),
            DB::integer(name: 'user_id')->nullable(),
            DB::varchar(name: 'xref', length: 20)->nullable(),
            DB::char(name: 'location', length: 4)->nullable(),
            DB::integer(name: 'block_order'),
            DB::varchar(name: 'module_name', length: 32),
            DB::primaryKey(columns: ['block_id']),
            DB::index(columns: ['gedcom_id']),
            DB::index(columns: ['user_id']),
            DB::index(columns: ['module_name']),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['module_name'], foreign_table: 'module')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableBlockSetting(): Table
    {
        return new Table(
            'block_setting',
            DB::integer(name: 'block_id'),
            DB::varchar(name: 'setting_name', length: 32),
            DB::text('setting_value'),
            DB::primaryKey(columns: ['block_id', 'setting_name']),
            DB::foreignKey(local_columns: ['block_id'], foreign_table: 'block')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableChange(): Table
    {
        return new Table(
            'change',
            DB::integer(name: 'change_id')->autoincrement(),
            DB::timestamp(name: 'change_time')->default(default: 'CURRENT_TIMESTAMP'),
            DB::char(name: 'status', length: 8),
            DB::integer(name: 'gedcom_id'),
            DB::varchar(name: 'xref', length: 20),
            DB::text(name: 'old_gedcom'),
            DB::text(name: 'new_gedcom'),
            DB::integer(name: 'user_id'),
            DB::primaryKey(columns: ['change_id']),
            DB::index(columns: ['gedcom_id', 'status', 'xref']),
            DB::index(columns: ['user_id']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableDates(): Table
    {
        return new Table(
            'dates',
            DB::integer(name: 'd_day'),
            DB::char(name: 'd_month', length: 5),
            DB::integer(name: 'd_mon'),
            DB::integer(name: 'd_year'),
            DB::integer(name: 'd_julianday1'),
            DB::integer(name: 'd_julianday2'),
            DB::varchar(name: 'd_fact', length: 15),
            DB::varchar(name: 'd_gid', length: 20),
            DB::integer(name: 'd_file'),
            DB::varchar(name: 'd_type', length: 13),
            DB::index(columns: ['d_day']),
            DB::index(columns: ['d_month']),
            DB::index(columns: ['d_mon']),
            DB::index(columns: ['d_year']),
            DB::index(columns: ['d_julianday1']),
            DB::index(columns: ['d_julianday2']),
            DB::index(columns: ['d_gid']),
            DB::index(columns: ['d_file']),
            DB::index(columns: ['d_type']),
            DB::index(columns: ['d_fact', 'd_gid']),
            DB::foreignKey(local_columns: ['d_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableDefaultResn(): Table
    {
        return new Table(
            'default_resn',
            DB::integer(name: 'default_resn_id')->autoincrement(),
            DB::integer(name: 'gedcom_id'),
            DB::varchar(name: 'xref', length: 20)->nullable(),
            DB::varchar(name: 'tag_type', length: 15)->nullable(),
            DB::varchar(name: 'resn', length: 12),
            DB::primaryKey(columns: ['default_resn_id']),
            DB::uniqueIndex(columns: ['gedcom_id', 'xref', 'tag_type']),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableFamilies(): Table
    {
        return new Table(
            'families',
            DB::varchar(name: 'f_id', length: 20),
            DB::integer(name: 'f_file'),
            DB::varchar(name: 'f_husb', length: 20)->nullable(),
            DB::varchar(name: 'f_wife', length: 20)->nullable(),
            DB::text(name: 'f_gedcom'),
            DB::integer(name: 'f_numchil'),
            DB::primaryKey(columns: ['f_file', 'f_id']),
            DB::uniqueIndex(columns: ['f_id', 'f_file']),
            DB::index(columns: ['f_file', 'f_husb']),
            DB::index(columns: ['f_file', 'f_wife']),
            DB::index(columns: ['f_file', 'f_numchil']),
            DB::foreignKey(local_columns: ['f_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableFavorite(): Table
    {
        return new Table(
            'favorite',
            DB::integer(name: 'favorite_id')->autoincrement(),
            DB::integer(name: 'user_id')->nullable(),
            DB::integer(name: 'gedcom_id'),
            DB::varchar(name: 'xref', length: 20)->nullable(),
            DB::char(name: 'favorite_type', length: 4),
            DB::varchar(name: 'url', length: 255)->nullable(),
            DB::varchar(name: 'title', length: 255)->nullable(),
            DB::varchar(name: 'note', length: 1000)->nullable(),
            DB::primaryKey(columns: ['favorite_id']),
            DB::index(columns: ['user_id']),
            DB::index(columns: ['gedcom_id', 'user_id']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableFile(): Table
    {
        return new Table(
            'file',
            DB::nvarchar(name: 'name', length: 255),
            DB::integer(name: 'size')->nullable(),
            DB::integer(name: 'last_modified')->nullable(),
            DB::varchar(name: 'mime_type', length: 255)->nullable(),
            DB::nvarchar(name: 'sha1', length: 40)->nullable(),
            DB::integer(name: 'file_exists')->nullable(),
            DB::primaryKey(['name']),
            DB::index(columns: ['sha1']),
            DB::index(columns: ['size']),
            DB::index(columns: ['mime_type']),
            DB::index(columns: ['last_modified']),
        );
    }

    public static function tableGedcom(): Table
    {
        return new Table(
            'gedcom',
            DB::integer(name: 'gedcom_id')->autoincrement(),
            DB::nvarchar(name: 'gedcom_name', length: 255),
            DB::integer(name: 'sort_order')->default(default: 0),
            DB::primaryKey(columns: ['gedcom_id']),
            DB::uniqueIndex(columns: ['gedcom_name']),
            DB::index(columns: ['sort_order']),
        );
    }

    public static function tableGedcomChunk(): Table
    {
        return new Table(
            'gedcom_chunk',
            DB::integer(name: 'gedcom_chunk_id')->autoincrement(),
            DB::integer(name: 'gedcom_id'),
            DB::text(name: 'chunk_data'),
            DB::integer(name: 'imported')->default(default: 0),
            DB::primaryKey(columns: ['gedcom_chunk_id']),
            DB::index(columns: ['gedcom_id', 'imported']),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableGedcomSetting(): Table
    {
        return new Table(
            'gedcom_setting',
            DB::integer('gedcom_id'),
            DB::varchar('setting_name', length: 32),
            DB::nvarchar('setting_value', length: 255),
            DB::primaryKey(columns: ['gedcom_id', 'setting_name']),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableHitCounter(): Table
    {
        return new Table(
            'hit_counter',
            DB::integer('gedcom_id'),
            DB::varchar('page_name', length: 32),
            DB::varchar('page_parameter', length: 32),
            DB::integer('page_count'),
            DB::primaryKey(columns: ['gedcom_id', 'page_name', 'page_parameter']),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableIndividuals(): Table
    {
        return new Table(
            'individuals',
            DB::varchar(name: 'i_id', length: 20),
            DB::integer(name: 'i_file'),
            DB::varchar(name: 'i_rin', length: 20),
            DB::char(name: 'i_sex', length: 1),
            DB::text(name: 'i_gedcom'),
            DB::primaryKey(columns: ['i_id', 'i_file']),
            DB::uniqueIndex(columns: ['i_file', 'i_id']),
            DB::index(columns: ['i_file', 'i_sex']),
            DB::foreignKey(local_columns: ['i_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableJob(): Table
    {
        return new Table(
            'job',
            DB::integer(name: 'job_id')->autoincrement(),
            DB::varchar(name: 'job_status', length: 15)->default('queued'),
            DB::integer(name: 'fail_count')->default(0),
            DB::timestamp(name: 'queued_at')->default(default: 'CURRENT_TIMESTAMP'),
            DB::timestamp(name: 'queued_at'),
            DB::primaryKey(columns: ['job_id']),
        );
    }

    public static function tableLink(): Table
    {
        return new Table(
            'link',
            DB::integer(name: 'l_file'),
            DB::varchar(name: 'l_from', length: 20),
            DB::varchar(name: 'l_type', length: 15),
            DB::varchar(name: 'l_to', length: 20),
            DB::primaryKey(columns: ['l_from', 'l_file', 'l_type', 'l_to']),
            DB::uniqueIndex(columns: ['l_to', 'l_file', 'l_type', 'l_from']),
            DB::foreignKey(local_columns: ['l_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableLog(): Table
    {
        return new Table(
            'log',
            DB::integer(name: 'log_id')->autoincrement(),
            DB::timestamp(name: 'log_time')->default(default: 'CURRENT_TIMESTAMP'),
            DB::varchar(name: 'log_type', length: 6),
            DB::text(name: 'log_message'),
            DB::varchar(name: 'ip_address', length: 45),
            DB::integer(name: 'user_id')->nullable(),
            DB::integer(name: 'gedcom_id')->nullable(),
            DB::primaryKey(columns: ['log_id']),
            DB::index(columns: ['gedcom_id']),
            DB::index(columns: ['user_id']),
            DB::index(columns: ['log_time']),
            DB::index(columns: ['log_type']),
            DB::index(columns: ['ip_address']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteSetNull()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteSetNull()->onUpdateCascade(),
        );
    }

    public static function tableMedia(): Table
    {
        return new Table(
            'media',
            DB::varchar(name: 'm_id', length: 20),
            DB::integer(name: 'm_file'),
            DB::text(name: 'm_gedcom'),
            DB::primaryKey(columns: ['m_file', 'm_id']),
            DB::uniqueIndex(columns: ['m_id', 'm_file']),
            DB::foreignKey(local_columns: ['m_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableMediaFile(): Table
    {
        return new Table(
            'media_file',
            DB::integer(name: 'id')->autoincrement(),
            DB::varchar(name: 'm_id', length: 20),
            DB::integer(name: 'm_file'),
            DB::nvarchar(name: 'multimedia_file_refn', length: 248),
            DB::nvarchar(name: 'multimedia_format', length: 4),
            DB::nvarchar(name: 'source_media_type', length: 15),
            DB::nvarchar(name: 'descriptive_title', length: 248),
            DB::primaryKey(columns: ['id']),
            DB::index(columns: ['m_id', 'm_file']),
            DB::index(columns: ['m_file', 'm_id']),
            DB::index(columns: ['m_file', 'multimedia_file_refn']),
            DB::index(columns: ['m_file', 'multimedia_format']),
            DB::index(columns: ['m_file', 'source_media_type']),
            DB::index(columns: ['m_file', 'descriptive_title']),
        );
    }

    public static function tableMessage(): Table
    {
        return new Table(
            'message',
            DB::integer(name: 'message_id')->autoincrement(),
            DB::nvarchar(name: 'sender', length: 64),
            DB::varchar(name: 'ip_address', length: 45),
            DB::integer(name: 'user_id'),
            DB::nvarchar(name: 'subject', length: 255),
            DB::text(name: 'body'),
            DB::timestamp(name: 'created')->default(default: 'CURRENT_TIMESTAMP'),
            DB::primaryKey(columns: ['message_id']),
            DB::index(columns: ['user_id']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableModule(): Table
    {
        return new Table(
            'module',
            DB::varchar(name: 'module_name', length: 32),
            DB::char(name: 'status', length: 8)->default(default: 'enabled'),
            DB::integer(name: 'tab_order')->nullable(),
            DB::integer(name: 'menu_order')->nullable(),
            DB::integer(name: 'sidebar_order')->nullable(),
            DB::integer(name: 'footer_order')->nullable(),
            DB::primaryKey(columns: ['module_name']),
        );
    }

    public static function tableModulePrivacy(): Table
    {
        return new Table(
            'module_privacy',
            DB::integer(name: 'id')->autoincrement(),
            DB::varchar(name: 'module_name', length: 32),
            DB::integer(name: 'gedcom_id'),
            DB::varchar(name: 'interface', length: 255),
            DB::integer(name: 'access_level'),
            DB::primaryKey(columns: ['id']),
            DB::uniqueIndex(columns: ['gedcom_id', 'module_name', 'interface']),
            DB::uniqueIndex(columns: ['module_name', 'gedcom_id', 'interface']),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['module_name'], foreign_table: 'module')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableModuleSetting(): Table
    {
        return new Table(
            'module_setting',
            DB::varchar(name: 'module_name', length: 32),
            DB::varchar(name: 'setting_name', length: 32),
            DB::text(name: 'setting_value'),
            DB::primaryKey(columns: ['module_name', 'setting_name']),
            DB::foreignKey(local_columns: ['module_name'], foreign_table: 'module')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableName(): Table
    {
        return new Table(
            'name',
            DB::integer(name: 'n_file'),
            DB::varchar(name: 'n_id', length: 20),
            DB::integer(name: 'n_num'),
            DB::varchar(name: 'n_type', length: 15),
            DB::nvarchar(name: 'n_sort', length: 255),
            DB::nvarchar(name: 'n_full', length: 255),
            DB::nvarchar(name: 'n_surname', length: 255)->nullable(),
            DB::nvarchar(name: 'n_surn', length: 255)->nullable(),
            DB::nvarchar(name: 'n_givn', length: 255)->nullable(),
            DB::varchar(name: 'n_soundex_givn_std', length: 255)->nullable(),
            DB::varchar(name: 'n_soundex_surn_std', length: 255)->nullable(),
            DB::varchar(name: 'n_soundex_givn_dm', length: 255)->nullable(),
            DB::varchar(name: 'n_soundex_surn_dm', length: 255)->nullable(),
            DB::primaryKey(columns: ['n_id', 'n_file', 'n_num']),
            DB::index(columns: ['n_full', 'n_id', 'n_file']),
            DB::index(columns: ['n_givn', 'n_file', 'n_type', 'n_id']),
            DB::index(columns: ['n_surn', 'n_file', 'n_type', 'n_id']),
            DB::foreignKey(local_columns: ['n_file', 'n_id'], foreign_table: 'individuals', foreign_columns: ['i_file', 'i_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableNews(): Table
    {
        return new Table(
            'news',
            DB::integer(name: 'news_id')->autoincrement(),
            DB::integer(name: 'user_id')->nullable(),
            DB::integer(name: 'gedcom_id')->nullable(),
            DB::nvarchar(name: 'subject', length: 255),
            DB::text(name: 'body'),
            DB::timestamp(name: 'updated')->default(default: 'CURRENT_TIMESTAMP'),
            DB::primaryKey(columns: ['news_id']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableOther(): Table
    {
        return new Table(
            'other',
            DB::varchar(name: 'o_id', length: 20),
            DB::integer(name: 'o_file'),
            DB::varchar(name: 'o_type', length: 15),
            DB::text(name: 'o_gedcom'),
            DB::primaryKey(columns: ['o_file', 'o_id']),
            DB::uniqueIndex(columns: ['o_id', 'o_file']),
            DB::foreignKey(local_columns: ['o_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tablePlaceLocation(): Table
    {
        return new Table(
            'place_location',
            DB::integer(name: 'id')->autoincrement(),
            DB::integer(name: 'parent_id')->nullable(),
            DB::nvarchar(name: 'place', length: 120),
            DB::float(name: 'latitude')->nullable(),
            DB::float(name: 'longitude')->nullable(),
            DB::primaryKey(columns: ['id']),
            DB::uniqueIndex(columns: ['parent_id', 'place']),
            DB::uniqueIndex(columns: ['place', 'parent_id']),
            DB::foreignKey(local_columns: ['parent_id'], foreign_table:  'place_location', foreign_columns: ['id']),
            DB::index(columns: ['latitude']),
            DB::index(columns: ['longitude']),
        );
    }

    public static function tablePlaceLinks(): Table
    {
        return new Table(
            'placelinks',
            DB::integer(name: 'pl_p_id'),
            DB::varchar(name: 'pl_gid', length: 20),
            DB::integer(name: 'pl_file'),
            DB::primaryKey(columns: ['pl_p_id', 'pl_gid', 'pl_file']),
            DB::index(columns: ['pl_p_id']),
            DB::index(columns: ['pl_gid']),
            DB::index(columns: ['pl_file']),
            DB::foreignKey(local_columns: ['pl_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tablePlaces(): Table
    {
        return new Table(
            'places',
            DB::integer(name: 'p_id')->autoincrement(),
            DB::nvarchar(name: 'p_place', length: 150),
            DB::integer(name: 'p_parent_id')->nullable(),
            DB::integer(name: 'p_file'),
            DB::text(name: 'p_std_soundex'),
            DB::text(name: 'p_dm_soundex'),
            DB::primaryKey(columns: ['p_id']),
            DB::uniqueIndex(columns: ['p_parent_id', 'p_file', 'p_place']),
            DB::foreignKey(local_columns: ['p_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableSession(): Table
    {
        return new Table(
            'session',
            DB::varchar(name: 'session_id', length: 32),
            DB::timestamp(name: 'session_time')->default(default: 'CURRENT_TIMESTAMP'),
            DB::integer(name: 'user_id')->nullable(),
            DB::varchar(name: 'ip_address', length: 45),
            DB::text(name: 'session_data'),
            DB::primaryKey(columns: ['session_id']),
            DB::index(columns: ['session_time']),
            DB::index(columns: ['user_id', 'ip_address']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableSiteSetting(): Table
    {
        return new Table(
            'site_setting',
            DB::varchar(name: 'setting_name', length: 32),
            DB::nvarchar(name: 'setting_value', length: 2000),
            DB::primaryKey(columns: ['setting_name']),
        );
    }

    public static function tableSources(): Table
    {
        return new Table(
            'sources',
            DB::varchar(name: 's_id', length: 20),
            DB::integer(name: 's_file'),
            DB::nvarchar(name: 's_name', length: 255),
            DB::text(name: 's_gedcom'),
            DB::primaryKey(columns: ['s_file', 's_id']),
            DB::uniqueIndex(columns: ['s_id', 's_file']),
            DB::index(columns: ['s_file', 's_name']),
            DB::foreignKey(local_columns: ['s_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableUser(): Table
    {
        return new Table(
            'user',
            DB::integer('user_id')->autoincrement(),
            DB::nvarchar('user_name', length: 32),
            DB::nvarchar('real_name', length: 64),
            DB::nvarchar('email', length: 64),
            DB::varchar('password', length: 128),
            DB::primaryKey(columns: ['user_id']),
            DB::uniqueIndex(columns: ['user_name']),
            DB::uniqueIndex(columns: ['email']),
        );
    }

    public static function tableUserGedcomSetting(): Table
    {
        return new Table(
            'user_gedcom_setting',
            DB::integer(name: 'user_id'),
            DB::integer(name: 'gedcom_id'),
            DB::varchar(name: 'setting_name', length: 32),
            DB::nvarchar(name: 'setting_value', length: 255),
            DB::primaryKey(columns: ['user_id', 'gedcom_id', 'setting_name']),
            DB::index(columns: ['gedcom_id']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
            DB::foreignKey(local_columns: ['gedcom_id'], foreign_table: 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function tableUserSetting(): Table
    {
        return new Table(
            'user_setting',
            DB::integer(name: 'user_id'),
            DB::varchar(name: 'setting_name', length: 32),
            DB::nvarchar(name: 'setting_value', length: 255),
            DB::primaryKey(columns: ['user_id', 'setting_name']),
            DB::foreignKey(local_columns: ['user_id'], foreign_table: 'user')->onDeleteCascade()->onUpdateCascade(),
        );
    }

    public static function schema(): Schema
    {
        return new Schema(
            [
                self::tableBlock(),
                self::tableBlockSetting(),
                self::tableChange(),
                self::tableDates(),
                self::tableDefaultResn(),
                self::tableFamilies(),
                self::tableFavorite(),
                //self::tableFile(),
                self::tableGedcom(),
                self::tableGedcomChunk(),
                self::tableGedcomSetting(),
                self::tableHitCounter(),
                self::tableIndividuals(),
                //self::tableJob(),
                self::tableLink(),
                self::tableLog(),
                self::tableMedia(),
                self::tableMediaFile(),
                self::tableMessage(),
                self::tableModule(),
                self::tableModulePrivacy(),
                self::tableModuleSetting(),
                self::tableName(),
                self::tableNews(),
                self::tableOther(),
                self::tablePlaceLocation(),
                self::tablePlaceLinks(),
                self::tablePlaces(),
                self::tableSession(),
                self::tableSiteSetting(),
                self::tableSources(),
                self::tableUser(),
                self::tableUserGedcomSetting(),
                self::tableUserSetting(),
            ],
        );
    }
}
