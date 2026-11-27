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

namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Database\Driver;
use Fisharebest\Database\Exceptions\QueryException;
use Fisharebest\Database\Schema\MigrationMode;
use Fisharebest\Database\Schema\Schema;
use Fisharebest\Database\Schema\Table;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use InvalidArgumentException;
use PDOException;
use RuntimeException;
use UnexpectedValueException;

use function array_keys;
use function array_unique;
use function count;
use function ctype_digit;
use function file_exists;
use function implode;
use function in_array;
use function is_dir;
use function is_int;
use function is_string;
use function min;
use function preg_match_all;
use function preg_replace;
use function realpath;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;
use function trim;

use const PHP_INT_MAX;

class WebtreesSchema
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

    private function backfillModulePrivacy(): void
    {
        $maximum_id = DB::query()->from('module_privacy')->value(DB::max(column: 'id'));

        if ($maximum_id !== null && !is_int($maximum_id) && !(is_string($maximum_id) && ctype_digit($maximum_id))) {
            throw new UnexpectedValueException('Invalid module privacy ID');
        }

        $next_id = (int) ($maximum_id ?? 0);

        $rows = DB::query()
            ->select('module_name', 'gedcom_id', 'component', 'id', 'interface')
            ->from('module_privacy')
            ->orderBy(
                DB::ascending('module_name'),
                DB::ascending('gedcom_id'),
                DB::ascending('component'),
            )
            ->get();

        foreach ($rows as $row) {
            $interface = self::COMPONENT_TO_INTERFACE[$row->component]
                ?? throw new UnexpectedValueException(sprintf('Unknown module privacy component "%s"', $row->component));

            if ($row->interface !== null && $row->interface !== $interface) {
                throw new UnexpectedValueException(sprintf('Conflicting module privacy interface for "%s"', $row->component));
            }

            if ($row->id !== null && $row->interface !== null) {
                continue;
            }

            if ($row->id === null) {
                if ($next_id === PHP_INT_MAX) {
                    throw new UnexpectedValueException('Module privacy IDs exhausted');
                }

                $next_id++;
            }

            DB::query(table: 'module_privacy')
                ->whereEqual(column: 'module_name', value: $row->module_name)
                ->whereEqual(column: 'gedcom_id', value: $row->gedcom_id)
                ->whereEqual(column: 'component', value: $row->component)
                ->update(values: [
                    'id'        => $row->id ?? $next_id,
                    'interface' => $interface,
                ]);
        }
    }

    private function dropLegacyPlaceIndex(): void
    {
        if (
            DB::driverName() !== Driver::MySQL->value
            && DB::driverName() !== Driver::MariaDB->value
        ) {
            return;
        }

        $connection = DB::connection();
        $schema     = $connection->adaptor()->introspectSchema();
        $table_name = $connection->prefix() . 'placelocation';

        if (!$schema->hasTable(name: $table_name)) {
            return;
        }

        $table = $schema->getTable(name: $table_name);

        foreach ($table->getIndexes() as $index) {
            if ($index->isUnique() && $index->getColumns() === ['pl_parent_id', 'pl_place']) {
                $sql = $connection->adaptor()->dropIndexSQL(table: $table, index: $index);

                DB::transactionDDL(callback: static function () use ($connection, $sql): void {
                    foreach ($sql as $statement) {
                        $connection->exec($statement);
                    }
                });

                return;
            }
        }
    }

    /**
     * Write default data to the database.
     */
    public function seedDatabase(): void
    {
        // Add a "default" user and tree to store default settings.
        DB::identityInsert(table: 'user', callback: static function (): void {
            DB::query(table: 'user')->insertOrUpdate([
                'user_id' => -1,
            ], [
                'user_name' => 'DEFAULT_USER',
                'real_name' => 'DEFAULT_USER',
                'email'     => 'DEFAULT_USER',
                'password'  => 'DEFAULT_USER',
            ]);
        });

        DB::identityInsert(table: 'gedcom', callback: static function (): void {
            DB::query(table: 'gedcom')->insertOrUpdate([
                'gedcom_id' => -1,
            ], [
                'gedcom_name' => 'DEFAULT_TREE',
            ]);
        });

        $updated = DB::now();

        // Set default privacy settings for new trees.
        DB::identityInsert(table: 'default_resn', callback: static function () use ($updated): void {
            foreach (
                [
                    1 => ['SSN', 'confidential'],
                    2 => ['SOUR', 'privacy'],
                    3 => ['REPO', 'privacy'],
                    4 => ['SUBM', 'confidential'],
                    5 => ['SUBN', 'confidential'],
                ] as $default_resn_id => [$tag_type, $resn]
            ) {
                DB::query(table: 'default_resn')->insertOrUpdate([
                    'default_resn_id' => $default_resn_id,
                ], [
                    'gedcom_id' => -1,
                    'tag_type'  => $tag_type,
                    'resn'      => $resn,
                    'updated'   => $updated,
                ], [
                    'resn'    => $resn,
                    'updated' => $updated,
                ]);
            }
        });
    }

    private function setVersion(int $version): void
    {
        DB::query(table: 'site_setting')
            ->insertOrUpdate(
                key_values: ['setting_name' => 'WT_SCHEMA_VERSION'],
                insert_values: ['setting_value' => (string) $version],
            );

        if (Site::$preferences !== []) {
            Site::$preferences['WT_SCHEMA_VERSION'] = (string) $version;
        }
    }

    /**
     * @return bool Were any updates applied
     */
    public function updateSchema(): bool
    {
        try {
            $current_version = (int) Site::getPreference('WT_SCHEMA_VERSION');
        } catch (PDOException | QueryException) {
            // During initial installation, the site_setting table does not exist.
            $current_version = 0;
        }

        if ($current_version === Webtrees::SCHEMA_VERSION) {
            return false;
        }

        $lax = new MigrationMode(
            strict_not_null: false,
            drop_orphan_columns: false,
            drop_orphan_tables: false,
        );

        $historical = new MigrationMode(
            strict_not_null: false,
            drop_orphan_columns: true,
            drop_orphan_tables: false,
        );

        $strict = new MigrationMode(
            strict_not_null: true,
            drop_orphan_columns: true,
            drop_orphan_tables: false,
        );

        $strict_keep_orphans = new MigrationMode(
            strict_not_null: true,
            drop_orphan_columns: false,
            drop_orphan_tables: false,
        );

        switch ($current_version) {
            case 0:
                // No upgrade needed - go straight to the latest version
                DB::migrate(target: $this->latestVersion(), mode: $strict, execute: true);
                $this->setVersion(Webtrees::SCHEMA_VERSION);
                break;

            case 1:
                DB::migrate(target: $this->version2(), mode: $lax, execute: true);
                DB::migrate(target: $this->version2(), mode: $historical, execute: true);
                $this->setVersion(2);
            // fall through

            case 2:
                DB::migrate(target: $this->version3(), mode: $lax, execute: true);
                DB::migrate(target: $this->version3(), mode: $historical, execute: true);
                $this->setVersion(3);
            // fall through

            case 3:
                DB::migrate(target: $this->version4(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion4(...));
                DB::migrate(target: $this->version4(), mode: $historical, execute: true);
                $this->setVersion(4);
            // fall through

            case 4:
                DB::migrate(target: $this->version5(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion5(...));
                DB::migrate(target: $this->version5(), mode: $historical, execute: true);
                $this->setVersion(5);
            // fall through

            case 5:
                DB::migrate(target: $this->version6(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion6(...));
                DB::migrate(target: $this->version6(), mode: $historical, execute: true);
                $this->setVersion(6);
            // fall through

            case 6:
                $target = $this->version7();
                DB::migrate(target: $this->version7(), mode: $lax, execute: true);
                DB::migrate(target: $this->version7(), mode: $historical, execute: true);
                DB::dropTableIfExists(table: 'remotelinks');
                $this->setVersion(7);
            // fall through

            case 7:
                DB::migrate(target: $this->version8(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion8(...));
                DB::migrate(target: $this->version8(), mode: $historical, execute: true);
                $this->setVersion(8);
            // fall through

            case 8:
                DB::migrate(target: $this->version9(), mode: $lax, execute: true);
                DB::migrate(target: $this->version9(), mode: $historical, execute: true);
                $this->setVersion(9);
            // fall through

            case 9:
                DB::migrate(target: $this->version10(), mode: $lax, execute: true);
                DB::migrate(target: $this->version10(), mode: $historical, execute: true);
                $this->setVersion(10);
            // fall through

            case 10:
                DB::migrate(target: $this->version11(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion11(...));
                DB::migrate(target: $this->version11(), mode: $historical, execute: true);
                $this->setVersion(11);
            // fall through

            case 11:
                DB::migrate(target: $this->version12(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion12(...));
                DB::migrate(target: $this->version12(), mode: $historical, execute: true);
                $this->setVersion(12);
            // fall through

            case 12:
                DB::migrate(target: $this->version13(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion13(...));
                DB::migrate(target: $this->version13(), mode: $historical, execute: true);
                $this->setVersion(13);
            // fall through

            case 13:
                DB::migrate(target: $this->version14(), mode: $lax, execute: true);
                DB::migrate(target: $this->version14(), mode: $historical, execute: true);
                $this->setVersion(14);
            // fall through

            case 14:
                DB::migrate(target: $this->version15(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion15(...));
                DB::migrate(target: $this->version15(), mode: $historical, execute: true);
                $this->setVersion(15);
            // fall through

            case 15:
                DB::migrate(target: $this->version16(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion16(...));
                DB::migrate(target: $this->version16(), mode: $historical, execute: true);
                $this->setVersion(16);
            // fall through

            case 16:
                DB::migrate(target: $this->version17(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion17(...));
                DB::migrate(target: $this->version17(), mode: $historical, execute: true);
                $this->setVersion(17);
            // fall through

            case 17:
                DB::migrate(target: $this->version18(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion18(...));
                DB::migrate(target: $this->version18(), mode: $historical, execute: true);
                $this->setVersion(18);
            // fall through

            case 18:
                DB::migrate(target: $this->version19(), mode: $lax, execute: true);
                DB::migrate(target: $this->version19(), mode: $historical, execute: true);
                $this->setVersion(19);
            // fall through

            case 19:
                DB::migrate(target: $this->version20(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion20(...));
                DB::migrate(target: $this->version20(), mode: $historical, execute: true);
                $this->setVersion(20);
            // fall through

            case 20:
                $this->upgradeHistoricalMedia($this->version21(), $lax, $strict);
                DB::dropTableIfExists(table: 'media_mapping');
                $this->setVersion(21);
            // fall through

            case 21:
                DB::migrate(target: $this->version22(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion22(...));
                DB::migrate(target: $this->version22(), mode: $historical, execute: true);
                $this->setVersion(22);
            // fall through

            case 22:
                DB::migrate(target: $this->version23(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion23(...));
                DB::migrate(target: $this->version23(), mode: $historical, execute: true);
                $this->setVersion(23);
            // fall through

            case 23:
                DB::migrate(target: $this->version24(), mode: $lax, execute: true);
                DB::migrate(target: $this->version24(), mode: $historical, execute: true);
                $this->setVersion(24);
            // fall through

            case 24:
                DB::migrate(target: $this->version25(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion25(...));
                DB::migrate(target: $this->version25(), mode: $historical, execute: true);
                $this->setVersion(25);
            // fall through

            case 25:
                DB::migrate(target: $this->version26(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion26(...));
                DB::migrate(target: $this->version26(), mode: $historical, execute: true);
                $this->setVersion(26);
            // fall through

            case 26:
                DB::migrate(target: $this->version27(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion27(...));
                DB::migrate(target: $this->version27(), mode: $historical, execute: true);
                $this->setVersion(27);
            // fall through

            case 27:
                DB::transaction(callback: $this->migrateToVersion28(...));
                DB::migrate(target: $this->version28(), mode: $lax, execute: true);
                DB::migrate(target: $this->version28(), mode: $historical, execute: true);
                $this->setVersion(28);
            // fall through

            case 28:
                DB::migrate(target: $this->version29(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion29(...));
                DB::migrate(target: $this->version29(), mode: $historical, execute: true);
                $this->setVersion(29);
            // fall through

            case 29:
                DB::migrate(target: $this->version30(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion30(...));
                DB::migrate(target: $this->version30(), mode: $historical, execute: true);
                DB::dropTableIfExists(table: 'ip_address');
                $this->setVersion(30);
            // fall through

            case 30:
                DB::migrate(target: $this->version31(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion31(...));
                DB::migrate(target: $this->version31(), mode: $historical, execute: true);
                $this->setVersion(31);
            // fall through

            case 31:
                DB::migrate(target: $this->version32(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion32(...));
                DB::migrate(target: $this->version32(), mode: $historical, execute: true);
                $this->setVersion(32);
            // fall through

            case 32:
                DB::migrate(target: $this->version33(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion33(...));
                DB::migrate(target: $this->version33(), mode: $historical, execute: true);
                $this->setVersion(33);
            // fall through

            case 33:
                DB::migrate(target: $this->version34(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion34(...));
                DB::migrate(target: $this->version34(), mode: $historical, execute: true);
                $this->setVersion(34);
            // fall through

            case 34:
                DB::migrate(target: $this->version35(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion35(...));
                DB::migrate(target: $this->version35(), mode: $historical, execute: true);
                $this->setVersion(35);
            // fall through

            case 35:
                DB::migrate(target: $this->version36(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion36(...));
                DB::migrate(target: $this->version36(), mode: $historical, execute: true);
                $this->setVersion(36);
            // fall through

            case 36:
                DB::migrate(target: $this->version37(), mode: $lax, execute: true);
                if (DB::query()->from('default_resn')->whereNull('updated')->count() > 0) {
                    throw new RuntimeException('Legacy restrictions need updated timestamps before schema version 37');
                }
                if (DB::query()->from('places')->whereNull('p_place')->count() > 0) {
                    throw new RuntimeException('Legacy places need names before schema version 37');
                }
                DB::migrate(target: $this->version37(), mode: $strict, execute: true);
                $this->setVersion(37);
            // fall through

            case 37:
                DB::migrate(target: $this->version38(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion38(...));
                DB::migrate(target: $this->version38(), mode: $strict, execute: true);
                DB::dropTableIfExists(table: 'next_id');
                DB::dropTableIfExists(table: 'site_access_rule');
                $this->setVersion(38);
            // fall through

            case 38:
                DB::migrate(target: $this->version39(), mode: $strict, execute: true);
                $this->setVersion(39);
            // fall through

            case 39:
                DB::migrate(target: $this->version40(), mode: $strict, execute: true);
                $this->setVersion(40);
            // fall through

            case 40:
                DB::migrate(target: $this->version41(), mode: $strict, execute: true);
                $this->setVersion(41);
            // fall through

            case 41:
                DB::migrate(target: $this->version42(), mode: $strict, execute: true);
                $this->setVersion(42);
            // fall through

            case 42:
                // We will replace a composite/natural primary key with an auto-increment id.
                // This needs intermediate steps.
                $module_privacy = DB::introspect()->getTable(name: 'module_privacy');

                if ($module_privacy->hasColumn(name: 'component') && $module_privacy->getPrimaryKey()?->getColumns() !== ['id']) {
                    DB::migrate(target: $this->version43a(), mode: $lax, execute: true);
                    DB::transaction(callback: $this->backfillModulePrivacy(...));
                    DB::migrate(target: $this->version43b(), mode: $strict_keep_orphans, execute: true);
                }

                DB::migrate(target: $this->version43c(), mode: $strict, execute: true);
                $this->setVersion(43);
            // fall through

            case 43:
                DB::migrate(target: $this->version44(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion44(...));
                DB::migrate(target: $this->version44(), mode: $strict, execute: true);
                $this->setVersion(44);
            // fall through

            case 44:
                DB::migrate(target: $this->version45(), mode: $lax, execute: true);
                $this->dropLegacyPlaceIndex();
                DB::transaction(callback: $this->migrateToVersion45(...));
                DB::migrate(target: $this->version45(), mode: $strict, execute: true);
                DB::dropTableIfExists(table: 'placelocation');
                $this->setVersion(45);
            // fall through

            case 45:
                DB::migrate(target: $this->version46(), mode: $lax, execute: true);
                DB::transaction(callback: $this->migrateToVersion46(...));
                DB::migrate(target: $this->version46(), mode: $strict, execute: true);
                $this->setVersion(46);
                break;

            default:
                $message = sprintf('Unsupported schema version %d', $current_version);
                throw new InvalidArgumentException($message);
        }

        return true;
    }

    private function upgradeHistoricalMedia(Schema $target, MigrationMode $lax, MigrationMode $strict): void
    {
        // The old m_id and m_file names refer to different values in the new schema.
        // Keep their replacements in temporary columns until the old key and columns are gone.
        $staged = $this->version20()->alterTable(name: 'media', callback: static fn (Table $table): Table => $table
            ->addColumn(column: DB::varchar(name: 'm_id_21', length: 20))
            ->addColumn(column: DB::integer(name: 'm_file_21'))
            ->addColumn(column: DB::varchar(name: 'm_filename', length: 512))
            ->addColumn(column: DB::text(name: 'm_gedcom'))
            ->addColumn(column: DB::varchar(name: 'm_type', length: 20)));

        $without_primary_key = $staged->alterTable(name: 'media', callback: static fn (Table $table): Table => $table
            ->alterColumn(column: DB::integer(name: 'm_id')->notNull())
            ->dropPrimaryKey());

        $without_old_columns = $without_primary_key->alterTable(name: 'media', callback: static fn (Table $table): Table => $table
            ->dropIndex(name: 'media_ix1')
            ->dropColumn(name: 'm_id')
            ->dropColumn(name: 'm_file')
            ->dropColumn(name: 'm_media')
            ->dropColumn(name: 'm_gedfile')
            ->dropColumn(name: 'm_gedrec'));

        $with_new_columns = $without_old_columns->alterTable(name: 'media', callback: static fn (Table $table): Table => $table
            ->addColumn(column: DB::varchar(name: 'm_id', length: 20))
            ->addColumn(column: DB::integer(name: 'm_file')));

        $media = DB::introspect()->getTable(name: 'media');

        if ($media->hasColumn(name: 'm_id_21') !== $media->hasColumn(name: 'm_file_21')) {
            if (!$media->hasColumn(name: 'm_id') || !$media->hasColumn(name: 'm_file')) {
                throw new RuntimeException('Legacy media staging is missing a source and destination column');
            }

            DB::transaction(callback: $this->migrateToVersion21(...));
            DB::migrate(target: $target, mode: $strict, execute: true);

            return;
        }

        if ($media->hasColumn(name: 'm_media') && $media->hasColumn(name: 'm_id') && $media->hasColumn(name: 'm_file')) {
            if (
                DB::query()
                    ->select('m_media')
                    ->from('media')
                    ->groupBy('m_media', 'm_gedfile')
                    ->havingRaw('COUNT(*) > 1')
                    ->first() !== null
            ) {
                throw new RuntimeException('Legacy media contains duplicate identifiers within a tree');
            }

            if ($media->getPrimaryKey() !== null) {
                DB::migrate(target: $staged, mode: $lax, execute: true);
            } elseif (!$media->hasColumn(name: 'm_id_21')) {
                throw new RuntimeException('Legacy media is missing its staging columns');
            }

            DB::query(table: 'media')->update([
                'm_id_21'    => DB::column('m_media'),
                'm_file_21'  => DB::column('m_gedfile'),
                'm_filename' => DB::column('m_file'),
                'm_gedcom'   => DB::column('m_gedrec'),
            ]);

            DB::migrate(target: $without_primary_key, mode: $lax, execute: true);
        }

        if (DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_id_21')) {
            DB::migrate(target: $without_old_columns, mode: $strict, execute: true);
            DB::migrate(target: $with_new_columns, mode: $lax, execute: true);
            DB::query(table: 'media')->update([
                'm_id'   => DB::column('m_id_21'),
                'm_file' => DB::column('m_file_21'),
            ]);
        }

        DB::transaction(callback: $this->migrateToVersion21(...));
        DB::migrate(target: $target, mode: $strict, execute: true);
    }

    public function migrateToVersion4(): void
    {
        // 1.6.2:includes/db_schema/db_schema_3_4.php transfers privacy-dependent relationship limits.
        $users = DB::query()
            ->select('user_id')
            ->from('user')
            ->get();
        foreach ($users as $user) {
            $values = DB::query()
                ->from('user_setting')
                ->whereEqual('user_id', $user->user_id)
                ->whereIn('setting_name', ['max_relation_path', 'relationship_privacy'])
                ->pluck('setting_value', 'setting_name');

            if (!isset($values['relationship_privacy']) || $values['relationship_privacy'] === '' || $values['relationship_privacy'] === '0' || !isset($values['max_relation_path'])) {
                continue;
            }

            $trees = DB::query()
                ->select('gedcom_id')
                ->from('gedcom')
                ->get();
            foreach ($trees as $tree) {
                $tree_values = DB::query()
                    ->from('gedcom_setting')
                    ->whereEqual('gedcom_id', $tree->gedcom_id)
                    ->whereIn('setting_name', ['MAX_RELATION_PATH_LENGTH', 'USE_RELATIONSHIP_PRIVACY'])
                    ->pluck('setting_value', 'setting_name');

                if (
                    isset($tree_values['USE_RELATIONSHIP_PRIVACY'], $tree_values['MAX_RELATION_PATH_LENGTH'])
                    && $tree_values['USE_RELATIONSHIP_PRIVACY'] !== '' && $tree_values['USE_RELATIONSHIP_PRIVACY'] !== '0'
                ) {
                    DB::query(table: 'user_gedcom_setting')->insertOrUpdate(
                        [
                            'user_id'      => $user->user_id,
                            'gedcom_id'    => $tree->gedcom_id,
                            'setting_name' => 'RELATIONSHIP_PATH_LENGTH',
                        ],
                        ['setting_value' => (string) min((int) $values['max_relation_path'], (int) $tree_values['MAX_RELATION_PATH_LENGTH'])],
                    );
                }
            }
        }

        $this->deleteSettings('site_setting', ['SESSION_SAVE_PATH']);
        $this->deleteSettings('gedcom_setting', [
            'HOME_SITE_TEXT', 'HOME_SITE_URL', 'CHECK_MARRIAGE_RELATIONS',
            'MAX_RELATION_PATH_LENGTH', 'USE_RELATIONSHIP_PRIVACY',
        ]);
        $this->deleteSettings('user_setting', ['loggedin', 'relationship_privacy', 'max_relation_path_length']);
        DB::query(table: 'name')
            ->whereLike('n_surn', 'MC0%')
            ->update(['n_surn' => DB::concat(
                DB::value('MC'),
                DB::substring(column: 'n_surn', start: 4, length: 255),
            )]);
    }

    public function migrateToVersion5(): void
    {
        $this->deleteSettings('gedcom_setting', ['PAGE_AFTER_LOGIN']);
        DB::query(table: 'gedcom_setting')->whereEqual('setting_name', 'NOTE_FACTS_ADD')
            ->whereEqual('setting_value', 'ASSO,SOUR,NOTE,REPO')->update(['setting_value' => 'SOUR']);
    }

    public function migrateToVersion6(): void
    {
        // 1.6.2:includes/db_schema/db_schema_5_6.php rewrites obsolete SHOW_RELATIVES_EVENTS tokens.
        $settings = DB::query()
            ->select('gedcom_id', 'setting_value')
            ->from('gedcom_setting')
            ->whereEqual('setting_name', 'SHOW_RELATIVES_EVENTS')
            ->get();
        foreach ($settings as $setting) {
            $value = preg_replace('/_(BIRT|MARR|DEAT)_(COUS|MSIB|FSIB|GGCH|NEPH|GGPA)|_FAMC_RESI_EMIG/', '', $setting->setting_value);
            $value = preg_replace('/_MARR_(MOTH|FATH|FAMC)/', '_MARR_PARE', $value);
            $value = preg_replace('/_DEAT_(MOTH|FATH)/', '_DEAT_PARE', $value);
            preg_match_all('/[_A-Z]+/', $value, $matches);
            DB::query(table: 'gedcom_setting')->whereEqual('gedcom_id', $setting->gedcom_id)
                ->whereEqual('setting_name', 'SHOW_RELATIVES_EVENTS')
                ->update(['setting_value' => implode(',', array_unique($matches[0]))]);
        }
    }

    public function migrateToVersion8(): void
    {
        // 1.6.2:includes/db_schema/db_schema_7_8.php strips the historical themes/ prefix.
        foreach (['gedcom_setting', 'user_setting', 'user_gedcom_setting'] as $table) {
            DB::query(table: $table)
                ->whereEqual('setting_name', 'THEME_DIR')
                ->whereLike('setting_value', '%/')
                ->update(['setting_value' => DB::substring(
                    column: 'setting_value',
                    start: 1,
                    length: DB::subtract(DB::length('setting_value'), DB::value(1)),
                )]);
            DB::query(table: $table)
                ->whereEqual('setting_name', 'THEME_DIR')
                ->whereLike('setting_value', 'themes/%')
                ->update(['setting_value' => DB::substring(column: 'setting_value', start: 8, length: 255)]);
        }
    }

    public function migrateToVersion11(): void
    {
        $this->deleteSettings('gedcom_setting', ['SEARCH_FACTS_DEFAULT', 'DISPLAY_JEWISH_GERESHAYIM', 'DISPLAY_JEWISH_THOUSANDS']);
    }

    public function migrateToVersion12(): void
    {
        // 1.6.2:includes/db_schema/db_schema_11_12.php removes slash escaping before n_list is dropped.
        DB::query(table: 'name')->whereNotNull('n_surn')->update(['n_full' => DB::replace(column: 'n_full', search: '/', replacement: '')]);
    }

    public function migrateToVersion13(): void
    {
        $settings = DB::query()
            ->select('gedcom_id', 'setting_value')
            ->from('gedcom_setting')
            ->whereEqual('setting_name', 'MULTI_MEDIA')
            ->get();
        foreach ($settings as $setting) {
            DB::query(table: 'gedcom_setting')->insertOrUpdate(
                ['gedcom_id' => $setting->gedcom_id, 'setting_name' => 'MEDIA_UPLOAD'],
                ['setting_value' => $setting->setting_value],
            );
        }
        $this->deleteSettings('gedcom_setting', ['SHOW_MEDIA_FILENAME', 'USE_THUMBS_MAIN', 'MULTI_MEDIA']);
        DB::query(table: 'default_resn')->whereEqual('tag_type', '_PRIM')->delete();
    }

    public function migrateToVersion15(): void
    {
        $this->deleteSettings('gedcom_setting', ['GEDCOM_DEFAULT_TAB', 'LINK_ICONS', 'ZOOM_BOXES']);
        $this->deleteSettings('user_setting', ['default']);
        $this->normalizeNoteFacts();
    }

    public function migrateToVersion16(): void
    {
        $this->deleteSettings('gedcom_setting', ['GEDCOM_DEFAULT_TAB', 'LINK_ICONS', 'ZOOM_BOXES', 'SHOW_LIST_PLACES', 'SHOW_CONTEXT_HELP']);
        $this->deleteSettings('user_setting', ['defaulttab']);
        $this->normalizeNoteFacts();
        DB::query(table: 'site_setting')->whereEqual('setting_name', 'LOGIN_URL')->whereEqual('setting_value', 'login.php')->delete();
        DB::query(table: 'site_setting')->whereEqual('setting_name', 'SERVER_URL')->whereEqual('setting_value', '')->delete();
    }

    public function migrateToVersion17(): void
    {
        // 1.6.2:includes/db_schema/db_schema_16_17.php seeds sentinel user/tree and default blocks.
        $module_names = [
            'todays_events', 'user_messages', 'user_favorites', 'user_welcome', 'random_media',
            'upcoming_events', 'logged_in', 'gedcom_stats', 'gedcom_news', 'gedcom_favorites',
            'review_changes', 'gedcom_block',
        ];
        foreach ($module_names as $module_name) {
            if (
                DB::query()
                    ->from('module')
                    ->whereEqual('module_name', $module_name)
                    ->count() === 0
            ) {
                // The old application registered modules before inserting its default blocks.
                // Decide how to register these historical module names on a current installation.
                throw new RuntimeException(sprintf('Version 17 requires legacy module %s to be registered before creating default blocks', $module_name));
            }
        }

        if (
            DB::query()
                ->from('user')
                ->whereEqual('user_id', -1)
                ->count() === 0
        ) {
            DB::query(table: 'user')->insert(['user_id' => -1, 'user_name' => 'DEFAULT_USER', 'real_name' => 'DEFAULT_USER', 'email' => 'DEFAULT_USER', 'password' => 'DEFAULT_USER']);
        }
        if (
            DB::query()
                ->from('gedcom')
                ->whereEqual('gedcom_id', -1)
                ->count() === 0
        ) {
            DB::query(table: 'gedcom')->insert(['gedcom_id' => -1, 'gedcom_name' => 'DEFAULT_TREE']);
        }
        $default_blocks = [
            'user_id' => [
                'main' => ['todays_events', 'user_messages', 'user_favorites'],
                'side' => ['user_welcome', 'random_media', 'upcoming_events', 'logged_in'],
            ],
            'gedcom_id' => [
                'main' => ['gedcom_stats', 'gedcom_news', 'gedcom_favorites', 'review_changes'],
                'side' => ['gedcom_block', 'random_media', 'todays_events', 'logged_in'],
            ],
        ];
        foreach ($default_blocks as $column => $locations) {
            foreach ($locations as $location => $modules) {
                foreach ($modules as $order => $module) {
                    if (
                        DB::query()
                            ->from('block')
                            ->whereEqual($column, -1)
                            ->whereEqual('location', $location)
                            ->whereEqual('block_order', $order + 1)
                            ->whereEqual('module_name', $module)
                            ->count() === 0
                    ) {
                        DB::query(table: 'block')->insert([$column => -1, 'location' => $location, 'block_order' => $order + 1, 'module_name' => $module]);
                    }
                }
            }
        }
    }

    public function migrateToVersion18(): void
    {
        // 1.6.2:includes/db_schema/db_schema_17_18.php seeds browser access rules after site_access_rule exists.
        $default_rules = [
            'Mozilla/5.0 (%) Gecko/% %/%' => 'Gecko-based browsers',
            'Mozilla/5.0 (%) AppleWebKit/% (KHTML, like Gecko)%' => 'WebKit-based browsers',
            'Opera/% (%) Presto/% Version/%' => 'Presto-based browsers',
            'Mozilla/% (compatible; MSIE %' => 'Trident-based browsers',
            'Mozilla/5.0 (compatible; Konqueror/%' => 'Konqueror browser',
        ];
        foreach ($default_rules as $pattern => $comment) {
            $this->insertAccessRule($pattern, $comment);
        }
    }

    public function migrateToVersion20(): void
    {
        foreach (['xref', 'tag_type'] as $column) {
            DB::query(table: 'default_resn')->whereEqual($column, '')->update([$column => null]);
        }
    }

    public function migrateToVersion21(): void
    {
        // 1.6.2:includes/db_schema/db_schema_20_21.php extracts TYPE after m_gedrec becomes m_gedcom.
        // The DDL must retain m_gedcom and m_type until this data step has completed.
        if (
            DB::hasColumn(table: 'media', column: 'm_media') ||
            !DB::hasColumn(table: 'media', column: 'm_gedcom') ||
            !DB::hasColumn(table: 'media', column: 'm_type')
        ) {
            throw new RuntimeException('Version 21 requires safe staging of legacy media columns as m_id, m_file, m_filename, m_gedcom and m_type before extracting 3 TYPE');
        }
        $media_records = DB::query()
            ->select('m_id', 'm_file', 'm_gedcom')
            ->from('media')
            ->whereLike('m_gedcom', "%\n3 TYPE %")
            ->get();
        foreach ($media_records as $media) {
            if (preg_match_all('/\n3 TYPE ([^\r\n]*)/', $media->m_gedcom, $matches) !== 0) {
                $type = $matches[1][count($matches[1]) - 1];
                if (strlen($type) > 20) {
                    throw new RuntimeException(sprintf('Version 21 media %s TYPE exceeds 20 characters', $media->m_id));
                }
                DB::query(table: 'media')->whereEqual('m_id', $media->m_id)->whereEqual('m_file', $media->m_file)
                    ->update(['m_type' => $type]);
            }
        }
        $this->deleteSettings('gedcom_setting', ['MEDIA_EXTERNAL']);
    }

    public function migrateToVersion22(): void
    {
        $this->migrateToVersion20();
        $this->deleteSettings('gedcom_setting', ['AUTO_GENERATE_THUMBS', 'POSTAL_CODE', 'MEDIA_DIRECTORY_LEVELS', 'USE_MEDIA_VIEWER']);
        DB::query(table: 'module_setting')->whereEqual('module_name', 'lightbox')->delete();
        // 1.6.2:includes/db_schema/db_schema_21_22.php removes old media path prefixes.
        $media_records = DB::query()
            ->select('m_id', 'm_file', 'm_filename', 'm_gedcom')
            ->from('media')
            ->get();
        foreach ($media_records as $media) {
            $directory = DB::query()
                ->from('gedcom_setting')
                ->whereEqual('gedcom_id', $media->m_file)
                ->whereEqual('setting_name', 'MEDIA_DIRECTORY')
                ->value('setting_value');
            $filename = $this->stripMediaPrefix($media->m_filename, $directory);
            $gedcom = $this->stripGedcomMediaPrefix($media->m_gedcom, $directory);
            DB::query(table: 'media')->whereEqual('m_id', $media->m_id)->whereEqual('m_file', $media->m_file)
                ->update(['m_filename' => $filename, 'm_gedcom' => $gedcom]);
        }
        $changes = DB::query()
            ->select('change_id', 'gedcom_id', 'new_gedcom')
            ->from('change')
            ->whereEqual('status', 'pending')
            ->get();
        foreach ($changes as $change) {
            $directory = DB::query()
                ->from('gedcom_setting')
                ->whereEqual('gedcom_id', $change->gedcom_id)
                ->whereEqual('setting_name', 'MEDIA_DIRECTORY')
                ->value('setting_value');
            DB::query(table: 'change')->whereEqual('change_id', $change->change_id)
                ->update(['new_gedcom' => $this->stripGedcomMediaPrefix($change->new_gedcom, $directory)]);
        }
    }

    public function migrateToVersion23(): void
    {
        // 1.6.2:includes/db_schema/db_schema_22_23.php relocates public media directories
        // and removes firewall files; filesystem changes require an independently retryable move.
        $settings = DB::query()
            ->select('gedcom_id', 'setting_value')
            ->from('gedcom_setting')
            ->whereEqual('setting_name', 'MEDIA_DIRECTORY')
            ->get();
        foreach ($settings as $setting) {
            $firewall = DB::query()
                ->from('gedcom_setting')
                ->whereEqual('gedcom_id', $setting->gedcom_id)
                ->whereEqual('setting_name', 'USE_MEDIA_FIREWALL')
                ->value('setting_value');
            if ($firewall !== null && $firewall !== '' && $firewall !== '0') {
                $root = DB::query()
                    ->from('gedcom_setting')
                    ->whereEqual('gedcom_id', $setting->gedcom_id)
                    ->whereEqual('setting_name', 'MEDIA_FIREWALL_ROOTDIR')
                    ->value('setting_value');
                $data_directory = realpath(Webtrees::DATA_DIR);
                if ($root === null || $data_directory === false || realpath($root) !== $data_directory) {
                    // Resolve the old custom firewall path and migrate its media before
                    // removing the settings that identify where those files are stored.
                    throw new RuntimeException('Version 23 requires migration of a custom media firewall directory');
                }
            } elseif (is_dir(Webtrees::ROOT_DIR . $setting->setting_value)) {
                // Move this public folder into data/ and remove its old index/security files.
                // A database transaction cannot undo the move if a later step fails.
                throw new RuntimeException('Version 23 requires a retryable move of a public media directory');
            }
        }

        $this->deleteSettings('gedcom_setting', ['USE_MEDIA_FIREWALL', 'MEDIA_FIREWALL_THUMBS', 'MEDIA_FIREWALL_ROOTDIR']);
    }

    public function migrateToVersion25(): void
    {
        // 1.6.2:includes/db_schema/db_schema_24_25.php promotes tree welcome settings.
        $settings = DB::query()
            ->select('setting_name', 'setting_value')
            ->from('gedcom_setting')
            ->get();
        foreach ($settings as $setting) {
            if (
                in_array($setting->setting_name, ['SHOW_REGISTER_CAUTION', 'WELCOME_TEXT_CUST_HEAD'], true)
                || str_starts_with($setting->setting_name, 'WELCOME_TEXT_AUTH_MODE')
            ) {
                DB::query(table: 'site_setting')->insertOrUpdate(
                    ['setting_name' => $setting->setting_name],
                    ['setting_value' => $setting->setting_value],
                );
            }
        }
        $this->deleteSettings('gedcom_setting', ['ALLOW_EDIT_GEDCOM', 'SHOW_REGISTER_CAUTION', 'WELCOME_TEXT_CUST_HEAD']);
        DB::query(table: 'gedcom_setting')->whereLike('setting_name', 'WELCOME_TEXT_AUTH_MODE%')->delete();
        $this->deleteSettings('site_setting', ['STORE_MESSAGES']);
    }

    public function migrateToVersion26(): void
    {
        // 1.4.5:includes/db_schema/db_schema_25_26.php repeats the settings promotion,
        // while 1.5.0:includes/db_schema/db_schema_25_26.php removes this welcome setting.
        $this->migrateToVersion25();
        $this->deleteSettings('site_setting', ['WELCOME_TEXT_CUST_HEAD']);
        $this->insertAccessRule('Mozilla/% (Windows%; Trident%; rv:%) like Gecko', 'Modern Internet Explorer');
    }

    public function migrateToVersion27(): void
    {
        // 1.6.2:includes/db_schema/db_schema_26_27.php removes quoted/zero soundex codes.
        $soundex_columns = [
            'name' => [
                'n_soundex_givn_std' => '0000',
                'n_soundex_surn_std' => '0000',
                'n_soundex_givn_dm' => '000000',
                'n_soundex_surn_dm' => '000000',
            ],
            'places' => [
                'p_std_soundex' => '0000',
                'p_dm_soundex' => '000000',
            ],
        ];
        foreach ($soundex_columns as $table => $columns) {
            $rows = DB::query()
                ->select('*')
                ->from($table)
                ->get();
            foreach ($rows as $row) {
                $updates = [];
                foreach ($columns as $column => $zero) {
                    $value = $row->$column;
                    if ($value !== null) {
                        if ($table === 'name') {
                            $value = trim($value, "'");
                        }
                        $value = str_replace([$zero . ':', ':' . $zero], '', $value);
                        $updates[$column] = $value === $zero ? null : $value;
                    }
                }
                $query = DB::query(table: $table);
                if ($table === 'name') {
                    $query = $query->whereEqual('n_id', $row->n_id)->whereEqual('n_file', $row->n_file)->whereEqual('n_num', $row->n_num);
                } else {
                    $query = $query->whereEqual('p_id', $row->p_id);
                }
                if ($updates !== []) {
                    $query->update($updates);
                }
            }
        }
    }

    public function migrateToVersion28(): void
    {
        $this->deleteSettings('gedcom_setting', ['USE_GEONAMES']);
        // 1.6.2:includes/db_schema/db_schema_27_28.php keeps the first access rule
        // for each address and user-agent combination before adding a unique index.
        $seen = [];
        $rules = DB::query()
            ->select('*')
            ->from('site_access_rule')
            ->orderBy(DB::ascending('site_access_rule_id'))
            ->get();
        foreach ($rules as $rule) {
            $key = implode("\0", [$rule->ip_address_end, $rule->ip_address_start, $rule->user_agent_pattern]);
            if (isset($seen[$key])) {
                DB::query(table: 'site_access_rule')->whereEqual('site_access_rule_id', $rule->site_access_rule_id)->delete();
            } else {
                $seen[$key] = true;
            }
        }
    }

    public function migrateToVersion29(): void
    {
        // 1.6.2:includes/db_schema/db_schema_28_29.php corrects Hebrew leap-year Adar.
        DB::query(table: 'dates')->whereEqual('d_mon', 6)->whereEqual('d_type', '@#DHEBREW@')
            ->whereRaw('MOD(7 * d_year + 1, 19) >= ?', [7])->update(['d_mon' => 7]);
    }

    public function migrateToVersion30(): void
    {
        $this->deleteSettings('user_setting', ['editaccount']);
        $this->deleteSettings('gedcom_setting', ['SHOW_STATS']);
        $this->deleteSettings('site_setting', ['REQUIRE_ADMIN_AUTH_REGISTRATION']);
        DB::query(table: 'site_access_rule')->whereEqual('user_agent_pattern', 'Mozilla/5.0 (compatible; Konqueror/%')
            ->update(['user_agent_pattern' => 'Mozilla/5.0 (% Konqueror/%']);
        $blocks = DB::query()
            ->select('block_setting.block_id', 'block_setting.setting_value')
            ->from('block_setting')
            ->join('block', ['block.block_id' => 'block_setting.block_id'])
            ->whereEqual('block_setting.setting_name', 'html')
            ->whereEqual('block.module_name', 'html')
            ->get();
        foreach ($blocks as $block) {
            DB::query(table: 'block_setting')->whereEqual('block_id', $block->block_id)->whereEqual('setting_name', 'html')
                ->update(['setting_value' => str_replace(['#WT_VERSION#', '#browserTime24#'], ['#webtreesVersion#', '#browserTime#'], $block->setting_value)]);
        }
        foreach (['gedcom_setting', 'site_setting', 'user_setting', 'block_setting'] as $table) {
            $name = $table === 'block_setting' ? 'languages' : 'language';
            DB::query(table: $table)->whereEqual('setting_name', $name)
                ->update(['setting_value' => DB::replace(column: 'setting_value', search: '_', replacement: '-')]);
        }
    }

    public function migrateToVersion31(): void
    {
        // SERVER_NAME and the current application title are required to reproduce
        // WEBTREES_EMAIL and META_TITLE without the removed Filter/Auth APIs.
        $server_name = $_SERVER['SERVER_NAME'] ?? null;
        if (!is_string($server_name) || $server_name === '') {
            throw new RuntimeException('Version 31 requires SERVER_NAME to derive default WEBTREES_EMAIL; run the upgrade in a configured HTTP environment');
        }
        $email = 'webtrees-noreply@' . preg_replace('/^www\./i', '', $server_name);
        $defaults = [
            'ADVANCED_NAME_FACTS'          => 'NICK,_AKA',
            'ADVANCED_PLAC_FACTS'          => '',
            'ALLOW_THEME_DROPDOWN'         => '1',
            'CALENDAR_FORMAT'              => 'gregorian',
            'CHART_BOX_TAGS'              => '',
            'COMMON_NAMES_ADD'            => '',
            'COMMON_NAMES_REMOVE'         => '',
            'COMMON_NAMES_THRESHOLD'      => '40',
            'DEFAULT_PEDIGREE_GENERATIONS' => '4',
            'EXPAND_RELATIVES_EVENTS'      => '0',
            'EXPAND_SOURCES'               => '0',
            'FAM_FACTS_ADD' => 'CENS,MARR,RESI,SLGS,MARR_CIVIL,MARR_RELIGIOUS,MARR_PARTNERS,RESN',
            'FAM_FACTS_QUICK' => 'MARR,DIV,_NMR',
            'FAM_FACTS_UNIQUE' => 'NCHI,MARL,DIV,ANUL,DIVF,ENGA,MARB,MARC,MARS,_NMR',
            'FAM_ID_PREFIX'               => 'F',
            'FORMAT_TEXT'                 => 'markdown',
            'FULL_SOURCES'                => '0',
            'GEDCOM_ID_PREFIX'            => 'I',
            'GEDCOM_MEDIA_PATH'           => '',
            'GENERATE_UIDS'               => '0',
            'HIDE_GEDCOM_ERRORS'          => '1',
            'HIDE_LIVE_PEOPLE'            => '1',
            'INDI_FACTS_ADD' => 'AFN,BIRT,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,FCOM,ORDN,NATU,EMIG,IMMI,CENS,PROB,WILL,GRAD,RETI,DSCR,EDUC,IDNO,NATI,NCHI,NMR,OCCU,PROP,RELI,RESI,SSN,TITL,BAPL,CONL,ENDL,SLGC,_MILI,ASSO,RESN',
            'INDI_FACTS_QUICK'           => 'BIRT,BURI,BAPM,CENS,DEAT,OCCU,RESI',
            'INDI_FACTS_UNIQUE'          => '',
            'KEEP_ALIVE_YEARS_BIRTH'     => '',
            'KEEP_ALIVE_YEARS_DEATH'     => '',
            'LANGUAGE'                   => 'en-US',
            'MAX_ALIVE_AGE'              => '120',
            'MAX_DESCENDANCY_GENERATIONS' => '15',
            'MAX_PEDIGREE_GENERATIONS'    => '10',
            'MEDIA_DIRECTORY'            => 'media/',
            'MEDIA_ID_PREFIX'            => 'M',
            'MEDIA_UPLOAD'               => '1',
            'META_DESCRIPTION'           => '',
            'META_TITLE'                 => 'webtrees',
            'NOTE_FACTS_ADD'             => 'SOUR,RESN',
            'NOTE_FACTS_QUICK'           => '',
            'NOTE_FACTS_UNIQUE'          => '',
            'NOTE_ID_PREFIX'             => 'N',
            'NO_UPDATE_CHAN'             => '0',
            'PEDIGREE_FULL_DETAILS'      => '1',
            'PEDIGREE_LAYOUT'            => '1',
            'PEDIGREE_ROOT_ID'           => '',
            'PEDIGREE_SHOW_GENDER'        => '0',
            'PREFER_LEVEL2_SOURCES'      => '1',
            'QUICK_REQUIRED_FACTS'       => 'BIRT,DEAT',
            'QUICK_REQUIRED_FAMFACTS'    => 'MARR',
            'REPO_FACTS_ADD'             => 'PHON,EMAIL,FAX,WWW,RESN',
            'REPO_FACTS_QUICK'           => '',
            'REPO_FACTS_UNIQUE'          => 'NAME,ADDR',
            'REPO_ID_PREFIX'             => 'R',
            'REQUIRE_AUTHENTICATION'     => '0',
            'SAVE_WATERMARK_IMAGE'       => '0',
            'SAVE_WATERMARK_THUMB'       => '0',
            'SHOW_AGE_DIFF'              => '0',
            'SHOW_COUNTER'               => '1',
            'SHOW_DEAD_PEOPLE'           => '2',
            'SHOW_EST_LIST_DATES'        => '0',
            'SHOW_FACT_ICONS'            => '1',
            'SHOW_GEDCOM_RECORD'         => '0',
            'SHOW_HIGHLIGHT_IMAGES'      => '1',
            'SHOW_LDS_AT_GLANCE'         => '0',
            'SHOW_LEVEL2_NOTES'          => '1',
            'SHOW_LIVING_NAMES'          => '1',
            'SHOW_MEDIA_DOWNLOAD'        => '0',
            'SHOW_NO_WATERMARK'          => '1',
            'SHOW_PARENTS_AGE'           => '1',
            'SHOW_PEDIGREE_PLACES'       => '9',
            'SHOW_PEDIGREE_PLACES_SUFFIX' => '0',
            'SHOW_PRIVATE_RELATIONSHIPS' => '1',
            'SHOW_RELATIVES_EVENTS' => '_BIRT_CHIL,_BIRT_SIBL,_MARR_CHIL,_MARR_PARE,_DEAT_CHIL,_DEAT_PARE,_DEAT_GPAR,_DEAT_SIBL,_DEAT_SPOU',
            'SOURCE_ID_PREFIX'            => 'S',
            'SOUR_FACTS_ADD'             => 'NOTE,REPO,SHARED_NOTE,RESN',
            'SOUR_FACTS_QUICK'           => 'TEXT,NOTE,REPO',
            'SOUR_FACTS_UNIQUE'          => 'AUTH,ABBR,TITL,PUBL,TEXT',
            'SUBLIST_TRIGGER_I'          => '200',
            'SURNAME_LIST_STYLE'         => 'style2',
            'SURNAME_TRADITION'          => 'paternal',
            'THUMBNAIL_WIDTH'            => '100',
            'USE_RIN'                    => '0',
            'USE_SILHOUETTE'             => '1',
            'WATERMARK_THUMB'            => '0',
            'WEBTREES_EMAIL'             => $email,
            'WORD_WRAPPED_NOTES'         => '0',
        ];
        foreach ($defaults as $name => $value) {
            DB::query(table: 'gedcom_setting')->insertOrUpdate(
                ['gedcom_id' => -1, 'setting_name' => $name],
                ['setting_value' => $value],
            );
        }
        DB::query(table: 'gedcom_setting')->whereEqual('setting_name', 'WEBTREES_EMAIL')->whereEqual('setting_value', '')->delete();
        $trees = DB::query()
            ->select('gedcom_id')
            ->from('gedcom')
            ->whereGreaterThan('gedcom_id', 0)
            ->get();
        foreach ($trees as $tree) {
            DB::query(table: 'gedcom_setting')->insertOrUpdate(
                ['gedcom_id' => $tree->gedcom_id, 'setting_name' => 'WEBTREES_EMAIL'],
                ['setting_value' => $email],
            );
        }
        foreach (['SSN' => 'confidential', 'SOUR' => 'privacy', 'REPO' => 'privacy', 'SUBM' => 'confidential', 'SUBN' => 'confidential'] as $tag => $restriction) {
            if (
                DB::query()
                    ->from('default_resn')
                    ->whereEqual('gedcom_id', -1)
                    ->whereEqual('tag_type', $tag)
                    ->count() === 0
            ) {
                DB::query(table: 'default_resn')->insert(['gedcom_id' => -1, 'tag_type' => $tag, 'resn' => $restriction]);
            }
        }
    }

    public function migrateToVersion32(): void
    {
        $index_directory = DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'INDEX_DIRECTORY')
            ->value('setting_value');
        if ($index_directory !== null && $index_directory !== '') {
            $directory = realpath(Webtrees::ROOT_DIR . $index_directory);
            if ($directory === false) {
                $directory = realpath($index_directory);
            }
            if ($directory === false) {
                throw new RuntimeException('Version 32 cannot locate INDEX_DIRECTORY to check for historical language files');
            }

            foreach (['en_AU', 'en_GB', 'en_US', 'fr_CA', 'pt_BR'] as $language) {
                foreach (['histo.' . $language . '.php', 'language/' . $language . '.php', 'language/' . $language . '.csv', 'language/' . $language . '.mo'] as $filename) {
                    if (file_exists($directory . '/' . $filename)) {
                        // Rename the old language/index file to its hyphenated tag before
                        // rewriting database settings; failures must be safe to retry.
                        throw new RuntimeException(sprintf('Version 32 must rename historical language file %s', $filename));
                    }
                }
            }
        }
        foreach (['en_AU' => 'en-AU', 'en_GB' => 'en-GB', 'en_US' => 'en-US', 'fr_CA' => 'fr-CA', 'pt_BR' => 'pt-BR'] as $old => $new) {
            $old_name = 'WELCOME_TEXT_AUTH_MODE_' . $old;
            $new_name = 'WELCOME_TEXT_AUTH_MODE_' . $new;
            $settings = DB::query()
                ->select('setting_value')
                ->from('site_setting')
                ->whereEqual('setting_name', $old_name)
                ->get();
            foreach ($settings as $setting) {
                DB::query(table: 'site_setting')->insertOrUpdate(
                    ['setting_name' => $new_name],
                    ['setting_value' => $setting->setting_value],
                );
                DB::query(table: 'site_setting')->whereEqual('setting_name', $old_name)->delete();
            }
            DB::query(table: 'block_setting')->whereEqual('setting_name', 'languages')
                ->update(['setting_value' => DB::replace(column: 'setting_value', search: $old, replacement: $new)]);
        }
    }

    public function migrateToVersion33(): void
    {
        DB::query(table: 'change')->whereEqual('old_gedcom', '')->whereEqual('new_gedcom', '')->delete();
    }

    public function migrateToVersion34(): void
    {
        $this->insertAccessRule('Mozilla/5.0 (Mobile; Windows Phone 8.1; % Microsoft; %', 'Windows Phone 8.1');
    }

    public function migrateToVersion35(): void
    {
        // Installed chart modules must be registered before removing common-name settings.
        // The obsolete Module::getInstalledModules('enabled') API requires a current module service.
        throw new RuntimeException('Version 35 requires registration of installed chart modules before removing common-name settings');
    }

    public function migrateToVersion36(): void
    {
        $text_columns = [
            'news' => ['body'],
            'other' => ['o_gedcom'],
            'places' => ['p_std_soundex', 'p_dm_soundex'],
            'block_setting' => ['setting_value'],
            'change' => ['new_gedcom', 'old_gedcom'],
            'families' => ['f_gedcom'],
            'individuals' => ['i_gedcom'],
            'log' => ['log_message'],
            'media' => ['m_gedcom'],
            'message' => ['body'],
            'module_setting' => ['setting_value'],
            'sources' => ['s_gedcom'],
        ];
        foreach ($text_columns as $table => $columns) {
            if (!DB::hasTable(table: $table)) {
                continue;
            }
            foreach ($columns as $column) {
                DB::query(table: $table)->whereNull($column)->update([$column => '']);
            }
        }
    }

    /**
     * @param list<string> $names
     */
    private function deleteSettings(string $table, array $names): void
    {
        DB::query(table: $table)->whereIn('setting_name', $names)->delete();
    }

    private function normalizeNoteFacts(): void
    {
        DB::query(table: 'gedcom_setting')->whereEqual('setting_name', 'NOTE_FACTS_ADD')
            ->whereEqual('setting_value', 'SOUR')->update(['setting_value' => 'SOUR,RESN']);
    }

    private function insertAccessRule(string $pattern, string $comment): void
    {
        if (
            DB::query()
                ->from('site_access_rule')
                ->whereEqual('user_agent_pattern', $pattern)
                ->whereEqual('ip_address_start', 0)
                ->whereEqual('ip_address_end', 4294967295)
                ->count() === 0
        ) {
            DB::query(table: 'site_access_rule')->insert(['user_agent_pattern' => $pattern, 'rule' => 'allow', 'comment' => $comment]);
        }
    }

    private function stripMediaPrefix(string|null $filename, string|null $directory): string|null
    {
        if ($filename === null) {
            return null;
        }
        while (str_starts_with($filename, './')) {
            $filename = substr($filename, 2);
        }
        if ($directory !== null && $directory !== '' && str_starts_with($filename, $directory)) {
            if (str_starts_with($filename, $directory . $directory)) {
                throw new RuntimeException('Version 22 cannot safely distinguish a repeated media-directory prefix from a partially migrated path');
            }
            $filename = substr($filename, strlen($directory));
        }
        return $filename;
    }

    private function stripGedcomMediaPrefix(string|null $gedcom, string|null $directory): string|null
    {
        if ($gedcom === null) {
            return null;
        }
        $gedcom = str_replace("\n1 FILE ./", "\n1 FILE ", $gedcom);
        if ($directory !== null && $directory !== '') {
            if (str_contains($gedcom, "\n1 FILE " . $directory . $directory)) {
                throw new RuntimeException('Version 22 cannot safely distinguish repeated media-directory prefixes in GEDCOM media paths');
            }
            $gedcom = str_replace("\n1 FILE " . $directory, "\n1 FILE ", $gedcom);
        }
        return $gedcom;
    }

    public function migrateToVersion38(): void
    {
        if (!DB::hasColumn(table: 'media', column: 'm_filename')) {
            foreach (['m_ext', 'm_type', 'm_titl'] as $column) {
                if (DB::hasColumn(table: 'media', column: $column)) {
                    throw new RuntimeException(sprintf('Missing legacy media column m_filename while %s remains', $column));
                }
            }

            return;
        }

        foreach (['m_ext', 'm_type', 'm_titl'] as $column) {
            if (!DB::hasColumn(table: 'media', column: $column)) {
                throw new RuntimeException(sprintf('Missing legacy media column %s', $column));
            }
        }

        DB::query(table: 'media_file')->insertFrom(
            ['m_id', 'm_file', 'multimedia_file_refn', 'multimedia_format', 'source_media_type', 'descriptive_title'],
            DB::query()
                ->select(
                    'media.m_id',
                    'media.m_file',
                    DB::substring(column: 'media.m_filename', start: 1, length: 248),
                    DB::substring(column: 'media.m_ext', start: 1, length: 4),
                    DB::substring(column: 'media.m_type', start: 1, length: 15),
                    DB::substring(column: 'media.m_titl', start: 1, length: 248),
                )
                ->from('media')
                ->leftJoin('media_file', [
                    'media_file.m_id'   => 'media.m_id',
                    'media_file.m_file' => 'media.m_file',
                ])
                ->whereNull('media_file.id'),
        );
    }

    public function migrateToVersion44(): void
    {
        $language = DB::query()
            ->from('gedcom_setting')
            ->whereEqual('setting_name', 'LANGUAGE')
            ->whereGreaterThan('gedcom_id', 0)
            ->value('setting_value');

        DB::query(table: 'site_setting')->insertOrUpdate(
            ['setting_name' => 'LANGUAGE'],
            ['setting_value' => $language ?? 'en-US'],
        );
    }

    public function migrateToVersion45(): void
    {
        if (DB::hasTable(table: 'placelocation')) {
            if (DB::driverName() !== Driver::MySQL->value && DB::driverName() !== Driver::MariaDB->value) {
                if (DB::query()->from('placelocation')->count() !== 0) {
                    throw new RuntimeException('Legacy placelocation contains data on a non-MySQL database; cannot import it safely');
                }
            } else {
                $this->importLegacyPlaces();
            }
        }

        DB::query(table: 'place_location')
            ->whereNotNull('parent_id')
            ->whereEqual('latitude', 0)
            ->whereEqual('longitude', 0)
            ->update([
                'latitude'  => null,
                'longitude' => null,
            ]);
    }

    private function importLegacyPlaces(): void
    {
        $legacy_table = DB::introspect()->getTable(name: 'placelocation');
        $has_unique_name = false;

        foreach ($legacy_table->getIndexes() as $index) {
            if ($index->isUnique() && $index->getColumns() === ['pl_parent_id', 'pl_place']) {
                $has_unique_name = true;
            }
        }

        if ($has_unique_name) {
            $duplicate = DB::query()
                ->select('pl_parent_id')
                ->from('placelocation')
                ->groupBy('pl_parent_id', DB::substring(column: 'pl_place', start: 1, length: 120))
                ->havingRaw('COUNT(*) > ?', [1])
                ->first();

            if ($duplicate !== null) {
                throw new RuntimeException('Legacy placelocation unique index must be dropped before truncating and merging duplicate places in version 45');
            }
        }

        DB::query(table: 'placelocation')
            ->whereEqual('pl_lati', '')
            ->orWhereEqual('pl_long', '')
            ->update(['pl_lati' => null, 'pl_long' => null]);

        DB::query(table: 'placelocation')
            ->whereRaw('(pl_lati NOT REGEXP ? OR pl_long NOT REGEXP ?)', [
                '^[NS][0-9]+[.]?[0-9]*$',
                '^[EW][0-9]+[.]?[0-9]*$',
            ])
            ->update(['pl_lati' => null, 'pl_long' => null]);

        DB::query(table: 'placelocation')->update([
            'pl_place' => DB::substring(column: 'pl_place', start: 1, length: 120),
        ]);

        while (true) {
            $row = DB::query()
                ->select(
                    DB::alias(expression: DB::min('pl_id'), alias: 'minimum_id'),
                    DB::alias(expression: DB::max('pl_id'), alias: 'maximum_id'),
                )
                ->from('placelocation')
                ->groupBy('pl_parent_id', 'pl_place')
                ->havingRaw('COUNT(*) > ?', [1])
                ->first();

            if ($row === null) {
                break;
            }

            DB::query(table: 'placelocation')
                ->whereEqual('pl_parent_id', $row->maximum_id)
                ->update(['pl_parent_id' => $row->minimum_id]);

            DB::query(table: 'placelocation')
                ->whereEqual('pl_id', $row->maximum_id)
                ->delete();
        }

        $roots = DB::query()
            ->select('pl_id')
            ->from('placelocation')
            ->whereNull('pl_parent_id')
            ->orWhereEqual('pl_parent_id', 0);

        $children = DB::query()
            ->select('child.pl_id')
            ->from('placelocation AS child')
            ->join('valid_places AS parent', ['parent.pl_id' => 'child.pl_parent_id']);

        DB::query(table: 'place_location')->insertFrom(
            ['id', 'parent_id', 'place', 'latitude', 'longitude'],
            DB::query()
                ->withRecursiveCte('valid_places', $roots, $children)
                ->select(
                    'placelocation.pl_id',
                    DB::case('pl_parent_id')->when(0, null)->otherwise(DB::column('pl_parent_id')),
                    'pl_place',
                    DB::replace(DB::replace('pl_lati', 'S', '-'), 'N', ''),
                    DB::replace(DB::replace('pl_long', 'W', '-'), 'E', ''),
                )
                ->from('placelocation')
                ->join('valid_places', ['valid_places.pl_id' => 'placelocation.pl_id'])
                ->leftJoin('place_location', ['place_location.id' => 'placelocation.pl_id'])
                ->whereNull('place_location.id')
                ->orderBy(DB::ascending('placelocation.pl_id')),
        );

        $unimported = DB::query()
            ->select('placelocation.pl_id')
            ->from('placelocation')
            ->leftJoin('place_location', ['place_location.id' => 'placelocation.pl_id'])
            ->whereNull('place_location.id')
            ->first();

        if ($unimported !== null) {
            throw new RuntimeException(sprintf('Cannot import legacy placelocation id %s: missing or cyclic parent', $unimported->pl_id));
        }
    }

    public function migrateToVersion46(): void
    {
        if (
            DB::query()
                ->from('gedcom_setting')
                ->whereEqual('setting_name', 'LANGUAGE')
                ->count() !== 0
            && DB::query()
                ->from('site_setting')
                ->whereEqual('setting_name', 'LANGUAGE')
                ->count() === 0
        ) {
            throw new RuntimeException('Site LANGUAGE preference must be migrated before removing tree language settings');
        }

        DB::query(table: 'media_file')->update([
            'multimedia_format' => DB::upper(column: 'multimedia_format'),
            'source_media_type' => DB::upper(column: 'source_media_type'),
        ]);

        $new_columns = [
            'MEDIA_DIRECTORY'        => 'media_folder',
            'imported'               => 'imported',
            'title'                  => 'title',
            'gedcom_filename'        => 'gedcom_filename',
            'REQUIRE_AUTHENTICATION' => 'private',
            'CONTACT_USER_ID'        => 'contact_user_id',
            'WEBMASTER_USER_ID'      => 'support_user_id',
        ];

        $rows = DB::query()
            ->select('gedcom_id', 'setting_name', 'setting_value')
            ->from('gedcom_setting')
            ->whereIn('setting_name', array_keys($new_columns))
            ->get();

        foreach ($rows as $row) {
            $column = $new_columns[$row->setting_name];
            if (
                in_array($row->setting_name, ['CONTACT_USER_ID', 'WEBMASTER_USER_ID'], true)
                && $row->setting_value !== ''
                && !ctype_digit($row->setting_value)
            ) {
                throw new RuntimeException(sprintf('Tree %s has invalid user ID in %s', $row->gedcom_id, $row->setting_name));
            }

            $value = match ($row->setting_name) {
                'imported', 'REQUIRE_AUTHENTICATION'   => (int) (bool) $row->setting_value,
                'CONTACT_USER_ID', 'WEBMASTER_USER_ID' => (int) $row->setting_value === 0 ? null : (int) $row->setting_value,
                default                                => mb_substr($row->setting_value, 0, 255),
            };

            if (in_array($row->setting_name, ['CONTACT_USER_ID', 'WEBMASTER_USER_ID'], true) && $value !== null) {
                $exists = DB::query()
                    ->from('user')
                    ->whereEqual('user_id', $value)
                    ->count();

                if ($exists === 0) {
                    throw new RuntimeException(sprintf('Tree %s references missing user %s in %s', $row->gedcom_id, $value, $row->setting_name));
                }
            }

            $updated = DB::query(table: 'gedcom')
                ->whereEqual('gedcom_id', $row->gedcom_id)
                ->update([$column => $value]);

            if (
                $updated === 0 && DB::query()
                    ->from('gedcom')
                    ->whereEqual('gedcom_id', $row->gedcom_id)
                    ->count() === 0
            ) {
                throw new RuntimeException(sprintf('Tree %s referenced by %s does not exist', $row->gedcom_id, $row->setting_name));
            }
        }

        DB::query(table: 'gedcom_setting')
            ->whereIn('setting_name', [...array_keys($new_columns), 'LANGUAGE'])
            ->delete();
    }

    public function latestVersion(): Schema
    {
        return $this->version46();
    }

    public function version1(): Schema
    {
        return DB::schema(tables: [
            DB::table(
                name: 'block',
                columns: [
                    DB::integer(name: 'block_id')->autoIncrement()->notNull(),
                    DB::integer(name: 'gedcom_id'),
                    DB::integer(name: 'user_id'),
                    DB::varchar(name: 'xref', length: 20),
                    DB::varchar(name: 'location', length: 4),
                    DB::integer(name: 'block_order')->notNull(),
                    DB::varchar(name: 'module_name', length: 32)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['block_id']),
                indexes: [
                    DB::index(name: 'block_ix1', columns: ['module_name']),
                    DB::index(name: 'block_ix2', columns: ['gedcom_id']),
                    DB::index(name: 'block_ix3', columns: ['user_id']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'block_fk1', columns: ['module_name'], foreign_table: 'module', foreign_columns: ['module_name']),
                    DB::foreignKey(name: 'block_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                    DB::foreignKey(name: 'block_fk3', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id']),
                ],
            ),
            DB::table(
                name: 'block_setting',
                columns: [
                    DB::integer(name: 'block_id')->notNull(),
                    DB::varchar(name: 'setting_name', length: 32)->notNull(),
                    DB::text(name: 'setting_value')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['block_id', 'setting_name']),
                foreign_keys: [
                    DB::foreignKey(name: 'block_setting_fk1', columns: ['block_id'], foreign_table: 'block', foreign_columns: ['block_id']),
                ],
            ),
            DB::table(
                name: 'change',
                columns: [
                    DB::integer(name: 'change_id')->autoIncrement()->notNull(),
                    DB::datetime(name: 'change_time')->notNull(),
                    DB::varchar(name: 'status', length: 8)->default(default: 'pending')->notNull(),
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'xref', length: 20)->notNull(),
                    DB::text(name: 'old_gedcom')->notNull(),
                    DB::text(name: 'new_gedcom')->notNull(),
                    DB::integer(name: 'user_id')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['change_id']),
                indexes: [
                    DB::index(name: 'change_ix1', columns: ['gedcom_id', 'status', 'xref']),
                    DB::index(name: 'change_ix2', columns: ['user_id']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'change_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id']),
                    DB::foreignKey(name: 'change_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'dates',
                columns: [
                    DB::integer(name: 'd_day')->notNull(),
                    DB::varchar(name: 'd_month', length: 5),
                    DB::integer(name: 'd_mon')->notNull(),
                    DB::integer(name: 'd_year')->notNull(),
                    DB::integer(name: 'd_julianday1')->notNull(),
                    DB::integer(name: 'd_julianday2')->notNull(),
                    DB::varchar(name: 'd_fact', length: 15)->notNull(),
                    DB::varchar(name: 'd_gid', length: 20)->notNull(),
                    DB::integer(name: 'd_file')->notNull(),
                    DB::varchar(name: 'd_type', length: 13)->notNull(),
                ],
                indexes: [
                    DB::index(name: 'dates_ix1', columns: ['d_day']),
                    DB::index(name: 'dates_ix2', columns: ['d_month']),
                    DB::index(name: 'dates_ix3', columns: ['d_mon']),
                    DB::index(name: 'dates_ix4', columns: ['d_year']),
                    DB::index(name: 'dates_ix5', columns: ['d_julianday1']),
                    DB::index(name: 'dates_ix6', columns: ['d_julianday2']),
                    DB::index(name: 'dates_ix7', columns: ['d_gid']),
                    DB::index(name: 'dates_ix8', columns: ['d_file']),
                    DB::index(name: 'dates_ix9', columns: ['d_type']),
                    DB::index(name: 'dates_ix10', columns: ['d_fact', 'd_gid']),
                ],
            ),
            DB::table(
                name: 'default_resn',
                columns: [
                    DB::integer(name: 'default_resn_id')->autoIncrement()->notNull(),
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'xref', length: 20),
                    DB::varchar(name: 'tag_type', length: 15),
                    DB::varchar(name: 'resn', length: 12)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['default_resn_id']),
                indexes: [
                    DB::unique(name: 'default_resn_ux1', columns: ['gedcom_id', 'xref', 'tag_type']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'default_resn_fk1', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'families',
                columns: [
                    DB::varchar(name: 'f_id', length: 20)->notNull(),
                    DB::integer(name: 'f_file')->notNull(),
                    DB::varchar(name: 'f_husb', length: 20),
                    DB::varchar(name: 'f_wife', length: 20),
                    DB::text(name: 'f_gedcom')->notNull(),
                    DB::integer(name: 'f_numchil')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['f_id', 'f_file']),
                indexes: [
                    DB::unique(name: 'families_ux1', columns: ['f_file', 'f_id']),
                    DB::index(name: 'families_ix1', columns: ['f_husb']),
                    DB::index(name: 'families_ix2', columns: ['f_wife']),
                ],
            ),
            DB::table(
                name: 'gedcom',
                columns: [
                    DB::integer(name: 'gedcom_id')->autoIncrement()->notNull(),
                    DB::varchar(name: 'gedcom_name', length: 255)->notNull(),
                    DB::text(name: 'import_gedcom')->notNull(),
                    DB::integer(name: 'import_offset')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['gedcom_id']),
                indexes: [
                    DB::unique(name: 'gedcom_ux1', columns: ['gedcom_name']),
                ],
            ),
            DB::table(
                name: 'gedcom_setting',
                columns: [
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'setting_name', length: 32)->notNull(),
                    DB::varchar(name: 'setting_value', length: 255)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['gedcom_id', 'setting_name']),
                foreign_keys: [
                    DB::foreignKey(name: 'gedcom_setting_fk1', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'hit_counter',
                columns: [
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'page_name', length: 32)->notNull(),
                    DB::varchar(name: 'page_parameter', length: 32)->notNull(),
                    DB::integer(name: 'page_count')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['gedcom_id', 'page_name', 'page_parameter']),
                foreign_keys: [
                    DB::foreignKey(name: 'hit_counter_fk1', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'individuals',
                columns: [
                    DB::varchar(name: 'i_id', length: 20)->notNull(),
                    DB::integer(name: 'i_file')->notNull(),
                    DB::varchar(name: 'i_rin', length: 20)->notNull(),
                    DB::varchar(name: 'i_sex', length: 1)->notNull(),
                    DB::text(name: 'i_gedcom')->notNull(),
                    DB::integer(name: 'i_isdead')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['i_id', 'i_file']),
                indexes: [
                    DB::unique(name: 'individuals_ux1', columns: ['i_file', 'i_id']),
                ],
            ),
            DB::table(
                name: 'ip_address',
                columns: [
                    DB::varchar(name: 'ip_address', length: 40)->notNull(),
                    DB::varchar(name: 'category', length: 13)->notNull(),
                    DB::varchar(name: 'comment', length: 255)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['ip_address']),
            ),
            DB::table(
                name: 'link',
                columns: [
                    DB::integer(name: 'l_file')->notNull(),
                    DB::varchar(name: 'l_from', length: 20)->notNull(),
                    DB::varchar(name: 'l_type', length: 15)->notNull(),
                    DB::varchar(name: 'l_to', length: 20)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['l_from', 'l_file', 'l_type', 'l_to']),
                indexes: [
                    DB::unique(name: 'link_ux1', columns: ['l_to', 'l_file', 'l_type', 'l_from']),
                ],
            ),
            DB::table(
                name: 'log',
                columns: [
                    DB::integer(name: 'log_id')->autoIncrement()->notNull(),
                    DB::datetime(name: 'log_time')->notNull(),
                    DB::varchar(name: 'log_type', length: 6)->notNull(),
                    DB::text(name: 'log_message')->notNull(),
                    DB::varchar(name: 'ip_address', length: 40)->notNull(),
                    DB::integer(name: 'user_id'),
                    DB::integer(name: 'gedcom_id'),
                ],
                primary_key: DB::primaryKey(columns: ['log_id']),
                indexes: [
                    DB::index(name: 'log_ix1', columns: ['log_time']),
                    DB::index(name: 'log_ix2', columns: ['log_type']),
                    DB::index(name: 'log_ix3', columns: ['ip_address']),
                    DB::index(name: 'log_ix4', columns: ['user_id']),
                    DB::index(name: 'log_ix5', columns: ['gedcom_id']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'log_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id']),
                    DB::foreignKey(name: 'log_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'media',
                columns: [
                    DB::integer(name: 'm_id')->autoIncrement()->notNull(),
                    DB::varchar(name: 'm_ext', length: 6),
                    DB::varchar(name: 'm_titl', length: 255),
                    DB::varchar(name: 'm_file', length: 512),
                    DB::varchar(name: 'm_media', length: 20)->notNull(),
                    DB::integer(name: 'm_gedfile')->notNull(),
                    DB::text(name: 'm_gedrec'),
                ],
                primary_key: DB::primaryKey(columns: ['m_id']),
                indexes: [
                    DB::index(name: 'media_ix1', columns: ['m_media', 'm_gedfile']),
                ],
            ),
            DB::table(
                name: 'media_mapping',
                columns: [
                    DB::integer(name: 'mm_id')->autoIncrement()->notNull(),
                    DB::varchar(name: 'mm_media', length: 20)->notNull(),
                    DB::varchar(name: 'mm_gid', length: 20)->notNull(),
                    DB::integer(name: 'mm_order')->default(default: 0)->notNull(),
                    DB::integer(name: 'mm_gedfile')->notNull(),
                    DB::text(name: 'mm_gedrec')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['mm_id']),
                indexes: [
                    DB::index(name: 'media_mapping_ix1', columns: ['mm_media', 'mm_gedfile']),
                    DB::index(name: 'media_mapping_ix2', columns: ['mm_gid', 'mm_gedfile']),
                    DB::index(name: 'media_mapping_ix3', columns: ['mm_gedfile']),
                ],
            ),
            DB::table(
                name: 'message',
                columns: [
                    DB::integer(name: 'message_id')->autoIncrement()->notNull(),
                    DB::varchar(name: 'sender', length: 64)->notNull(),
                    DB::varchar(name: 'ip_address', length: 40)->notNull(),
                    DB::integer(name: 'user_id')->notNull(),
                    DB::varchar(name: 'subject', length: 255)->notNull(),
                    DB::text(name: 'body')->notNull(),
                    DB::datetime(name: 'created')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['message_id']),
                indexes: [
                    DB::index(name: 'message_ix1', columns: ['user_id']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'message_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id']),
                ],
            ),
            DB::table(
                name: 'module',
                columns: [
                    DB::varchar(name: 'module_name', length: 32)->notNull(),
                    DB::varchar(name: 'status', length: 8)->default(default: 'enabled')->notNull(),
                    DB::integer(name: 'tab_order'),
                    DB::integer(name: 'menu_order'),
                    DB::integer(name: 'sidebar_order'),
                ],
                primary_key: DB::primaryKey(columns: ['module_name']),
            ),
            DB::table(
                name: 'module_privacy',
                columns: [
                    DB::varchar(name: 'module_name', length: 32)->notNull(),
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'component', length: 7)->notNull(),
                    DB::integer(name: 'access_level')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['module_name', 'gedcom_id', 'component']),
                indexes: [
                    DB::unique(name: 'module_privacy_ux1', columns: ['gedcom_id', 'module_name', 'component']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'module_privacy_fk1', columns: ['module_name'], foreign_table: 'module', foreign_columns: ['module_name']),
                    DB::foreignKey(name: 'module_privacy_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'module_setting',
                columns: [
                    DB::varchar(name: 'module_name', length: 32)->notNull(),
                    DB::varchar(name: 'setting_name', length: 32)->notNull(),
                    DB::text(name: 'setting_value')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['module_name', 'setting_name']),
                foreign_keys: [
                    DB::foreignKey(name: 'module_setting_fk1', columns: ['module_name'], foreign_table: 'module', foreign_columns: ['module_name']),
                ],
            ),
            DB::table(
                name: 'name',
                columns: [
                    DB::integer(name: 'n_file')->notNull(),
                    DB::varchar(name: 'n_id', length: 20)->notNull(),
                    DB::integer(name: 'n_num')->notNull(),
                    DB::varchar(name: 'n_type', length: 15)->notNull(),
                    DB::varchar(name: 'n_sort', length: 255)->notNull(),
                    DB::varchar(name: 'n_full', length: 255)->notNull(),
                    DB::varchar(name: 'n_surname', length: 255),
                    DB::varchar(name: 'n_surn', length: 255),
                    DB::varchar(name: 'n_givn', length: 255),
                    DB::varchar(name: 'n_soundex_givn_std', length: 255),
                    DB::varchar(name: 'n_soundex_surn_std', length: 255),
                    DB::varchar(name: 'n_soundex_givn_dm', length: 255),
                    DB::varchar(name: 'n_soundex_surn_dm', length: 255),
                    DB::varchar(name: 'n_list', length: 255)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['n_id', 'n_file', 'n_num']),
                indexes: [
                    DB::index(name: 'name_ix1', columns: ['n_full', 'n_id', 'n_file']),
                    DB::index(name: 'name_ix2', columns: ['n_file', 'n_surn']),
                ],
            ),
            DB::table(
                name: 'next_id',
                columns: [
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'record_type', length: 15)->notNull(),
                    DB::varchar(name: 'next_id', length: 20)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['gedcom_id', 'record_type']),
                foreign_keys: [
                    DB::foreignKey(name: 'next_id_fk1', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'other',
                columns: [
                    DB::varchar(name: 'o_id', length: 20)->notNull(),
                    DB::integer(name: 'o_file')->notNull(),
                    DB::varchar(name: 'o_type', length: 15)->notNull(),
                    DB::text(name: 'o_gedcom')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['o_id', 'o_file']),
                indexes: [
                    DB::unique(name: 'other_ux1', columns: ['o_file', 'o_id']),
                ],
            ),
            DB::table(
                name: 'placelinks',
                columns: [
                    DB::integer(name: 'pl_p_id')->notNull(),
                    DB::varchar(name: 'pl_gid', length: 20)->notNull(),
                    DB::integer(name: 'pl_file')->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['pl_p_id', 'pl_gid', 'pl_file']),
                indexes: [
                    DB::index(name: 'placelinks_ix1', columns: ['pl_p_id']),
                    DB::index(name: 'placelinks_ix2', columns: ['pl_gid']),
                    DB::index(name: 'placelinks_ix3', columns: ['pl_file']),
                ],
            ),
            DB::table(
                name: 'places',
                columns: [
                    DB::integer(name: 'p_id')->autoIncrement()->notNull(),
                    DB::varchar(name: 'p_place', length: 150),
                    DB::integer(name: 'p_parent_id'),
                    DB::integer(name: 'p_file')->notNull(),
                    DB::text(name: 'p_std_soundex'),
                    DB::text(name: 'p_dm_soundex'),
                    DB::integer(name: 'p_level'),
                ],
                primary_key: DB::primaryKey(columns: ['p_id']),
                indexes: [
                    DB::index(name: 'places_ix1', columns: ['p_place']),
                    DB::index(name: 'places_ix2', columns: ['p_level']),
                    DB::index(name: 'places_ix3', columns: ['p_parent_id']),
                    DB::index(name: 'places_ix4', columns: ['p_file']),
                ],
            ),
            DB::table(
                name: 'remotelinks',
                columns: [
                    DB::varchar(name: 'r_gid', length: 20)->notNull(),
                    DB::varchar(name: 'r_linkid', length: 255),
                    DB::integer(name: 'r_file')->notNull(),
                ],
                indexes: [
                    DB::index(name: 'remotelinks_ix1', columns: ['r_gid']),
                    DB::index(name: 'remotelinks_ix2', columns: ['r_linkid']),
                    DB::index(name: 'remotelinks_ix3', columns: ['r_file']),
                ],
            ),
            DB::table(
                name: 'site_setting',
                columns: [
                    DB::varchar(name: 'setting_name', length: 32)->notNull(),
                    DB::varchar(name: 'setting_value', length: 255)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['setting_name']),
            ),
            DB::table(
                name: 'sources',
                columns: [
                    DB::varchar(name: 's_id', length: 20)->notNull(),
                    DB::integer(name: 's_file')->notNull(),
                    DB::varchar(name: 's_name', length: 255)->notNull(),
                    DB::text(name: 's_gedcom')->notNull(),
                    DB::varchar(name: 's_dbid', length: 1),
                ],
                primary_key: DB::primaryKey(columns: ['s_id', 's_file']),
                indexes: [
                    DB::unique(name: 'sources_ux1', columns: ['s_file', 's_id']),
                    DB::index(name: 'sources_ix1', columns: ['s_name']),
                    DB::index(name: 'sources_ix2', columns: ['s_dbid']),
                ],
            ),
            DB::table(
                name: 'user',
                columns: [
                    DB::integer(name: 'user_id')->autoIncrement()->notNull(),
                    DB::varchar(name: 'user_name', length: 32)->notNull(),
                    DB::varchar(name: 'real_name', length: 64)->notNull(),
                    DB::varchar(name: 'email', length: 64)->notNull(),
                    DB::varchar(name: 'password', length: 64)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['user_id']),
                indexes: [
                    DB::unique(name: 'user_ux1', columns: ['user_name']),
                    DB::unique(name: 'user_ux2', columns: ['email']),
                ],
            ),
            DB::table(
                name: 'user_gedcom_setting',
                columns: [
                    DB::integer(name: 'user_id')->notNull(),
                    DB::integer(name: 'gedcom_id')->notNull(),
                    DB::varchar(name: 'setting_name', length: 32)->notNull(),
                    DB::varchar(name: 'setting_value', length: 255)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['user_id', 'gedcom_id', 'setting_name']),
                indexes: [
                    DB::index(name: 'user_gedcom_setting_ix1', columns: ['gedcom_id']),
                ],
                foreign_keys: [
                    DB::foreignKey(name: 'user_gedcom_setting_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id']),
                    DB::foreignKey(name: 'user_gedcom_setting_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                ],
            ),
            DB::table(
                name: 'user_setting',
                columns: [
                    DB::integer(name: 'user_id')->notNull(),
                    DB::varchar(name: 'setting_name', length: 32)->notNull(),
                    DB::varchar(name: 'setting_value', length: 255)->notNull(),
                ],
                primary_key: DB::primaryKey(columns: ['user_id', 'setting_name']),
                foreign_keys: [
                    DB::foreignKey(name: 'user_setting_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id']),
                ],
            ),
        ]);
    }

    public function version2(): Schema
    {
        return $this->version1()
            ->addTable(
                table: DB::table(
                    name: 'session',
                    columns: [
                        DB::varchar(name: 'session_id', length: 32)->notNull(),
                        DB::datetime(name: 'session_time')->notNull(),
                        DB::integer(name: 'user_id')->notNull(),
                        DB::varchar(name: 'ip_address', length: 32)->notNull(),
                        DB::text(name: 'session_data')->notNull(),
                    ],
                    primary_key: DB::primaryKey(columns: ['session_id']),
                    indexes: [
                        DB::index(name: 'session_ix1', columns: ['session_time']),
                        DB::index(name: 'session_ix2', columns: ['user_id', 'ip_address']),
                    ],
                )
            );
    }

    public function version3(): Schema
    {
        return $this->version2()
            ->alterTable(name: 'gedcom', callback: static fn (Table $table): Table => $table
                ->dropColumn(name: 'import_gedcom')
                ->dropColumn(name: 'import_offset'))
            ->addTable(
                table: DB::table(
                    name: 'gedcom_chunk',
                    columns: [
                        DB::integer(name: 'gedcom_chunk_id')->autoIncrement()->notNull(),
                        DB::integer(name: 'gedcom_id')->notNull(),
                        DB::text(name: 'chunk_data')->notNull(),
                        DB::integer(name: 'imported')->default(default: 0)->notNull(),
                    ],
                    primary_key: DB::primaryKey(columns: ['gedcom_chunk_id']),
                    indexes: [
                        DB::index(name: 'gedcom_chunk_ix1', columns: ['gedcom_id', 'imported']),
                    ],
                    foreign_keys: [
                        DB::foreignKey(name: 'gedcom_chunk_fk1', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id']),
                    ],
                )
            );
    }

    public function version4(): Schema
    {
        return $this->version3();
    }

    public function version5(): Schema
    {
        return $this->version4()
            ->alterTable(name: 'gedcom', callback: static fn (Table $table): Table => $table
                ->addColumn(column: DB::integer(name: 'sort_order')->default(default: 0)->notNull())
                ->addIndex(index: DB::index(name: 'gedcom_ix1', columns: ['sort_order'])));
    }

    public function version6(): Schema
    {
        return $this->version5();
    }

    public function version7(): Schema
    {
        return $this->version6()
            ->dropTable(name: 'remotelinks')
            ->alterTable(name: 'sources', callback: static fn (Table $table): Table => $table
                ->dropIndex(name: 'sources_ix2')
                ->dropColumn(name: 's_dbid'));
    }

    public function version8(): Schema
    {
        return $this->version7();
    }

    public function version9(): Schema
    {
        return $this->version8();
    }

    public function version10(): Schema
    {
        return $this->version9()
            ->alterTable(name: 'name', callback: static fn (Table $table): Table => $table
                ->dropIndex(name: 'name_ix2')
                ->addIndex(index: DB::index(name: 'name_ix2', columns: ['n_surn', 'n_file', 'n_type', 'n_id']))
                ->addIndex(index: DB::index(name: 'name_ix3', columns: ['n_givn', 'n_file', 'n_type', 'n_id'])));
    }

    public function version11(): Schema
    {
        return $this->version10()
            ->alterTable(name: 'user', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'password', length: 128)->notNull()));
    }

    public function version12(): Schema
    {
        return $this->version11()
            ->alterTable(name: 'name', callback: static fn (Table $table): Table => $table
                ->dropColumn(name: 'n_list'));
    }

    public function version13(): Schema
    {
        return $this->version12();
    }

    public function version14(): Schema
    {
        return $this->version13()
            ->alterTable(name: 'individuals', callback: static fn (Table $table): Table => $table
                ->dropColumn(name: 'i_isdead'));
    }

    public function version15(): Schema
    {
        return $this->version14();
    }

    public function version16(): Schema
    {
        return $this->version15()
            ->alterTable(name: 'session', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'session_id', length: 128)->notNull()));
    }

    public function version17(): Schema
    {
        return $this->version16();
    }

    public function version18(): Schema
    {
        return $this->version17()
            ->addTable(
                table: DB::table(
                    name: 'site_access_rule',
                    columns: [
                        DB::integer(name: 'site_access_rule_id')->autoIncrement()->notNull(),
                        DB::varchar(name: 'ip_address_start', length: 10)->default(default: '0')->notNull(),
                        DB::varchar(name: 'ip_address_end', length: 10)->default(default: '4294967295')->notNull(),
                        DB::varchar(name: 'user_agent_pattern', length: 255)->notNull(),
                        DB::varchar(name: 'rule', length: 7)->default(default: 'unknown')->notNull(),
                        DB::varchar(name: 'comment', length: 255)->notNull(),
                        DB::datetime(name: 'updated'),
                    ],
                    primary_key: DB::primaryKey(columns: ['site_access_rule_id']),
                    indexes: [
                        DB::unique(name: 'site_access_rule_ux1', columns: ['user_agent_pattern', 'ip_address_start', 'ip_address_end']),
                        DB::index(name: 'site_access_rule_ix1', columns: ['ip_address_start']),
                        DB::index(name: 'site_access_rule_ix2', columns: ['ip_address_end']),
                        DB::index(name: 'site_access_rule_ix3', columns: ['rule']),
                        DB::index(name: 'site_access_rule_ix4', columns: ['updated']),
                    ],
                )
            );
    }

    public function version19(): Schema
    {
        return $this->version18()
            ->alterTable(name: 'places', callback: static fn (Table $table): Table => $table
                ->dropIndex(name: 'places_ix1')
                ->dropIndex(name: 'places_ix2')
                ->dropIndex(name: 'places_ix3')
                ->dropIndex(name: 'places_ix4')
                ->dropColumn(name: 'p_level')
                ->addIndex(index: DB::index(name: 'places_ix1', columns: ['p_file', 'p_place']))
                ->addIndex(index: DB::unique(name: 'places_ux1', columns: ['p_parent_id', 'p_file', 'p_place'])));
    }

    public function version20(): Schema
    {
        return $this->version19();
    }

    public function version21(): Schema
    {
        return $this->version20()
            ->dropTable(name: 'media_mapping')
            ->replaceTable(
                table: DB::table(
                    name: 'media',
                    columns: [
                        DB::varchar(name: 'm_id', length: 20)->notNull(),
                        DB::varchar(name: 'm_ext', length: 6),
                        DB::varchar(name: 'm_type', length: 20),
                        DB::varchar(name: 'm_titl', length: 255),
                        DB::varchar(name: 'm_filename', length: 512),
                        DB::integer(name: 'm_file')->notNull(),
                        DB::text(name: 'm_gedcom'),
                    ],
                    primary_key: DB::primaryKey(columns: ['m_file', 'm_id']),
                    indexes: [
                        DB::index(name: 'media_ix1', columns: ['m_ext', 'm_type']),
                        DB::index(name: 'media_ix2', columns: ['m_titl']),
                    ],
                )
            );
    }

    public function version22(): Schema
    {
        return $this->version21();
    }

    public function version23(): Schema
    {
        return $this->version22();
    }

    public function version24(): Schema
    {
        return $this->version23()
            ->alterTable(name: 'media', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'm_ext', length: 6)->notNull())
                ->alterColumn(column: DB::varchar(name: 'm_type', length: 20)->notNull())
                ->alterColumn(column: DB::varchar(name: 'm_filename', length: 512)->notNull())
                ->alterColumn(column: DB::varchar(name: 'm_titl', length: 255)->notNull())
                ->alterColumn(column: DB::text(name: 'm_gedcom')->notNull()));
    }

    public function version25(): Schema
    {
        return $this->version24();
    }

    public function version26(): Schema
    {
        return $this->version25();
    }

    public function version27(): Schema
    {
        return $this->version26();
    }

    public function version28(): Schema
    {
        return $this->version27()
            ->alterTable(name: 'site_access_rule', callback: static fn (Table $table): Table => $table
                ->dropIndex(name: 'site_access_rule_ux1')
                ->dropIndex(name: 'site_access_rule_ix1')
                ->dropIndex(name: 'site_access_rule_ix2')
                ->dropIndex(name: 'site_access_rule_ix3')
                ->dropIndex(name: 'site_access_rule_ix4')
                ->addIndex(index: DB::unique(name: 'site_access_rule_ux1', columns: ['ip_address_end', 'ip_address_start', 'user_agent_pattern', 'rule']))
                ->addIndex(index: DB::index(name: 'site_access_rule_ix1', columns: ['rule'])));
    }

    public function version29(): Schema
    {
        return $this->version28();
    }

    public function version30(): Schema
    {
        return $this->version29()
            ->dropTable(name: 'ip_address');
    }

    public function version31(): Schema
    {
        return $this->version30();
    }

    public function version32(): Schema
    {
        return $this->version31();
    }

    public function version33(): Schema
    {
        return $this->version32()
            ->alterTable(name: 'site_setting', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'setting_value', length: 2000)->notNull()));
    }

    public function version34(): Schema
    {
        return $this->version33();
    }

    public function version35(): Schema
    {
        return $this->version34();
    }

    public function version36(): Schema
    {
        return $this->version35()
            ->alterTable(name: 'session', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'session_id', length: 256)->notNull()));
    }

    public function version37(): Schema
    {
        return $this->version36()
            ->alterTable(name: 'log', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'ip_address', length: 45)->notNull()))
            ->alterTable(name: 'message', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'ip_address', length: 45)->notNull()))
            ->alterTable(name: 'session', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'ip_address', length: 45)->notNull()))
            ->alterTable(name: 'places', callback: static fn (Table $table): Table => $table
                ->alterColumn(column: DB::varchar(name: 'p_place', length: 150)->notNull()))
            ->alterTable(name: 'default_resn', callback: static fn (Table $table): Table => $table
                ->addColumn(column: DB::varchar(name: 'comment', length: 255))
                ->addColumn(column: DB::datetime(name: 'updated')->notNull()));
    }

    public function version38(): Schema
    {
        return $this->version37()
            ->dropTable(name: 'next_id')
            ->dropTable(name: 'site_access_rule')
            ->addTable(
                table: DB::table(
                    name: 'media_file',
                    columns: [
                        DB::integer(name: 'id')->autoIncrement()->notNull(),
                        DB::varchar(name: 'm_id', length: 20)->notNull(),
                        DB::integer(name: 'm_file')->notNull(),
                        DB::varchar(name: 'multimedia_file_refn', length: 248)->notNull(),
                        DB::varchar(name: 'multimedia_format', length: 4)->notNull(),
                        DB::varchar(name: 'source_media_type', length: 15)->notNull(),
                        DB::varchar(name: 'descriptive_title', length: 248)->notNull(),
                    ],
                    primary_key: DB::primaryKey(columns: ['id']),
                    indexes: [
                        DB::index(name: 'media_file_ix1', columns: ['m_id', 'm_file']),
                        DB::index(name: 'media_file_ix2', columns: ['m_file', 'm_id']),
                        DB::index(name: 'media_file_ix3', columns: ['m_file', 'multimedia_file_refn']),
                        DB::index(name: 'media_file_ix4', columns: ['m_file', 'multimedia_format']),
                        DB::index(name: 'media_file_ix5', columns: ['m_file', 'source_media_type']),
                        DB::index(name: 'media_file_ix6', columns: ['m_file', 'descriptive_title']),
                    ],
                )
            )
            ->alterTable(
                name: 'media',
                callback: static fn (Table $table): Table => $table
                    ->dropIndex(name: 'media_ix1')
                    ->dropIndex(name: 'media_ix2')
                    ->dropColumn(name: 'm_filename')
                    ->dropColumn(name: 'm_ext')
                    ->dropColumn(name: 'm_type')
                    ->dropColumn(name: 'm_titl')
                    ->addIndex(index: DB::unique(name: 'media_ux1', columns: ['m_id', 'm_file'])),
            );
    }

    public function version39(): Schema
    {
        return $this->version38();
    }

    public function version40(): Schema
    {
        return $this->version39()
            ->addTable(
                table: DB::table(
                    name: 'favorite',
                    columns: [
                        DB::integer(name: 'favorite_id')->autoIncrement()->notNull(),
                        DB::integer(name: 'user_id'),
                        DB::integer(name: 'gedcom_id')->notNull(),
                        DB::varchar(name: 'xref', length: 20),
                        DB::varchar(name: 'favorite_type', length: 4)->notNull(),
                        DB::varchar(name: 'url', length: 255),
                        DB::varchar(name: 'title', length: 255),
                        DB::varchar(name: 'note', length: 1000),
                    ],
                    primary_key: DB::primaryKey(columns: ['favorite_id']),
                    indexes: [
                        DB::index(name: 'favorite_ix1', columns: ['user_id']),
                        DB::index(name: 'favorite_ix2', columns: ['gedcom_id', 'user_id']),
                    ],
                    foreign_keys: [
                        DB::foreignKey(name: 'favorite_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id'])->onDeleteCascade(),
                        DB::foreignKey(name: 'favorite_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade(),
                    ],
                )
            );
    }

    public function version41(): Schema
    {
        return $this->version40()
            ->addTable(
                table: DB::table(
                    name: 'news',
                    columns: [
                        DB::integer(name: 'news_id')->autoIncrement()->notNull(),
                        DB::integer(name: 'user_id'),
                        DB::integer(name: 'gedcom_id'),
                        DB::varchar(name: 'subject', length: 255)->notNull(),
                        DB::text(name: 'body')->notNull(),
                        DB::datetime(name: 'updated')->notNull(),
                    ],
                    primary_key: DB::primaryKey(columns: ['news_id']),
                    indexes: [
                        DB::index(name: 'news_ix1', columns: ['user_id', 'updated']),
                        DB::index(name: 'news_ix2', columns: ['gedcom_id', 'updated']),
                    ],
                    foreign_keys: [
                        DB::foreignKey(name: 'news_fk1', columns: ['user_id'], foreign_table: 'user', foreign_columns: ['user_id'])->onDeleteCascade(),
                        DB::foreignKey(name: 'news_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade(),
                    ],
                )
            );
    }

    public function version42(): Schema
    {
        return $this->version41()
            ->alterTable(
                name: 'module',
                callback: static fn (Table $table): Table => $table
                    ->addColumn(column: DB::integer(name: 'footer_order')),
            );
    }

    /**
     * The temporary indexes support MySQL's auto-increment and foreign key constraints during the primary-key swap.
     */
    public function version43a(): Schema
    {
        return $this->version42()
            ->alterTable(
                name: 'module_privacy',
                callback: static fn (Table $table): Table => $table
                    ->addColumn(column: DB::integer(name: 'id'))
                    ->addColumn(column: DB::varchar(name: 'interface', length: 255))
                    ->addIndex(index: DB::unique(name: 'module_privacy_ux2', columns: ['id']))
                    ->addIndex(index: DB::index(name: 'module_privacy_ix1', columns: ['module_name'])),
            );
    }

    public function version43b(): Schema
    {
        return $this->version43a()
            ->alterTable(
                name: 'module_privacy',
                callback: static fn (Table $table): Table => $table
                    ->alterColumn(column: DB::integer(name: 'id')->autoIncrement()->notNull())
                    ->alterColumn(column: DB::varchar(name: 'interface', length: 255)->notNull())
                    ->setPrimaryKey(primary_key: DB::primaryKey(columns: ['id'])),
            );
    }

    public function version43c(): Schema
    {
        return $this->version43b()
            ->alterTable(
                name: 'module_privacy',
                callback: static fn (Table $table): Table => $table
                    ->dropForeignKey(name: 'module_privacy_fk1')
                    ->dropForeignKey(name: 'module_privacy_fk2')
                    ->dropIndex(name: 'module_privacy_ux1')
                    ->dropIndex(name: 'module_privacy_ux2')
                    ->dropIndex(name: 'module_privacy_ix1')
                    ->dropColumn(name: 'component')
                    ->addIndex(index: DB::unique(name: 'module_privacy_ux1', columns: ['gedcom_id', 'module_name', 'interface']))
                    ->addIndex(index: DB::unique(name: 'module_privacy_ux2', columns: ['module_name', 'gedcom_id', 'interface']))
                    ->addForeignKey(foreign_key: DB::foreignKey(name: 'module_privacy_fk1', columns: ['module_name'], foreign_table: 'module', foreign_columns: ['module_name'])->onDeleteCascade())
                    ->addForeignKey(foreign_key: DB::foreignKey(name: 'module_privacy_fk2', columns: ['gedcom_id'], foreign_table: 'gedcom', foreign_columns: ['gedcom_id'])->onDeleteCascade()),
            );
    }

    public function version44(): Schema
    {
        return $this->version43c();
    }

    public function version45(): Schema
    {
        return $this->version44()
            ->addTable(
                table: DB::table(
                    name: 'place_location',
                    columns: [
                        DB::integer(name: 'id')->autoIncrement()->notNull(),
                        DB::integer(name: 'parent_id'),
                        DB::varchar(name: 'place', length: 120)->notNull(),
                        DB::float(name: 'latitude'),
                        DB::float(name: 'longitude'),
                    ],
                    primary_key: DB::primaryKey(columns: ['id']),
                    indexes: [
                        DB::unique(name: 'place_location_ux1', columns: ['parent_id', 'place']),
                        DB::unique(name: 'place_location_ux2', columns: ['place', 'parent_id']),
                        DB::index(name: 'place_location_ix1', columns: ['latitude']),
                        DB::index(name: 'place_location_ix2', columns: ['longitude']),
                    ],
                )
            );
    }

    public function version46(): Schema
    {
        return $this->version45()
            ->alterTable(
                name: 'gedcom',
                callback: static fn (Table $table): Table => $table
                    ->addColumn(column: DB::varchar(name: 'media_folder', length: 255)->default(default: 'media/')->notNull())
                    ->addColumn(column: DB::varchar(name: 'title', length: 255)->default(default: 'tree')->notNull())
                    ->addColumn(column: DB::varchar(name: 'gedcom_filename', length: 255)->default(default: 'tree.ged')->notNull())
                    ->addColumn(column: DB::integer(name: 'imported')->default(default: 1)->notNull())
                    ->addColumn(column: DB::integer(name: 'private')->default(default: 0)->notNull())
                    ->addColumn(column: DB::integer(name: 'contact_user_id'))
                    ->addColumn(column: DB::integer(name: 'support_user_id'))
                    ->addIndex(index: DB::index(name: 'gedcom_ix2', columns: ['media_folder']))
                    ->addIndex(index: DB::index(name: 'gedcom_ix3', columns: ['title']))
                    ->addIndex(index: DB::index(name: 'gedcom_ix4', columns: ['gedcom_filename']))
                    ->addIndex(index: DB::index(name: 'gedcom_ix5', columns: ['imported']))
                    ->addIndex(index: DB::index(name: 'gedcom_ix6', columns: ['private']))
                    ->addIndex(index: DB::index(name: 'gedcom_ix7', columns: ['contact_user_id']))
                    ->addIndex(index: DB::index(name: 'gedcom_ix8', columns: ['support_user_id']))
                    ->addForeignKey(foreign_key: DB::foreignKey(name: 'gedcom_fk1', columns: ['contact_user_id'], foreign_table: 'user', foreign_columns: ['user_id']))
                    ->addForeignKey(foreign_key: DB::foreignKey(name: 'gedcom_fk2', columns: ['support_user_id'], foreign_table: 'user', foreign_columns: ['user_id'])),
            );
    }

    public function version47(): Schema
    {
        return $this->version46()
            ->alterTable(name: 'change', callback: static fn (Table $table): Table => $table
                ->addIndex(index: DB::index(name: 'change_ix3', columns: ['status']))
                ->addIndex(index: DB::index(name: 'change_ix4', columns: ['xref']))
                ->addIndex(index: DB::index(name: 'change_ix5', columns: ['change_time']))
                ->addIndex(index: DB::index(name: 'change_ix6', columns: ['gedcom_id', 'change_time']))
                ->addIndex(index: DB::index(name: 'change_ix7', columns: ['gedcom_id', 'status', 'change_id'])));
    }
}
