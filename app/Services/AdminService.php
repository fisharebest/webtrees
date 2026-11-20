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

use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\ChangeStatus;
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
use Fisharebest\Collection\Collection;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;

use function array_map;
use function array_unique;
use function explode;
use function fclose;
use function fread;
use function implode;
use function preg_match;
use function sort;

/**
 * Utilities for the control panel.
 */
class AdminService
{
    /**
     * Count of XREFs used by two trees at the same time.
     */
    public function countCommonXrefs(Tree $tree1, Tree $tree2): int
    {
        $subquery1 = DB::query()
            ->select('xref AS xref1')
            ->from('change')
            ->whereEqual('gedcom_id', $tree1->id())
            ->whereEqual('status', ChangeStatus::Pending->value)
            ->union(DB::query()
                ->select('i_id AS xref')
                ->from('individuals')
                ->whereEqual('i_file', $tree1->id()))
            ->union(DB::query()
                ->select('f_id AS xref')
                ->from('families')
                ->whereEqual('f_file', $tree1->id()))
            ->union(DB::query()
                ->select('s_id AS xref')
                ->from('sources')
                ->whereEqual('s_file', $tree1->id()))
            ->union(DB::query()
                ->select('m_id AS xref')
                ->from('media')
                ->whereEqual('m_file', $tree1->id()))
            ->union(DB::query()
                ->select('o_id AS xref')
                ->from('other')
                ->whereEqual('o_file', $tree1->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR']));

        $subquery2 = DB::query()
            ->select('xref AS xref2')
            ->from('change')
            ->whereEqual('gedcom_id', $tree2->id())
            ->whereEqual('status', ChangeStatus::Pending->value)
            ->union(DB::query()
                ->select('i_id AS xref')
                ->from('individuals')
                ->whereEqual('i_file', $tree2->id()))
            ->union(DB::query()
                ->select('f_id AS xref')
                ->from('families')
                ->whereEqual('f_file', $tree2->id()))
            ->union(DB::query()
                ->select('s_id AS xref')
                ->from('sources')
                ->whereEqual('s_file', $tree2->id()))
            ->union(DB::query()
                ->select('m_id AS xref')
                ->from('media')
                ->whereEqual('m_file', $tree2->id()))
            ->union(DB::query()
                ->select('o_id AS xref')
                ->from('other')
                ->whereEqual('o_file', $tree2->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR']));

        return DB::query()
            ->withCte('sub1', $subquery1)
            ->withCte('sub2', $subquery2)
            ->from('sub1')
            ->join('sub2', ['sub1.xref1' => 'sub2.xref2'])
            ->count();
    }

    /**
     *
     * @return array<string,array<int,array<int,GedcomRecord>>>
     */
    public function duplicateRecords(Tree $tree): array
    {
        // We can't do any reasonable checks using MySQL.
        // Will need to wait for a "repositories" table.
        $repositories = [];

        $sources = DB::query()
            ->from('sources')
            ->whereEqual('s_file', $tree->id())
            ->groupBy('s_name')
            ->havingRaw('COUNT(s_id) > ?', [1])
            ->orderBy(DB::ascending('xrefs'))
            ->pluck(DB::groupConcat('s_id'), in: Collection::class)
            ->map(static fn (string $xrefs): array => array_map(static fn (string $xref): Source => Registry::sourceFactory()->make($xref, $tree), explode(',', $xrefs)))
            ->array();

        // Database agnostic way to do GROUP_CONCAT(DISTINCT x ORDER BY x)
        $distinct_order_by = static function (string $xrefs): string {
            $array = explode(',', $xrefs);
            sort($array);

            return implode(',', array_unique($array));
        };

        $individuals = DB::query()
            ->from('dates')
            ->join('name', ['d_file' => 'n_file', 'd_gid' => 'n_id'])
            ->whereEqual('d_file', $tree->id())
            ->whereIn('d_fact', ['BIRT', 'CHR', 'BAPM', 'DEAT', 'BURI'])
            ->groupBy('d_year', 'd_month', 'd_day', 'd_type', 'd_fact', 'n_type', 'n_full')
            ->havingRaw('COUNT(DISTINCT d_gid) > ?', [1])
            ->orderBy(DB::ascending('xrefs'))
            ->pluck(DB::groupConcat('d_gid'), in: Collection::class)
            ->map($distinct_order_by)
            ->unique()
            ->map(static fn (string $xrefs): array => array_map(static fn (string $xref): Individual => Registry::individualFactory()->make($xref, $tree), explode(',', $xrefs)))
            ->array();

        $families = DB::query()
            ->from('families')
            ->whereEqual('f_file', $tree->id())
            ->whereNested(static fn (QueryBuilder $query): QueryBuilder => $query
                ->whereNotEqual('f_husb', '')
                ->orWhereNotEqual('f_wife', ''))
            ->groupBy(
                DB::least(DB::column('f_husb'), DB::column('f_wife')),
                DB::greatest(DB::column('f_husb'), DB::column('f_wife')),
            )
            ->havingRaw('COUNT(f_id) > ?', [1])
            ->orderBy(DB::ascending('xrefs'))
            ->pluck(DB::groupConcat('f_id'), in: Collection::class)
            ->map(static fn (string $xrefs): array => array_map(static fn (string $xref): Family => Registry::familyFactory()->make($xref, $tree), explode(',', $xrefs)))
            ->array();

        $media = DB::query()
            ->from('media_file')
            ->whereEqual('m_file', $tree->id())
            ->whereNotEqual('descriptive_title', '')
            ->groupBy('descriptive_title')
            ->havingRaw('COUNT(DISTINCT m_id) > ?', [1])
            ->orderBy(DB::ascending('xrefs'))
            ->pluck(DB::groupConcat('m_id'), in: Collection::class)
            ->map(static fn (string $xrefs): array => array_map(static fn (string $xref): Media => Registry::mediaFactory()->make($xref, $tree), explode(',', $xrefs)))
            ->array();

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
     * @return array<string>
     */
    public function duplicateXrefs(Tree $tree): array
    {
        $subquery1 = DB::query()
            ->select('i_id AS xref', DB::alias(DB::value('INDI'), 'type'))
            ->from('individuals')
            ->whereEqual('i_file', $tree->id())
            ->union(DB::query()
                ->select('f_id AS xref', DB::alias(DB::value('FAM'), 'type'))
                ->from('families')
                ->whereEqual('f_file', $tree->id()))
            ->union(DB::query()
                ->select('s_id AS xref', DB::alias(DB::value('SOUR'), 'type'))
                ->from('sources')
                ->whereEqual('s_file', $tree->id()))
            ->union(DB::query()
                ->select('m_id AS xref', DB::alias(DB::value('OBJE'), 'type'))
                ->from('media')
                ->whereEqual('m_file', $tree->id()))
            ->union(DB::query()
                ->select('o_id AS xref', 'o_type AS type')
                ->from('other')
                ->whereEqual('o_file', $tree->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR']));

        $subquery2 = DB::query()
            ->select('xref AS other_xref')
            ->from('change')
            ->whereNotEqual('gedcom_id', $tree->id())
            ->union(DB::query()
                ->select('i_id AS xref')
                ->from('individuals')
                ->whereNotEqual('i_file', $tree->id()))
            ->union(DB::query()
                ->select('f_id AS xref')
                ->from('families')
                ->whereNotEqual('f_file', $tree->id()))
            ->union(DB::query()
                ->select('s_id AS xref')
                ->from('sources')
                ->whereNotEqual('s_file', $tree->id()))
            ->union(DB::query()
                ->select('m_id AS xref')
                ->from('media')
                ->whereNotEqual('m_file', $tree->id()))
            ->union(DB::query()
                ->select('o_id AS xref')
                ->from('other')
                ->whereNotEqual('o_file', $tree->id())
                ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR']));

        return DB::query()
            ->withCte('sub1', $subquery1)
            ->withCte('sub2', $subquery2)
            ->from('sub1')
            ->join('sub2', ['sub2.other_xref' => 'sub1.xref'])
            ->pluck('sub1.type', 'sub1.xref');
    }

    /**
     * A list of GEDCOM files in the data folder.
     *
     * @return Collection<string>
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
                ->map(fn (StorageAttributes $attributes) => $attributes->path())
                ->toArray();
        } catch (FilesystemException) {
            $files = [];
        }

        return (new Collection($files))->sort();
    }

    /**
     * Change the behaviour a little, when there are a lot of trees.
     */
    public function multipleTreeThreshold(): int
    {
        return (int) Site::getPreference('MULTIPLE_TREE_THRESHOLD');
    }
}
