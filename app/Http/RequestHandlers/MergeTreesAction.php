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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Merge two family trees.
 */
class MergeTreesAction implements RequestHandlerInterface
{
    /** @var AdminService */
    private $admin_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * AdminTreesController constructor.
     *
     * @param AdminService $admin_service
     * @param TreeService  $tree_service
     */
    public function __construct(AdminService $admin_service, TreeService $tree_service)
    {
        $this->admin_service = $admin_service;
        $this->tree_service  = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params     = (array) $request->getParsedBody();
        $tree1_name = $params['tree1_name'] ?? '';
        $tree2_name = $params['tree2_name'] ?? '';

        $tree1 = $this->tree_service->all()->get($tree1_name);
        $tree2 = $this->tree_service->all()->get($tree2_name);

        if ($tree1 instanceof Tree && $tree2 instanceof Tree && $tree1 !== $tree2 && $this->admin_service->countCommonXrefs($tree1, $tree2) === 0) {
            (new Builder(DB::connection()))->from('individuals')->insertUsing([
                'i_file',
                'i_id',
                'i_rin',
                'i_sex',
                'i_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'i_id',
                    'i_rin',
                    'i_sex',
                    'i_gedcom',
                ])->from('individuals')
                    ->where('i_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('families')->insertUsing([
                'f_file',
                'f_id',
                'f_husb',
                'f_wife',
                'f_gedcom',
                'f_numchil',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'f_id',
                    'f_husb',
                    'f_wife',
                    'f_gedcom',
                    'f_numchil',
                ])->from('families')
                    ->where('f_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('sources')->insertUsing([
                's_file',
                's_id',
                's_name',
                's_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    's_id',
                    's_name',
                    's_gedcom',
                ])->from('sources')
                    ->where('s_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('media')->insertUsing([
                'm_file',
                'm_id',
                'm_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'm_id',
                    'm_gedcom',
                ])->from('media')
                    ->where('m_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('media_file')->insertUsing([
                'm_file',
                'm_id',
                'multimedia_file_refn',
                'multimedia_format',
                'source_media_type',
                'descriptive_title',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'm_id',
                    'multimedia_file_refn',
                    'multimedia_format',
                    'source_media_type',
                    'descriptive_title',
                ])->from('media_file')
                    ->where('m_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('other')->insertUsing([
                'o_file',
                'o_id',
                'o_type',
                'o_gedcom',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'o_id',
                    'o_type',
                    'o_gedcom',
                ])->from('other')
                    ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
                    ->where('o_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('name')->insertUsing([
                'n_file',
                'n_id',
                'n_num',
                'n_type',
                'n_sort',
                'n_full',
                'n_surname',
                'n_surn',
                'n_givn',
                'n_soundex_givn_std',
                'n_soundex_surn_std',
                'n_soundex_givn_dm',
                'n_soundex_surn_dm',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'n_id',
                    'n_num',
                    'n_type',
                    'n_sort',
                    'n_full',
                    'n_surname',
                    'n_surn',
                    'n_givn',
                    'n_soundex_givn_std',
                    'n_soundex_surn_std',
                    'n_soundex_givn_dm',
                    'n_soundex_surn_dm',
                ])->from('name')
                    ->where('n_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('dates')->insertUsing([
                'd_file',
                'd_gid',
                'd_day',
                'd_month',
                'd_mon',
                'd_year',
                'd_julianday1',
                'd_julianday2',
                'd_fact',
                'd_type',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'd_gid',
                    'd_day',
                    'd_month',
                    'd_mon',
                    'd_year',
                    'd_julianday1',
                    'd_julianday2',
                    'd_fact',
                    'd_type',
                ])->from('dates')
                    ->where('d_file', '=', $tree1->id());
            });

            (new Builder(DB::connection()))->from('link')->insertUsing([
                'l_file',
                'l_from',
                'l_type',
                'l_to',
            ], static function (Builder $query) use ($tree1, $tree2): void {
                $query->select([
                    new Expression($tree2->id()),
                    'l_from',
                    'l_type',
                    'l_to',
                ])->from('link')
                    ->whereNotIn('l_from', [Header::RECORD_TYPE, 'TRLR'])
                    ->where('l_file', '=', $tree1->id());
            });

            FlashMessages::addMessage(I18N::translate('The family trees have been merged successfully.'), 'success');

            $url = route(ManageTrees::class, ['tree' => $tree2->name()]);
        } else {
            $url = route(MergeTreesPage::class, [
                'tree1_name' => $tree1->name(),
                'tree2_name' => $tree2->name(),
            ]);
        }

        return redirect($url);
    }
}
