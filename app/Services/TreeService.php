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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use stdClass;

use function app;

/**
 * Tree management and queries.
 */
class TreeService
{
    // The most likely surname tradition for a given language.
    private const DEFAULT_SURNAME_TRADITIONS = [
        'es'    => 'spanish',
        'is'    => 'icelandic',
        'lt'    => 'lithuanian',
        'pl'    => 'polish',
        'pt'    => 'portuguese',
        'pt-BR' => 'portuguese',
    ];

    /**
     * All the trees that the current user has permission to access.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return app('cache.array')->rememberForever(__CLASS__ . __METHOD__, static function (): Collection {
            // All trees
            $query = DB::table('gedcom')
                ->leftJoin('gedcom_setting', static function (JoinClause $join): void {
                    $join->on('gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                        ->where('gedcom_setting.setting_name', '=', 'title');
                })
                ->where('gedcom.gedcom_id', '>', 0)
                ->select([
                    'gedcom.gedcom_id AS tree_id',
                    'gedcom.gedcom_name AS tree_name',
                    'gedcom_setting.setting_value AS tree_title',
                ])
                ->orderBy('gedcom.sort_order')
                ->orderBy('gedcom_setting.setting_value');

            // Non-admins may not see all trees
            if (!Auth::isAdmin()) {
                $query
                    ->join('gedcom_setting AS gs2', static function (JoinClause $join): void {
                        $join->on('gs2.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('gs2.setting_name', '=', 'imported');
                    })
                    ->join('gedcom_setting AS gs3', static function (JoinClause $join): void {
                        $join->on('gs3.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('gs3.setting_name', '=', 'REQUIRE_AUTHENTICATION');
                    })
                    ->leftJoin('user_gedcom_setting', static function (JoinClause $join): void {
                        $join->on('user_gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('user_gedcom_setting.user_id', '=', Auth::id())
                            ->where('user_gedcom_setting.setting_name', '=', 'canedit');
                    })
                    ->where(static function (Builder $query): void {
                        $query
                            // Managers
                            ->where('user_gedcom_setting.setting_value', '=', 'admin')
                            // Members
                            ->orWhere(static function (Builder $query): void {
                                $query
                                    ->where('gs2.setting_value', '=', '1')
                                    ->where('gs3.setting_value', '=', '1')
                                    ->where('user_gedcom_setting.setting_value', '<>', 'none');
                            })
                            // Public trees
                            ->orWhere(static function (Builder $query): void {
                                $query
                                    ->where('gs2.setting_value', '=', '1')
                                    ->where('gs3.setting_value', '<>', '1');
                            });
                    });
            }

            return $query
                ->get()
                ->mapWithKeys(static function (stdClass $row): array {
                    return [$row->tree_id => Tree::rowMapper()($row)];
                });
        });
    }

    /**
     * Find the tree with a specific name.
     *
     * @param string $name
     *
     * @return Tree|null
     */
    public function findByName($name): ?Tree
    {
        return $this->all()->first(static function (Tree $tree) use ($name): bool {
            return $tree->name() === $name;
        });
    }

    /**
     * @param string $name
     * @param string $title
     *
     * @return Tree
     */
    public function create(string $name, string $title): Tree
    {
        DB::table('gedcom')->insert([
            'gedcom_name' => $name,
        ]);

        $tree_id = (int) DB::connection()->getPdo()->lastInsertId();

        $tree = new Tree($tree_id, $name, $title);

        $tree->setPreference('imported', '1');
        $tree->setPreference('title', $title);

        // Set preferences from default tree
        (new Builder(DB::connection()))->from('gedcom_setting')->insertUsing(
            ['gedcom_id', 'setting_name', 'setting_value'],
            static function (Builder $query) use ($tree_id): void {
                $query
                    ->select([new Expression($tree_id), 'setting_name', 'setting_value'])
                    ->from('gedcom_setting')
                    ->where('gedcom_id', '=', -1);
            }
        );

        (new Builder(DB::connection()))->from('default_resn')->insertUsing(
            ['gedcom_id', 'tag_type', 'resn'],
            static function (Builder $query) use ($tree_id): void {
                $query
                    ->select([new Expression($tree_id), 'tag_type', 'resn'])
                    ->from('default_resn')
                    ->where('gedcom_id', '=', -1);
            }
        );

        // Gedcom and privacy settings
        $tree->setPreference('CONTACT_USER_ID', (string) Auth::id());
        $tree->setPreference('WEBMASTER_USER_ID', (string) Auth::id());
        $tree->setPreference('LANGUAGE', WT_LOCALE); // Default to the current admin’s language
        $tree->setPreference('SURNAME_TRADITION', self::DEFAULT_SURNAME_TRADITIONS[WT_LOCALE] ?? 'paternal');

        // A tree needs at least one record.
        $head = "0 HEAD\n1 SOUR webtrees\n2 DEST webtrees\n1 GEDC\n2 VERS 5.5.1\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8";
        FunctionsImport::importRecord($head, $tree, true);

        // I18N: This should be a common/default/placeholder name of an individual. Put slashes around the surname.
        $name = I18N::translate('John /DOE/');
        $note = I18N::translate('Edit this individual and replace their details with your own.');
        $indi = "0 @X1@ INDI\n1 NAME " . $name . "\n1 SEX M\n1 BIRT\n2 DATE 01 JAN 1850\n2 NOTE " . $note;
        FunctionsImport::importRecord($indi, $tree, true);

        return $tree;
    }

    /**
     * @param Tree $tree
     */
    public function delete(Tree $tree): void
    {
        // If this is the default tree, then unset it
        if (Site::getPreference('DEFAULT_GEDCOM') === $tree->name()) {
            Site::setPreference('DEFAULT_GEDCOM', '');
        }

        $tree->deleteGenealogyData(false);

        DB::table('block_setting')
            ->join('block', 'block.block_id', '=', 'block_setting.block_id')
            ->where('gedcom_id', '=', $tree->id())
            ->delete();
        DB::table('block')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('user_gedcom_setting')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('gedcom_setting')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('module_privacy')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('hit_counter')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('default_resn')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('gedcom_chunk')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('log')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('gedcom')->where('gedcom_id', '=', $tree->id())->delete();
    }

    /**
     * Generate a unique name for a new tree.
     *
     * @return string
     */
    public function uniqueTreeName(): string
    {
        $name   = 'tree';
        $number = 1;

        while ($this->findByName($name . $number) instanceof Tree) {
            $number++;
        }

        return $name . $number;
    }
}
