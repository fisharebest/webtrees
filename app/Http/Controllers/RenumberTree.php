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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;

use function e;
use function redirect;
use function route;

final class RenumberTree
{
    use ViewResponseTrait;

    public function __construct(
        private AdminService $admin_service,
        private TimeoutService $timeout_service,
    ) {
    }

    public function get(Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $xrefs = $this->admin_service->duplicateXrefs($tree);

        /* I18N: Renumber the records in a family tree */
        $title = I18N::translate('Renumber XREFs') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-renumber', [
            'title' => $title,
            'tree'  => $tree,
            'xrefs' => $xrefs,
        ]);
    }

    private function linkedRecordsQuery(
        string $table,
        string $file_column,
        string $xref_column,
        Tree $tree,
        string $old_xref,
        string $tag,
    ): QueryBuilder {
        return DB::query(table: $table)
            ->whereEqual($file_column, $tree->id())
            ->whereExists(DB::query()
                ->select('*')
                ->from('link')
                ->whereEqual('l_file', DB::column($table . '.' . $file_column))
                ->whereEqual('l_from', DB::column($table . '.' . $xref_column))
                ->whereEqual('l_to', $old_xref)
                ->whereEqual('l_type', $tag));
    }

    public function post(Tree $tree): ResponseInterface
    {
        $xrefs = $this->admin_service->duplicateXrefs($tree);

        if ($xrefs !== [] && $tree->hasPendingEdit()) {
            $message =
                I18N::translate('You need to accept or reject all pending changes before proceeding.') .
                ' <a href="' . e(route(PendingChanges::class, ['tree' => $tree->name()])) . '">' .
                I18N::translate('Show pending changes') .
                '</a>';
            FlashMessages::addMessage($message, 'danger');

            return redirect(route(self::class, ['tree' => $tree->name()]));
        }

        foreach ($xrefs as $old_xref => $type) {
            $new_xref = Registry::xrefFactory()->make($type);
            switch ($type) {
                case Individual::RECORD_TYPE:
                    DB::query(table: 'individuals')
                        ->whereEqual('i_file', $tree->id())
                        ->whereEqual('i_id', $old_xref)
                        ->update([
                            'i_id'     => $new_xref,
                            'i_gedcom' => DB::replace('i_gedcom', "0 @$old_xref@ INDI", "0 @$new_xref@ INDI"),
                        ]);

                    DB::query(table: 'families')
                        ->whereEqual('f_husb', $old_xref)
                        ->whereEqual('f_file', $tree->id())
                        ->update([
                            'f_husb'   => $new_xref,
                            'f_gedcom' => DB::replace('f_gedcom', " HUSB @$old_xref@", " HUSB @$new_xref@"),
                        ]);

                    DB::query(table: 'families')
                        ->whereEqual('f_wife', $old_xref)
                        ->whereEqual('f_file', $tree->id())
                        ->update([
                            'f_wife'   => $new_xref,
                            'f_gedcom' => DB::replace('f_gedcom', " WIFE @$old_xref@", " WIFE @$new_xref@"),
                        ]);

                    // Other links from families to individuals
                    foreach (['CHIL', 'ASSO', '_ASSO'] as $tag) {
                        $this->linkedRecordsQuery('families', 'f_file', 'f_id', $tree, $old_xref, $tag)
                            ->update([
                                'f_gedcom' => DB::replace('f_gedcom', " $tag @$old_xref@", " $tag @$new_xref@"),
                            ]);
                    }

                    // Links from individuals to individuals
                    foreach (['ALIA', 'ASSO', '_ASSO'] as $tag) {
                        $this->linkedRecordsQuery('individuals', 'i_file', 'i_id', $tree, $old_xref, $tag)
                            ->update([
                                'i_gedcom' => DB::replace('i_gedcom', " $tag @$old_xref@", " $tag @$new_xref@"),
                            ]);
                    }

                    DB::query(table: 'placelinks')
                        ->whereEqual('pl_file', $tree->id())
                        ->whereEqual('pl_gid', $old_xref)
                        ->update([
                            'pl_gid' => $new_xref,
                        ]);

                    DB::query(table: 'dates')
                        ->whereEqual('d_file', $tree->id())
                        ->whereEqual('d_gid', $old_xref)
                        ->update([
                            'd_gid' => $new_xref,
                        ]);

                    DB::query(table: 'user_gedcom_setting')
                        ->whereEqual('gedcom_id', $tree->id())
                        ->whereEqual('setting_value', $old_xref)
                        ->whereIn('setting_name', [UserInterface::PREF_TREE_ACCOUNT_XREF, UserInterface::PREF_TREE_DEFAULT_XREF])
                        ->update([
                            'setting_value' => $new_xref,
                        ]);
                    break;

                case Family::RECORD_TYPE:
                    DB::query(table: 'families')
                        ->whereEqual('f_file', $tree->id())
                        ->whereEqual('f_id', $old_xref)
                        ->update([
                            'f_id'     => $new_xref,
                            'f_gedcom' => DB::replace('f_gedcom', "0 @$old_xref@ FAM", "0 @$new_xref@ FAM"),
                        ]);

                    // Links from individuals to families
                    foreach (['FAMC', 'FAMS'] as $tag) {
                        $this->linkedRecordsQuery('individuals', 'i_file', 'i_id', $tree, $old_xref, $tag)
                            ->update([
                                'i_gedcom' => DB::replace('i_gedcom', " $tag @$old_xref@", " $tag @$new_xref@"),
                            ]);
                    }

                    DB::query(table: 'placelinks')
                        ->whereEqual('pl_file', $tree->id())
                        ->whereEqual('pl_gid', $old_xref)
                        ->update([
                            'pl_gid' => $new_xref,
                        ]);

                    DB::query(table: 'dates')
                        ->whereEqual('d_file', $tree->id())
                        ->whereEqual('d_gid', $old_xref)
                        ->update([
                            'd_gid' => $new_xref,
                        ]);
                    break;

                case Source::RECORD_TYPE:
                    DB::query(table: 'sources')
                        ->whereEqual('s_file', $tree->id())
                        ->whereEqual('s_id', $old_xref)
                        ->update([
                            's_id'     => $new_xref,
                            's_gedcom' => DB::replace('s_gedcom', "0 @$old_xref@ SOUR", "0 @$new_xref@ SOUR"),
                        ]);

                    $this->linkedRecordsQuery('individuals', 'i_file', 'i_id', $tree, $old_xref, 'SOUR')
                        ->update([
                            'i_gedcom' => DB::replace('i_gedcom', " SOUR @$old_xref@", " SOUR @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('families', 'f_file', 'f_id', $tree, $old_xref, 'SOUR')
                        ->update([
                            'f_gedcom' => DB::replace('f_gedcom', " SOUR @$old_xref@", " SOUR @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('media', 'm_file', 'm_id', $tree, $old_xref, 'SOUR')
                        ->update([
                            'm_gedcom' => DB::replace('m_gedcom', " SOUR @$old_xref@", " SOUR @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('other', 'o_file', 'o_id', $tree, $old_xref, 'SOUR')
                        ->update([
                            'o_gedcom' => DB::replace('o_gedcom', " SOUR @$old_xref@", " SOUR @$new_xref@"),
                        ]);
                    break;

                case Repository::RECORD_TYPE:
                    DB::query(table: 'other')
                        ->whereEqual('o_file', $tree->id())
                        ->whereEqual('o_id', $old_xref)
                        ->whereEqual('o_type', 'REPO')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => DB::replace('o_gedcom', "0 @$old_xref@ REPO", "0 @$new_xref@ REPO"),
                        ]);

                    $this->linkedRecordsQuery('sources', 's_file', 's_id', $tree, $old_xref, 'REPO')
                        ->update([
                            's_gedcom' => DB::replace('s_gedcom', " REPO @$old_xref@", " REPO @$new_xref@"),
                        ]);
                    break;

                case Note::RECORD_TYPE:
                    DB::query(table: 'other')
                        ->whereEqual('o_file', $tree->id())
                        ->whereEqual('o_id', $old_xref)
                        ->whereEqual('o_type', 'NOTE')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => DB::replace('o_gedcom', "0 @$old_xref@ NOTE", "0 @$new_xref@ NOTE"),
                        ]);

                    $this->linkedRecordsQuery('individuals', 'i_file', 'i_id', $tree, $old_xref, 'NOTE')
                        ->update([
                            'i_gedcom' => DB::replace('i_gedcom', " NOTE @$old_xref@", " NOTE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('families', 'f_file', 'f_id', $tree, $old_xref, 'NOTE')
                        ->update([
                            'f_gedcom' => DB::replace('f_gedcom', " NOTE @$old_xref@", " NOTE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('media', 'm_file', 'm_id', $tree, $old_xref, 'NOTE')
                        ->update([
                            'm_gedcom' => DB::replace('m_gedcom', " NOTE @$old_xref@", " NOTE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('sources', 's_file', 's_id', $tree, $old_xref, 'NOTE')
                        ->update([
                            's_gedcom' => DB::replace('s_gedcom', " NOTE @$old_xref@", " NOTE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('other', 'o_file', 'o_id', $tree, $old_xref, 'NOTE')
                        ->update([
                            'o_gedcom' => DB::replace('o_gedcom', " NOTE @$old_xref@", " NOTE @$new_xref@"),
                        ]);
                    break;

                case Media::RECORD_TYPE:
                    DB::query(table: 'media')
                        ->whereEqual('m_file', $tree->id())
                        ->whereEqual('m_id', $old_xref)
                        ->update([
                            'm_id'     => $new_xref,
                            'm_gedcom' => DB::replace('m_gedcom', "0 @$old_xref@ OBJE", "0 @$new_xref@ OBJE"),
                        ]);

                    DB::query(table: 'media_file')
                        ->whereEqual('m_file', $tree->id())
                        ->whereEqual('m_id', $old_xref)
                        ->update([
                            'm_id' => $new_xref,
                        ]);

                    $this->linkedRecordsQuery('individuals', 'i_file', 'i_id', $tree, $old_xref, 'OBJE')
                        ->update([
                            'i_gedcom' => DB::replace('i_gedcom', " OBJE @$old_xref@", " OBJE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('families', 'f_file', 'f_id', $tree, $old_xref, 'OBJE')
                        ->update([
                            'f_gedcom' => DB::replace('f_gedcom', " OBJE @$old_xref@", " OBJE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('sources', 's_file', 's_id', $tree, $old_xref, 'OBJE')
                        ->update([
                            's_gedcom' => DB::replace('s_gedcom', " OBJE @$old_xref@", " OBJE @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('other', 'o_file', 'o_id', $tree, $old_xref, 'OBJE')
                        ->update([
                            'o_gedcom' => DB::replace('o_gedcom', " OBJE @$old_xref@", " OBJE @$new_xref@"),
                        ]);
                    break;

                default:
                    DB::query(table: 'other')
                        ->whereEqual('o_file', $tree->id())
                        ->whereEqual('o_id', $old_xref)
                        ->whereEqual('o_type', $type)
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => DB::replace('o_gedcom', "0 @$old_xref@ $type", "0 @$new_xref@ $type"),
                        ]);

                    $this->linkedRecordsQuery('individuals', 'i_file', 'i_id', $tree, $old_xref, $type)
                        ->update([
                            'i_gedcom' => DB::replace('i_gedcom', " $type @$old_xref@", " $type @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('families', 'f_file', 'f_id', $tree, $old_xref, $type)
                        ->update([
                            'f_gedcom' => DB::replace('f_gedcom', " $type @$old_xref@", " $type @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('media', 'm_file', 'm_id', $tree, $old_xref, $type)
                        ->update([
                            'm_gedcom' => DB::replace('m_gedcom', " $type @$old_xref@", " $type @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('sources', 's_file', 's_id', $tree, $old_xref, $type)
                        ->update([
                            's_gedcom' => DB::replace('s_gedcom', " $type @$old_xref@", " $type @$new_xref@"),
                        ]);

                    $this->linkedRecordsQuery('other', 'o_file', 'o_id', $tree, $old_xref, $type)
                        ->update([
                            'o_gedcom' => DB::replace('o_gedcom', " $type @$old_xref@", " $type @$new_xref@"),
                        ]);
                    break;
            }

            DB::query(table: 'name')
                ->whereEqual('n_file', $tree->id())
                ->whereEqual('n_id', $old_xref)
                ->update([
                    'n_id' => $new_xref,
                ]);

            DB::query(table: 'default_resn')
                ->whereEqual('gedcom_id', $tree->id())
                ->whereEqual('xref', $old_xref)
                ->update([
                    'xref' => $new_xref,
                ]);

            DB::query(table: 'hit_counter')
                ->whereEqual('gedcom_id', $tree->id())
                ->whereEqual('page_parameter', $old_xref)
                ->update([
                    'page_parameter' => $new_xref,
                ]);

            DB::query(table: 'link')
                ->whereEqual('l_file', $tree->id())
                ->whereEqual('l_from', $old_xref)
                ->update([
                    'l_from' => $new_xref,
                ]);

            DB::query(table: 'link')
                ->whereEqual('l_file', $tree->id())
                ->whereEqual('l_to', $old_xref)
                ->update([
                    'l_to' => $new_xref,
                ]);

            DB::query(table: 'favorite')
                ->whereEqual('gedcom_id', $tree->id())
                ->whereEqual('xref', $old_xref)
                ->update([
                    'xref' => $new_xref,
                ]);

            unset($xrefs[$old_xref]);

            // How much time do we have left?
            if ($this->timeout_service->isTimeNearlyUp()) {
                FlashMessages::addMessage(I18N::translate('The server’s time limit has been reached.'), 'warning');
                break;
            }
        }

        $url = route(self::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}
