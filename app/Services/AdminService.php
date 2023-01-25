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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;

use function array_map;
use function explode;
use function fclose;
use function fread;
use function preg_match;

/**
 * Utilities for the control panel.
 */
class AdminService
{
    /**
     * Count of XREFs used by two trees at the same time.
     *
     * @param Tree $tree1
     * @param Tree $tree2
     *
     * @return int
     */
    public function countCommonXrefs(Tree $tree1, Tree $tree2): int
    {
        $subquery1 = DB::table('individuals')
            ->where('i_file', '=', $tree1->id())
            ->select(['i_id AS xref'])
            ->union(DB::table('families')
                ->where('f_file', '=', $tree1->id())
                ->select(['f_id AS xref']))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree1->id())
                ->select(['s_id AS xref']))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree1->id())
                ->select(['m_id AS xref']))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree1->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                ->select(['o_id AS xref']));

        $subquery2 = DB::table('change')
            ->where('gedcom_id', '=', $tree2->id())
            ->select(['xref AS other_xref'])
            ->union(DB::table('individuals')
                ->where('i_file', '=', $tree2->id())
                ->select(['i_id AS xref']))
            ->union(DB::table('families')
                ->where('f_file', '=', $tree2->id())
                ->select(['f_id AS xref']))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree2->id())
                ->select(['s_id AS xref']))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree2->id())
                ->select(['m_id AS xref']))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree2->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                ->select(['o_id AS xref']));

        return DB::table(new Expression('(' . $subquery1->toSql() . ') AS sub1'))
            ->mergeBindings($subquery1)
            ->joinSub($subquery2, 'sub2', 'other_xref', '=', 'xref')
            ->count();
    }

    /**
     * @param Tree $tree
     *
     * @return array<string,array<int,array<int,GedcomRecord>>>
     */
    public function duplicateRecords(Tree $tree): array
    {
        // We can't do any reasonable checks using MySQL.
        // Will need to wait for a "repositories" table.
        $repositories = [];

        $sources = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->groupBy(['s_name'])
            ->having(new Expression('COUNT(s_id)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(s_id) AS xrefs')])
            ->orderBy('xrefs')
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Source {
                    return Registry::sourceFactory()->make($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $individuals = DB::table('dates')
            ->join('name', static function (JoinClause $join): void {
                $join
                    ->on('d_file', '=', 'n_file')
                    ->on('d_gid', '=', 'n_id');
            })
            ->where('d_file', '=', $tree->id())
            ->whereIn('d_fact', ['BIRT', 'CHR', 'BAPM', 'DEAT', 'BURI'])
            ->groupBy(['d_year', 'd_month', 'd_day', 'd_type', 'd_fact', 'n_type', 'n_full'])
            ->having(new Expression('COUNT(DISTINCT d_gid)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(DISTINCT d_gid ORDER BY d_gid) AS xrefs')])
            ->distinct()
            ->orderBy('xrefs')
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Individual {
                    return Registry::individualFactory()->make($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $families = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->groupBy([new Expression('LEAST(f_husb, f_wife)')])
            ->groupBy([new Expression('GREATEST(f_husb, f_wife)')])
            ->having(new Expression('COUNT(f_id)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(f_id) AS xrefs')])
            ->orderBy('xrefs')
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Family {
                    return Registry::familyFactory()->make($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        $media = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('descriptive_title', '<>', '')
            ->groupBy(['descriptive_title'])
            ->having(new Expression('COUNT(DISTINCT m_id)'), '>', '1')
            ->select([new Expression('GROUP_CONCAT(m_id) AS xrefs')])
            ->orderBy('xrefs')
            ->pluck('xrefs')
            ->map(static function (string $xrefs) use ($tree): array {
                return array_map(static function (string $xref) use ($tree): Media {
                    return Registry::mediaFactory()->make($xref, $tree);
                }, explode(',', $xrefs));
            })
            ->all();

        return [
            I18N::translate('Repositories')  => $repositories,
            I18N::translate('Sources')       => $sources,
            I18N::translate('Individuals')   => $individuals,
            I18N::translate('Families')      => $families,
            I18N::translate('Media objects') => $media,
        ];
    }

    /**
     * Every XREF used by this tree and also used by some other tree
     *
     * @param Tree $tree
     *
     * @return array<string>
     */
    public function duplicateXrefs(Tree $tree): array
    {
        $subquery1 = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->select(['i_id AS xref', new Expression("'INDI' AS type")])
            ->union(DB::table('families')
                ->where('f_file', '=', $tree->id())
                ->select(['f_id AS xref', new Expression("'FAM' AS type")]))
            ->union(DB::table('sources')
                ->where('s_file', '=', $tree->id())
                ->select(['s_id AS xref', new Expression("'SOUR' AS type")]))
            ->union(DB::table('media')
                ->where('m_file', '=', $tree->id())
                ->select(['m_id AS xref', new Expression("'OBJE' AS type")]))
            ->union(DB::table('other')
                ->where('o_file', '=', $tree->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                ->select(['o_id AS xref', 'o_type AS type']));

        $subquery2 = DB::table('change')
            ->where('gedcom_id', '<>', $tree->id())
            ->select(['xref AS other_xref'])
            ->union(DB::table('individuals')
                ->where('i_file', '<>', $tree->id())
                ->select(['i_id AS xref']))
            ->union(DB::table('families')
                ->where('f_file', '<>', $tree->id())
                ->select(['f_id AS xref']))
            ->union(DB::table('sources')
                ->where('s_file', '<>', $tree->id())
                ->select(['s_id AS xref']))
            ->union(DB::table('media')
                ->where('m_file', '<>', $tree->id())
                ->select(['m_id AS xref']))
            ->union(DB::table('other')
                ->where('o_file', '<>', $tree->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                ->select(['o_id AS xref']));

        return DB::query()
            ->fromSub($subquery1, 'sub1')
            ->joinSub($subquery2, 'sub2', 'other_xref', '=', 'xref')
            ->pluck('type', 'xref')
            ->all();
    }

    /**
     * A list of GEDCOM files in the data folder.
     *
     * @param FilesystemOperator $filesystem
     *
     * @return Collection<int,string>
     */
    public function gedcomFiles(FilesystemOperator $filesystem): Collection
    {
        try {
            $files = $filesystem->listContents('')
                ->filter(static function (StorageAttributes $attributes) use ($filesystem) {
                    if (!$attributes->isFile()) {
                        return false;
                    }

                    $stream = $filesystem->readStream($attributes->path());

                    $header = fread($stream, 10);
                    fclose($stream);

                    return preg_match('/^(' . UTF8::BYTE_ORDER_MARK . ')?0 HEAD/', $header) > 0;
                })
                ->map(function (StorageAttributes $attributes) {
                    return $attributes->path();
                })
                ->toArray();
        } catch (FilesystemException $ex) {
            $files = [];
        }

        return Collection::make($files)->sort();
    }

    /**
     * Change the behaviour a little, when there are a lot of trees.
     *
     * @return int
     */
    public function multipleTreeThreshold(): int
    {
        return (int) Site::getPreference('MULTIPLE_TREE_THRESHOLD');
    }
}
