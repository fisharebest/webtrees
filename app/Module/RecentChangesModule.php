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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function extract;
use function view;

use const EXTR_OVERWRITE;

/**
 * Class RecentChangesModule
 */
class RecentChangesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Where do we look for change information
    private const SOURCE_DATABASE = 'database';
    private const SOURCE_GEDCOM   = 'gedcom';

    private const DEFAULT_DAYS       = '7';
    private const DEFAULT_SHOW_USER  = '1';
    private const DEFAULT_SHOW_DATE  = '1';
    private const DEFAULT_SORT_STYLE = 'date_desc';
    private const DEFAULT_INFO_STYLE = 'table';
    private const DEFAULT_SOURCE     = self::SOURCE_DATABASE;
    private const MAX_DAYS           = 90;

    // Pagination
    private const LIMIT_LOW  = 10;
    private const LIMIT_HIGH = 20;

    private UserService $user_service;

    /**
     * RecentChangesModule constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Recent changes');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Recent changes” module */
        return I18N::translate('A list of records that have been updated recently.');
    }

    /**
     * @param Tree   $tree
     * @param int    $block_id
     * @param string $context
     * @param array  $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $days      = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
        $show_user = (bool) $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);
        $show_date = (bool) $this->getBlockSetting($block_id, 'show_date', self::DEFAULT_SHOW_DATE);
        $source    = $this->getBlockSetting($block_id, 'source', self::DEFAULT_SOURCE);

        extract($config, EXTR_OVERWRITE);

        if ($source === self::SOURCE_DATABASE) {
            $rows = $this->getRecentChangesFromDatabase($tree, $days);
        } else {
            $rows = $this->getRecentChangesFromGenealogy($tree, $days);
        }

        switch ($sortStyle) {
            case 'name':
                $rows  = $rows->sort(static function (object $x, object $y): int {
                    return GedcomRecord::nameComparator()($x->record, $y->record);
                });
                $order = [[1, 'asc']];
                break;

            case 'date_asc':
                $rows  = $rows->sort(static function (object $x, object $y): int {
                    return $x->time <=> $y->time;
                });
                $order = [[2, 'asc']];
                break;

            default:
            case 'date_desc':
                $rows  = $rows->sort(static function (object $x, object $y): int {
                    return $y->time <=> $x->time;
                });
                $order = [[2, 'desc']];
                break;
        }

        if ($rows->isEmpty()) {
            $content = I18N::plural('There have been no changes within the last %s day.', 'There have been no changes within the last %s days.', $days, I18N::number($days));
        } elseif ($infoStyle === 'list') {
            $content = view('modules/recent_changes/changes-list', [
                'id'         => $block_id,
                'limit_low'  => self::LIMIT_LOW,
                'limit_high' => self::LIMIT_HIGH,
                'rows'       => $rows->values(),
                'show_date'  => $show_date,
                'show_user'  => $show_user,
            ]);
        } else {
            $content = view('modules/recent_changes/changes-table', [
                'limit_low'  => self::LIMIT_LOW,
                'limit_high' => self::LIMIT_HIGH,
                'rows'       => $rows,
                'show_date'  => $show_date,
                'show_user'  => $show_user,
                'order'      => $order,
            ]);
        }

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
                'title'      => I18N::plural('Changes in the last %s day', 'Changes in the last %s days', $days, I18N::number($days)),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $params = (array) $request->getParsedBody();

        $this->setBlockSetting($block_id, 'days', $params['days']);
        $this->setBlockSetting($block_id, 'infoStyle', $params['infoStyle']);
        $this->setBlockSetting($block_id, 'sortStyle', $params['sortStyle']);
        $this->setBlockSetting($block_id, 'show_date', $params['show_date']);
        $this->setBlockSetting($block_id, 'show_user', $params['show_user']);
        $this->setBlockSetting($block_id, 'source', $params['source']);
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        $days      = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
        $show_date = $this->getBlockSetting($block_id, 'show_date', self::DEFAULT_SHOW_DATE);
        $show_user = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);
        $source    = $this->getBlockSetting($block_id, 'source', self::DEFAULT_SOURCE);

        $info_styles = [
            /* I18N: An option in a list-box */
            'list'  => I18N::translate('list'),
            /* I18N: An option in a list-box */
            'table' => I18N::translate('table'),
        ];

        $sort_styles = [
            /* I18N: An option in a list-box */
            'name'      => I18N::translate('sort by name'),
            /* I18N: An option in a list-box */
            'date_asc'  => I18N::translate('sort by date, oldest first'),
            /* I18N: An option in a list-box */
            'date_desc' => I18N::translate('sort by date, newest first'),
        ];

        $sources = [
            /* I18N: An option in a list-box */
            self::SOURCE_DATABASE => I18N::translate('show changes made in webtrees'),
            /* I18N: An option in a list-box */
            self::SOURCE_GEDCOM   => I18N::translate('show changes recorded in the genealogy data'),
        ];

        return view('modules/recent_changes/config', [
            'days'        => $days,
            'infoStyle'   => $infoStyle,
            'info_styles' => $info_styles,
            'max_days'    => self::MAX_DAYS,
            'sortStyle'   => $sortStyle,
            'sort_styles' => $sort_styles,
            'source'      => $source,
            'sources'     => $sources,
            'show_date'   => $show_date,
            'show_user'   => $show_user,
        ]);
    }

    /**
     * Find records that have changed since a given julian day
     *
     * @param Tree $tree Changes for which tree
     * @param int  $days Number of days
     *
     * @return Collection<object> List of records with changes
     */
    private function getRecentChangesFromDatabase(Tree $tree, int $days): Collection
    {
        $subquery = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'accepted')
            ->where('new_gedcom', '<>', '')
            ->where('change_time', '>', Carbon::now()->subDays($days))
            ->groupBy(['xref'])
            ->select(new Expression('MAX(change_id) AS recent_change_id'));

        $query = DB::table('change')
            ->joinSub($subquery, 'recent', 'recent_change_id', '=', 'change_id')
            ->select(['change.*']);

        return $query
            ->get()
            ->map(function (object $row) use ($tree): object {
                return (object) [
                    'record' => Registry::gedcomRecordFactory()->make($row->xref, $tree, $row->new_gedcom),
                    'time'   => Carbon::create($row->change_time)->local(),
                    'user'   => $this->user_service->find((int) $row->user_id),
                ];
            })
            ->filter(static function (object $row): bool {
                return $row->record instanceof GedcomRecord && $row->record->canShow();
            });
    }

    /**
     * Find records that have changed since a given julian day
     *
     * @param Tree $tree Changes for which tree
     * @param int  $days Number of days
     *
     * @return Collection<object> List of records with changes
     */
    private function getRecentChangesFromGenealogy(Tree $tree, int $days): Collection
    {
        $julian_day = Carbon::now()->julianDay() - $days;

        $individuals = DB::table('dates')
            ->where('d_file', '=', $tree->id())
            ->where('d_julianday1', '>=', $julian_day)
            ->where('d_fact', '=', 'CHAN')
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('d_file', '=', 'i_file')
                    ->on('d_gid', '=', 'i_id');
            })
            ->select(['individuals.*'])
            ->get()
            ->map(Registry::individualFactory()->mapper($tree))
            ->filter(Individual::accessFilter());

        $families = DB::table('dates')
            ->where('d_file', '=', $tree->id())
            ->where('d_julianday1', '>=', $julian_day)
            ->where('d_fact', '=', 'CHAN')
            ->join('families', static function (JoinClause $join): void {
                $join
                    ->on('d_file', '=', 'f_file')
                    ->on('d_gid', '=', 'f_id');
            })
            ->select(['families.*'])
            ->get()
            ->map(Registry::familyFactory()->mapper($tree))
            ->filter(Family::accessFilter());

        return $individuals->merge($families)
            ->map(function (GedcomRecord $record): object {
                $user = $this->user_service->findByUserName($record->lastChangeUser());

                return (object) [
                    'record' => $record,
                    'time'   => $record->lastChangeTimestamp(),
                    'user'   => $user ?? new User(0, '…', '…', ''),
                ];
            });
    }
}
