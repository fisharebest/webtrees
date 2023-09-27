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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Renumber the XREFs in a family tree.
 */
class RenumberTreeAction implements RequestHandlerInterface
{
    private AdminService $admin_service;

    private TimeoutService $timeout_service;

    /**
     * @param AdminService   $admin_service
     * @param TimeoutService $timeout_service
     */
    public function __construct(AdminService $admin_service, TimeoutService $timeout_service)
    {
        $this->admin_service   = $admin_service;
        $this->timeout_service = $timeout_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $xrefs = $this->admin_service->duplicateXrefs($tree);

        foreach ($xrefs as $old_xref => $type) {
            $new_xref = Registry::xrefFactory()->make($type);
            switch ($type) {
                case Individual::RECORD_TYPE:
                    DB::table('individuals')
                        ->where('i_file', '=', $tree->id())
                        ->where('i_id', '=', $old_xref)
                        ->update([
                            'i_id'     => $new_xref,
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, '0 @$old_xref@ INDI', '0 @$new_xref@ INDI')"),
                        ]);

                    DB::table('families')
                        ->where('f_husb', '=', $old_xref)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_husb'   => $new_xref,
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' HUSB @$old_xref@', ' HUSB @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->where('f_wife', '=', $old_xref)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_wife'   => $new_xref,
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' WIFE @$old_xref@', ' WIFE @$new_xref@')"),
                        ]);

                    // Other links from families to individuals
                    foreach (['CHIL', 'ASSO', '_ASSO'] as $tag) {
                        DB::table('families')
                            ->join('link', static function (JoinClause $join): void {
                                $join
                                    ->on('l_file', '=', 'f_file')
                                    ->on('l_from', '=', 'f_id');
                            })
                            ->where('l_to', '=', $old_xref)
                            ->where('l_type', '=', $tag)
                            ->where('f_file', '=', $tree->id())
                            ->update([
                                'f_gedcom' => new Expression("REPLACE(f_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
                            ]);
                    }

                    // Links from individuals to individuals
                    foreach (['ALIA', 'ASSO', '_ASSO'] as $tag) {
                        DB::table('individuals')
                            ->join('link', static function (JoinClause $join): void {
                                $join
                                    ->on('l_file', '=', 'i_file')
                                    ->on('l_from', '=', 'i_id');
                            })
                            ->where('link.l_to', '=', $old_xref)
                            ->where('link.l_type', '=', $tag)
                            ->where('i_file', '=', $tree->id())
                            ->update([
                                'i_gedcom' => new Expression("REPLACE(i_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
                            ]);
                    }

                    DB::table('placelinks')
                        ->where('pl_file', '=', $tree->id())
                        ->where('pl_gid', '=', $old_xref)
                        ->update([
                            'pl_gid' => $new_xref,
                        ]);

                    DB::table('dates')
                        ->where('d_file', '=', $tree->id())
                        ->where('d_gid', '=', $old_xref)
                        ->update([
                            'd_gid' => $new_xref,
                        ]);

                    DB::table('user_gedcom_setting')
                        ->where('gedcom_id', '=', $tree->id())
                        ->where('setting_value', '=', $old_xref)
                        ->whereIn('setting_name', [UserInterface::PREF_TREE_ACCOUNT_XREF, UserInterface::PREF_TREE_DEFAULT_XREF])
                        ->update([
                            'setting_value' => $new_xref,
                        ]);
                    break;

                case Family::RECORD_TYPE:
                    DB::table('families')
                        ->where('f_file', '=', $tree->id())
                        ->where('f_id', '=', $old_xref)
                        ->update([
                            'f_id'     => $new_xref,
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, '0 @$old_xref@ FAM', '0 @$new_xref@ FAM')"),
                        ]);

                    // Links from individuals to families
                    foreach (['FAMC', 'FAMS'] as $tag) {
                        DB::table('individuals')
                            ->join('link', static function (JoinClause $join): void {
                                $join
                                    ->on('l_file', '=', 'i_file')
                                    ->on('l_from', '=', 'i_id');
                            })
                            ->where('l_to', '=', $old_xref)
                            ->where('l_type', '=', $tag)
                            ->where('i_file', '=', $tree->id())
                            ->update([
                                'i_gedcom' => new Expression("REPLACE(i_gedcom, ' $tag @$old_xref@', ' $tag @$new_xref@')"),
                            ]);
                    }

                    DB::table('placelinks')
                        ->where('pl_file', '=', $tree->id())
                        ->where('pl_gid', '=', $old_xref)
                        ->update([
                            'pl_gid' => $new_xref,
                        ]);

                    DB::table('dates')
                        ->where('d_file', '=', $tree->id())
                        ->where('d_gid', '=', $old_xref)
                        ->update([
                            'd_gid' => $new_xref,
                        ]);
                    break;

                case Source::RECORD_TYPE:
                    DB::table('sources')
                        ->where('s_file', '=', $tree->id())
                        ->where('s_id', '=', $old_xref)
                        ->update([
                            's_id'     => $new_xref,
                            's_gedcom' => new Expression("REPLACE(s_gedcom, '0 @$old_xref@ SOUR', '0 @$new_xref@ SOUR')"),
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);

                    DB::table('media')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'm_file')
                                ->on('l_from', '=', 'm_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('m_file', '=', $tree->id())
                        ->update([
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'SOUR')
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' SOUR @$old_xref@', ' SOUR @$new_xref@')"),
                        ]);
                    break;

                case Repository::RECORD_TYPE:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', 'REPO')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, '0 @$old_xref@ REPO', '0 @$new_xref@ REPO')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'REPO')
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' REPO @$old_xref@', ' REPO @$new_xref@')"),
                        ]);
                    break;

                case Note::RECORD_TYPE:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', 'NOTE')
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, '0 @$old_xref@ NOTE', '0 @$new_xref@ NOTE')"),
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('media')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'm_file')
                                ->on('l_from', '=', 'm_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('m_file', '=', $tree->id())
                        ->update([
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'NOTE')
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' NOTE @$old_xref@', ' NOTE @$new_xref@')"),
                        ]);
                    break;

                case Media::RECORD_TYPE:
                    DB::table('media')
                        ->where('m_file', '=', $tree->id())
                        ->where('m_id', '=', $old_xref)
                        ->update([
                            'm_id'     => $new_xref,
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, '0 @$old_xref@ OBJE', '0 @$new_xref@ OBJE')"),
                        ]);

                    DB::table('media_file')
                        ->where('m_file', '=', $tree->id())
                        ->where('m_id', '=', $old_xref)
                        ->update([
                            'm_id' => $new_xref,
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', 'OBJE')
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' OBJE @$old_xref@', ' OBJE @$new_xref@')"),
                        ]);
                    break;

                default:
                    DB::table('other')
                        ->where('o_file', '=', $tree->id())
                        ->where('o_id', '=', $old_xref)
                        ->where('o_type', '=', $type)
                        ->update([
                            'o_id'     => $new_xref,
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, '0 @$old_xref@ $type', '0 @$new_xref@ $type')"),
                        ]);

                    DB::table('individuals')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'i_file')
                                ->on('l_from', '=', 'i_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('i_file', '=', $tree->id())
                        ->update([
                            'i_gedcom' => new Expression("REPLACE(i_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('families')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'f_file')
                                ->on('l_from', '=', 'f_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('f_file', '=', $tree->id())
                        ->update([
                            'f_gedcom' => new Expression("REPLACE(f_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('media')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'm_file')
                                ->on('l_from', '=', 'm_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('m_file', '=', $tree->id())
                        ->update([
                            'm_gedcom' => new Expression("REPLACE(m_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('sources')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 's_file')
                                ->on('l_from', '=', 's_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('s_file', '=', $tree->id())
                        ->update([
                            's_gedcom' => new Expression("REPLACE(s_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);

                    DB::table('other')
                        ->join('link', static function (JoinClause $join): void {
                            $join
                                ->on('l_file', '=', 'o_file')
                                ->on('l_from', '=', 'o_id');
                        })
                        ->where('l_to', '=', $old_xref)
                        ->where('l_type', '=', $type)
                        ->where('o_file', '=', $tree->id())
                        ->update([
                            'o_gedcom' => new Expression("REPLACE(o_gedcom, ' $type @$old_xref@', ' $type @$new_xref@')"),
                        ]);
                    break;
            }

            DB::table('name')
                ->where('n_file', '=', $tree->id())
                ->where('n_id', '=', $old_xref)
                ->update([
                    'n_id' => $new_xref,
                ]);

            DB::table('default_resn')
                ->where('gedcom_id', '=', $tree->id())
                ->where('xref', '=', $old_xref)
                ->update([
                    'xref' => $new_xref,
                ]);

            DB::table('hit_counter')
                ->where('gedcom_id', '=', $tree->id())
                ->where('page_parameter', '=', $old_xref)
                ->update([
                    'page_parameter' => $new_xref,
                ]);

            DB::table('link')
                ->where('l_file', '=', $tree->id())
                ->where('l_from', '=', $old_xref)
                ->update([
                    'l_from' => $new_xref,
                ]);

            DB::table('link')
                ->where('l_file', '=', $tree->id())
                ->where('l_to', '=', $old_xref)
                ->update([
                    'l_to' => $new_xref,
                ]);

            DB::table('favorite')
                ->where('gedcom_id', '=', $tree->id())
                ->where('xref', '=', $old_xref)
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

        $url = route(RenumberTreePage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}
