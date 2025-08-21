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

namespace Fisharebest\Webtrees\DB;

use Doctrine\DBAL\Schema\ForeignKeyConstraint\ReferentialAction;
use Doctrine\DBAL\Schema\Table;
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
        return Table::editor()
            ->setUnquotedName(DB::prefix('block'))
            ->addColumn(DB::integer(name: 'block_id', autoincrement: true))
            ->addColumn(DB::integer(name: 'gedcom_id', nullable: true))
            ->addColumn(DB::integer(name: 'user_id', nullable: true))
            ->addColumn(DB::varchar(name: 'xref', length: 20, nullable: true))
            ->addColumn(DB::char(name: 'location', length: 4, nullable: true))
            ->addColumn(DB::integer(name: 'block_order'))
            ->addColumn(DB::varchar(name: 'module_name', length: 32))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['block_id']))
            ->addIndex(DB::index(name: 'block_ix1', columns: ['gedcom_id']))
            ->addIndex(DB::index(name: 'block_ix2', columns: ['user_id']))
            ->addIndex(DB::index(name: 'block_ix3', columns: ['module_name']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'block_fk1', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'block_fk2', local_columns: ['module_name'], foreign_table: 'module', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'block_fk3', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableBlockSetting(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('block_setting'))
            ->addColumn(DB::integer(name: 'block_id'))
            ->addColumn(DB::varchar(name: 'setting_name', length: 32))
            ->addColumn(DB::text('setting_value'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['block_id', 'setting_name']))
            // block_ix1 is not needed (it's a leading part of the PK), but doctrine/dbal requires it
            ->addIndex(DB::Index(name: 'block_ix1', columns: ['block_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'block_setting_fk1', local_columns: ['block_id'], foreign_table: 'block', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableChange(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('change'))
            ->addColumn(DB::integer(name: 'change_id', autoincrement: true))
            ->addColumn(DB::timestamp(name: 'change_time', default: 'CURRENT_TIMESTAMP'))
            ->addColumn(DB::char(name: 'status', length: 8))
            ->addColumn(DB::integer(name: 'gedcom_id'))
            ->addColumn(DB::varchar(name: 'xref', length: 20))
            ->addColumn(DB::text(name: 'old_gedcom'))
            ->addColumn(DB::text(name: 'new_gedcom'))
            ->addColumn(DB::integer(name: 'user_id'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['change_id']))
            ->addIndex(DB::index(name: 'change_ix1', columns: ['gedcom_id', 'status', 'xref']))
            ->addIndex(DB::index(name: 'change_ix2', columns: ['user_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'change_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'change_fk2', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableDates(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('dates'))
            ->addColumn(DB::integer(name: 'd_day'))
            ->addColumn(DB::char(name: 'd_month', length: 5))
            ->addColumn(DB::integer(name: 'd_mon'))
            ->addColumn(DB::integer(name: 'd_year'))
            ->addColumn(DB::integer(name: 'd_julianday1'))
            ->addColumn(DB::integer(name: 'd_julianday2'))
            ->addColumn(DB::varchar(name: 'd_fact', length: 15))
            ->addColumn(DB::varchar(name: 'd_gid', length: 20))
            ->addColumn(DB::integer(name: 'd_file'))
            ->addColumn(DB::varchar(name: 'd_type', length: 13))
            ->addIndex(DB::index(name: 'dates_ix1', columns: ['d_day']))
            ->addIndex(DB::index(name: 'dates_ix2', columns: ['d_month']))
            ->addIndex(DB::index(name: 'dates_ix3', columns: ['d_mon']))
            ->addIndex(DB::index(name: 'dates_ix4', columns: ['d_year']))
            ->addIndex(DB::index(name: 'dates_ix5', columns: ['d_julianday1']))
            ->addIndex(DB::index(name: 'dates_ix6', columns: ['d_julianday2']))
            ->addIndex(DB::index(name: 'dates_ix7', columns: ['d_gid']))
            ->addIndex(DB::index(name: 'dates_ix8', columns: ['d_file']))
            ->addIndex(DB::index(name: 'dates_ix9', columns: ['d_type']))
            ->addIndex(DB::index(name: 'dates_ix10', columns: ['d_fact', 'd_gid']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'dates_fk1', local_columns: ['d_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableDefaultResn(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('default_resn'))
            ->addColumn(DB::integer(name: 'default_resn_id', autoincrement: true))
            ->addColumn(DB::integer(name: 'gedcom_id'))
            ->addColumn(DB::varchar(name: 'xref', length: 20, nullable: true))
            ->addColumn(DB::varchar(name: 'tag_type', length: 15, nullable: true))
            ->addColumn(DB::varchar(name: 'resn', length: 12))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['default_resn_id']))
            ->addIndex(DB::uniqueIndex(name: 'default_resn_ux1', columns: ['gedcom_id', 'xref', 'tag_type']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'default_resn_fk1', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableFamilies(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('families'))
            ->addColumn(DB::varchar(name: 'f_id', length: 20))
            ->addColumn(DB::integer(name: 'f_file'))
            ->addColumn(DB::varchar(name: 'f_husb', length: 20, nullable: true))
            ->addColumn(DB::varchar(name: 'f_wife', length: 20, nullable: true))
            ->addColumn(DB::text(name: 'f_gedcom'))
            ->addColumn(DB::integer(name: 'f_numchil'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['f_file', 'f_id']))
            ->addIndex(DB::uniqueIndex(name: 'families_ux1', columns: ['f_id', 'f_file']))
            ->addIndex(DB::index(name: 'families_ix1', columns: ['f_file', 'f_husb']))
            ->addIndex(DB::index(name: 'families_ix2', columns: ['f_file', 'f_wife']))
            ->addIndex(DB::index(name: 'families_ix3', columns: ['f_file', 'f_numchil']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'families_fk1', local_columns: ['f_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableFavorite(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('favorite'))
            ->addColumn(DB::integer(name: 'favorite_id', autoincrement: true))
            ->addColumn(DB::integer(name: 'user_id', nullable: true))
            ->addColumn(DB::integer(name: 'gedcom_id'))
            ->addColumn(DB::varchar(name: 'xref', length: 20, nullable: true))
            ->addColumn(DB::char(name: 'favorite_type', length: 4))
            ->addColumn(DB::varchar(name: 'url', length: 255, nullable: true))
            ->addColumn(DB::varchar(name: 'title', length: 255, nullable: true))
            ->addColumn(DB::varchar(name: 'note', length: 1000, nullable: true))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['favorite_id']))
            ->addIndex(DB::index(name: 'favorite_ix1', columns: ['user_id']))
            ->addIndex(DB::index(name: 'favorite_ix2', columns: ['gedcom_id', 'user_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'favorite_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'favorite_fk2', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableFile(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('file'))
            ->addColumn(DB::nvarchar(name: 'name', length: 255))
            ->addColumn(DB::integer(name: 'size', nullable: true))
            ->addColumn(DB::integer(name: 'last_modified', nullable: true))
            ->addColumn(DB::varchar(name: 'mime_type', length: 255, nullable: true))
            ->addColumn(DB::nvarchar(name: 'sha1', length: 40, nullable: true))
            ->addColumn(DB::integer(name: 'file_exists', nullable: true))
            ->addPrimaryKeyConstraint(DB::primaryKey(['name']))
            ->addIndex(DB::index(name: 'file_ix1', columns: ['sha1']))
            ->addIndex(DB::index(name: 'file_ix2', columns: ['size']))
            ->addIndex(DB::index(name: 'file_ix3', columns: ['mime_type']))
            ->addIndex(DB::index(name: 'file_ix4', columns: ['last_modified']))
            ->create();
    }

    public static function tableGedcom(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('gedcom'))
            ->addColumn(DB::integer(name: 'gedcom_id', autoincrement: true))
            ->addColumn(DB::nvarchar(name: 'gedcom_name', length: 255))
            ->addColumn(DB::integer(name: 'sort_order', default: 0))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['gedcom_id']))
            ->addIndex(DB::uniqueIndex(name: 'gedcom_ux1', columns: ['gedcom_name']))
            ->addIndex(DB::index(name: 'gedcom_ix1', columns: ['sort_order']))
            ->create();
    }

    public static function tableGedcomChunk(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('gedcom_chunk'))
            ->addColumn(DB::integer(name: 'gedcom_chunk_id', autoincrement: true))
            ->addColumn(DB::integer(name: 'gedcom_id'))
            ->addColumn(DB::text(name: 'chunk_data'))
            ->addColumn(DB::integer(name: 'imported', default: 0))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['gedcom_chunk_id']))
            ->addIndex(DB::index(name: 'gedcom_chunk_ix1', columns: ['gedcom_id', 'imported']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'gedcom_chunk_fk1', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableGedcomSetting(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('gedcom_setting'))
            ->addColumn(DB::integer('gedcom_id'))
            ->addColumn(DB::varchar('setting_name', length: 32))
            ->addColumn(DB::nvarchar('setting_value', length: 255))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['gedcom_id', 'setting_name']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'gedcom_setting_fk1', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableHitCounter(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('hit_counter'))
            ->addColumn(DB::integer('gedcom_id'))
            ->addColumn(DB::varchar('page_name', length: 32))
            ->addColumn(DB::varchar('page_parameter', length: 32))
            ->addColumn(DB::integer('page_count'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['gedcom_id', 'page_name', 'page_parameter']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'hit_counter_fk1', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableIndividuals(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('individuals'))
            ->addColumn(DB::varchar(name: 'i_id', length: 20))
            ->addColumn(DB::integer(name: 'i_file'))
            ->addColumn(DB::varchar(name: 'i_rin', length: 20))
            ->addColumn(DB::char(name: 'i_sex', length: 1))
            ->addColumn(DB::text(name: 'i_gedcom'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['i_id', 'i_file']))
            ->addIndex(DB::uniqueIndex(name: 'individuals_ux1', columns: ['i_file', 'i_id']))
            ->addIndex(DB::index(name: 'individuals_ix1', columns: ['i_file', 'i_sex']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'individuals_fk1', local_columns: ['i_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableJob(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('job'))
            ->addColumn(DB::integer(name: 'job_id', autoincrement: true))
            ->addColumn(DB::varchar(name: 'job_status', length: 15, default: 'queued'))
            ->addColumn(DB::integer(name: 'fail_count', default: 0))
            ->addColumn(DB::timestamp(name: 'queued_at', default: 'CURRENT_TIMESTAMP'))
            ->addColumn(DB::timestamp(name: 'queued_at'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['job_id']))
            ->create();
    }

    public static function tableLink(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('link'))
            ->addColumn(DB::integer(name: 'l_file'))
            ->addColumn(DB::varchar(name: 'l_from', length: 20))
            ->addColumn(DB::varchar(name: 'l_type', length: 15))
            ->addColumn(DB::varchar(name: 'l_to', length: 20))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['l_from', 'l_file', 'l_type', 'l_to']))
            ->addIndex(DB::uniqueIndex(name: 'link_ux1', columns: ['l_from', 'l_file', 'l_type', 'l_to']))
            ->addIndex(DB::uniqueIndex(name: 'link_ux2', columns: ['l_to', 'l_file', 'l_type', 'l_from']))
            ->addIndex(DB::index(name: 'link_ix1', columns: ['l_file']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'link_fk1', local_columns: ['l_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableLog(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('log'))
            ->addColumn(DB::integer(name: 'log_id', autoincrement: true))
            ->addColumn(DB::timestamp(name: 'log_time', default: 'CURRENT_TIMESTAMP'))
            ->addColumn(DB::varchar(name: 'log_type', length: 6))
            ->addColumn(DB::text(name: 'log_message'))
            ->addColumn(DB::varchar(name: 'ip_address', length: 45))
            ->addColumn(DB::integer(name: 'user_id', nullable: true))
            ->addColumn(DB::integer(name: 'gedcom_id', nullable: true))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['log_id']))
            ->addIndex(DB::index(name: 'log_ix1', columns: ['gedcom_id']))
            ->addIndex(DB::index(name: 'log_ix2', columns: ['user_id']))
            ->addIndex(DB::index(name: 'log_ix3', columns: ['log_time']))
            ->addIndex(DB::index(name: 'log_ix4', columns: ['log_type']))
            ->addIndex(DB::index(name: 'log_ix5', columns: ['ip_address']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'log_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::SET_NULL, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'log_fk2', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::SET_NULL, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableMedia(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('media'))
            ->addColumn(DB::varchar(name: 'm_id', length: 20))
            ->addColumn(DB::integer(name: 'm_file'))
            ->addColumn(DB::text(name: 'm_gedcom'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['m_file', 'm_id']))
            ->addIndex(DB::uniqueIndex(name: 'media_ux1', columns: ['m_id', 'm_file']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'media_fk1', local_columns: ['m_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableMediaFile(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('media_file'))
            ->addColumn(DB::integer(name: 'id', autoincrement: true))
            ->addColumn(DB::varchar(name: 'm_id', length: 20))
            ->addColumn(DB::integer(name: 'm_file'))
            ->addColumn(DB::nvarchar(name: 'multimedia_file_refn', length: 248))
            ->addColumn(DB::nvarchar(name: 'multimedia_format', length: 4))
            ->addColumn(DB::nvarchar(name: 'source_media_type', length: 15))
            ->addColumn(DB::nvarchar(name: 'descriptive_title', length: 248))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['id']))
            ->addIndex(DB::index(name: 'media_file_ix1', columns: ['m_id', 'm_file']))
            ->addIndex(DB::index(name: 'media_file_ix2', columns: ['m_file', 'm_id']))
            ->addIndex(DB::index(name: 'media_file_ix3', columns: ['m_file', 'multimedia_file_refn']))
            ->addIndex(DB::index(name: 'media_file_ix4', columns: ['m_file', 'multimedia_format']))
            ->addIndex(DB::index(name: 'media_file_ix5', columns: ['m_file', 'source_media_type']))
            ->addIndex(DB::index(name: 'media_file_ix6', columns: ['m_file', 'descriptive_title']))
            ->create();
    }

    public static function tableMessage(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('message'))
            ->addColumn(DB::integer(name: 'message_id', autoincrement: true))
            ->addColumn(DB::nvarchar(name: 'sender', length: 64))
            ->addColumn(DB::varchar(name: 'ip_address', length: 45))
            ->addColumn(DB::integer(name: 'user_id'))
            ->addColumn(DB::nvarchar(name: 'subject', length: 255))
            ->addColumn(DB::text(name: 'body'))
            ->addColumn(DB::timestamp(name: 'created', default: 'CURRENT_TIMESTAMP'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['message_id']))
            ->addIndex(DB::index(name: 'message_ix1', columns: ['user_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'message_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableModule(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('module'))
            ->addColumn(DB::varchar(name: 'module_name', length: 32))
            ->addColumn(DB::char(name: 'status', length: 8, default: 'enabled'))
            ->addColumn(DB::integer(name: 'tab_order', nullable: true))
            ->addColumn(DB::integer(name: 'menu_order', nullable: true))
            ->addColumn(DB::integer(name: 'sidebar_order', nullable: true))
            ->addColumn(DB::integer(name: 'footer_order', nullable: true))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['module_name']))
            ->create();
    }

    public static function tableModulePrivacy(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('module_privacy'))
            ->addColumn(DB::integer(name: 'id', autoincrement: true))
            ->addColumn(DB::varchar(name: 'module_name', length: 32))
            ->addColumn(DB::integer(name: 'gedcom_id'))
            ->addColumn(DB::varchar(name: 'interface', length: 255))
            ->addColumn(DB::integer(name: 'access_level'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['id']))
            ->addIndex(DB::uniqueIndex(name: 'module_privacy_ux1', columns: ['gedcom_id', 'module_name', 'interface']))
            ->addIndex(DB::uniqueIndex(name: 'module_privacy_ux2', columns: ['module_name', 'gedcom_id', 'interface']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'module_privacy_fk1', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'module_privacy_fk2', local_columns: ['module_name'], foreign_table: 'module', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableModuleSetting(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('module_setting'))
            ->addColumn(DB::varchar(name: 'module_name', length: 32))
            ->addColumn(DB::varchar(name: 'setting_name', length: 32))
            ->addColumn(DB::text(name: 'setting_value'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['module_name', 'setting_name']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'module_setting_fk1', local_columns: ['module_name'], foreign_table: 'module', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableName(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('name'))
            ->addColumn(DB::integer(name: 'n_file'))
            ->addColumn(DB::varchar(name: 'n_id', length: 20))
            ->addColumn(DB::integer(name: 'n_num'))
            ->addColumn(DB::varchar(name: 'n_type', length: 15))
            ->addColumn(DB::nvarchar(name: 'n_sort', length: 255))
            ->addColumn(DB::nvarchar(name: 'n_full', length: 255))
            ->addColumn(DB::nvarchar(name: 'n_surname', length: 255, nullable: true))
            ->addColumn(DB::nvarchar(name: 'n_surn', length: 255, nullable: true))
            ->addColumn(DB::nvarchar(name: 'n_givn', length: 255, nullable: true))
            ->addColumn(DB::varchar(name: 'n_soundex_givn_std', length: 255, nullable: true))
            ->addColumn(DB::varchar(name: 'n_soundex_surn_std', length: 255, nullable: true))
            ->addColumn(DB::varchar(name: 'n_soundex_givn_dm', length: 255, nullable: true))
            ->addColumn(DB::varchar(name: 'n_soundex_surn_dm', length: 255, nullable: true))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['n_id', 'n_file', 'n_num']))
            ->addIndex(DB::index(name: 'name_ix1', columns: ['n_full', 'n_id', 'n_file']))
            ->addIndex(DB::index(name: 'name_ix2', columns: ['n_givn', 'n_file', 'n_type', 'n_id']))
            ->addIndex(DB::index(name: 'name_ix3', columns: ['n_surn', 'n_file', 'n_type', 'n_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'name_fk1', local_columns: ['n_file', 'n_id'], foreign_table: 'individuals', foreign_columns: ['i_file', 'i_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableNews(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('news'))
            ->addColumn(DB::integer(name: 'news_id', autoincrement: true))
            ->addColumn(DB::integer(name: 'user_id', nullable: true))
            ->addColumn(DB::integer(name: 'gedcom_id', nullable: true))
            ->addColumn(DB::nvarchar(name: 'subject', length: 255))
            ->addColumn(DB::text(name: 'body'))
            ->addColumn(DB::timestamp(name: 'updated', default: 'CURRENT_TIMESTAMP'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['news_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'news_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'news_fk2', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableOther(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('other'))
            ->addColumn(DB::varchar(name: 'o_id', length: 20))
            ->addColumn(DB::integer(name: 'o_file'))
            ->addColumn(DB::varchar(name: 'o_type', length: 15))
            ->addColumn(DB::text(name: 'o_gedcom'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['o_file', 'o_id']))
            ->addIndex(DB::uniqueIndex(name: 'other_ux1', columns: ['o_id', 'o_file']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'other_fk1', local_columns: ['o_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tablePlaceLocation(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('place_location'))
            ->addColumn(DB::integer(name: 'id', autoincrement: true))
            ->addColumn(DB::integer(name: 'parent_id', nullable: true))
            ->addColumn(DB::nvarchar(name: 'place', length: 120))
            ->addColumn(DB::float(name: 'latitude', nullable: true))
            ->addColumn(DB::float(name: 'longitude', nullable: true))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['id']))
            ->addIndex(DB::uniqueIndex(name: 'place_location_ux1', columns: ['parent_id', 'place']))
            ->addIndex(DB::uniqueIndex(name: 'place_location_ux2', columns: ['place', 'parent_id']))
            ->addIndex(DB::index(name: 'place_location_ix1', columns: ['latitude']))
            ->addIndex(DB::index(name: 'place_location_ix2', columns: ['longitude']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'place_location_fk1', local_columns: ['parent_id'], foreign_table:  'place_location', foreign_columns: ['id']))
            ->create();
    }

    public static function tablePlaceLinks(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('placelinks'))
            ->addColumn(DB::integer(name: 'pl_p_id'))
            ->addColumn(DB::varchar(name: 'pl_gid', length: 20))
            ->addColumn(DB::integer(name: 'pl_file'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['pl_p_id', 'pl_gid', 'pl_file']))
            ->addIndex(DB::index(name: 'placelinks_ix1', columns: ['pl_p_id']))
            ->addIndex(DB::index(name: 'placelinks_ix2', columns: ['pl_gid']))
            ->addIndex(DB::index(name: 'placelinks_ix3', columns: ['pl_file']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'placelinks_fk1', local_columns: ['pl_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tablePlaces(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('places'))
            ->addColumn(DB::integer(name: 'p_id', autoincrement: true))
            ->addColumn(DB::nvarchar(name: 'p_place', length: 150))
            ->addColumn(DB::integer(name: 'p_parent_id', nullable: true))
            ->addColumn(DB::integer(name: 'p_file'))
            ->addColumn(DB::text(name: 'p_std_soundex'))
            ->addColumn(DB::text(name: 'p_dm_soundex'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['p_id']))
            ->addIndex(DB::uniqueIndex(name: 'places_ux1', columns: ['p_parent_id', 'p_file', 'p_place']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'places_fk1', local_columns: ['p_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableSession(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('session'))
            ->addColumn(DB::varchar(name: 'session_id', length: 32))
            ->addColumn(DB::timestamp(name: 'session_time', default: 'CURRENT_TIMESTAMP'))
            ->addColumn(DB::integer(name: 'user_id', nullable: true))
            ->addColumn(DB::varchar(name: 'ip_address', length: 45))
            ->addColumn(DB::text(name: 'session_data'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['session_id']))
            ->addIndex(DB::index(name: 'session_ix1', columns: ['session_time']))
            ->addIndex(DB::index(name: 'session_ix2', columns: ['user_id', 'ip_address']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'session_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableSiteSetting(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('site_setting'))
            ->addColumn(DB::varchar(name: 'setting_name', length: 32))
            ->addColumn(DB::nvarchar(name: 'setting_value', length: 2000))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['setting_name']))
            ->create();
    }

    public static function tableSources(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('sources'))
            ->addColumn(DB::varchar(name: 's_id', length: 20))
            ->addColumn(DB::integer(name: 's_file'))
            ->addColumn(DB::nvarchar(name: 's_name', length: 255))
            ->addColumn(DB::text(name: 's_gedcom'))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['s_file', 's_id']))
            ->addIndex(DB::uniqueIndex(name: 'sources_ux1', columns: ['s_id', 's_file']))
            ->addIndex(DB::index(name: 'sources_ix1', columns: ['s_file', 's_name']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'sources_fk1', local_columns: ['s_file'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'], on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableUser(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('user'))
            ->addColumn(DB::integer('user_id', autoincrement: true))
            ->addColumn(DB::nvarchar('user_name', length: 32))
            ->addColumn(DB::nvarchar('real_name', length: 64))
            ->addColumn(DB::nvarchar('email', length: 64))
            ->addColumn(DB::varchar('password', length: 128))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['user_id']))
            ->addIndex(DB::uniqueIndex(name: 'user_ux1', columns: ['user_name']))
            ->addIndex(DB::uniqueIndex(name: 'user_ux2', columns: ['email']))
            ->create();
    }

    public static function tableUserGedcomSetting(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('user_gedcom_setting'))
            ->addColumn(DB::integer(name: 'user_id'))
            ->addColumn(DB::integer(name: 'gedcom_id'))
            ->addColumn(DB::varchar(name: 'setting_name', length: 32))
            ->addColumn(DB::nvarchar(name: 'setting_value', length: 255))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['user_id', 'gedcom_id', 'setting_name']))
            ->addIndex(DB::index(name: 'user_gedcom_setting_ix1', columns: ['gedcom_id']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'user_gedcom_setting_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'user_gedcom_setting_fk2', local_columns: ['gedcom_id'], foreign_table: 'gedcom', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function tableUserSetting(): Table
    {
        return Table::editor()
            ->setUnquotedName(DB::prefix('user_setting'))
            ->addColumn(DB::integer(name: 'user_id'))
            ->addColumn(DB::varchar(name: 'setting_name', length: 32))
            ->addColumn(DB::nvarchar(name: 'setting_value', length: 255))
            ->addPrimaryKeyConstraint(DB::primaryKey(columns: ['user_id', 'setting_name']))
            ->addForeignKeyConstraint(DB::foreignKey(name: 'user_setting_fk1', local_columns: ['user_id'], foreign_table: 'user', on_delete: ReferentialAction::CASCADE, on_update: ReferentialAction::CASCADE))
            ->create();
    }

    public static function schema(): \Doctrine\DBAL\Schema\Schema
    {
        return new \Doctrine\DBAL\Schema\Schema([
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
        ]);
    }
}
