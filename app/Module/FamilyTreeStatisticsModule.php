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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function array_slice;
use function extract;
use function view;

use const EXTR_OVERWRITE;

/**
 * Class FamilyTreeStatisticsModule
 */
class FamilyTreeStatisticsModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /** Show this number of surnames by default */
    private const DEFAULT_NUMBER_OF_SURNAMES = '10';

    private ModuleService $module_service;

    /**
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Statistics');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of “Statistics” module */
        return I18N::translate('The size of the family tree, earliest and latest events, common names, etc.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $statistics = Registry::container()->get(Statistics::class);

        $show_last_update     = $this->getBlockSetting($block_id, 'show_last_update', '1');
        $show_common_surnames = $this->getBlockSetting($block_id, 'show_common_surnames', '1');
        $number_of_surnames   = (int) $this->getBlockSetting($block_id, 'number_of_surnames', self::DEFAULT_NUMBER_OF_SURNAMES);
        $stat_indi            = $this->getBlockSetting($block_id, 'stat_indi', '1');
        $stat_fam             = $this->getBlockSetting($block_id, 'stat_fam', '1');
        $stat_sour            = $this->getBlockSetting($block_id, 'stat_sour', '1');
        $stat_media           = $this->getBlockSetting($block_id, 'stat_media', '1');
        $stat_repo            = $this->getBlockSetting($block_id, 'stat_repo', '1');
        $stat_surname         = $this->getBlockSetting($block_id, 'stat_surname', '1');
        $stat_events          = $this->getBlockSetting($block_id, 'stat_events', '1');
        $stat_users           = $this->getBlockSetting($block_id, 'stat_users', '1');
        $stat_first_birth     = $this->getBlockSetting($block_id, 'stat_first_birth', '1');
        $stat_last_birth      = $this->getBlockSetting($block_id, 'stat_last_birth', '1');
        $stat_first_death     = $this->getBlockSetting($block_id, 'stat_first_death', '1');
        $stat_last_death      = $this->getBlockSetting($block_id, 'stat_last_death', '1');
        $stat_long_life       = $this->getBlockSetting($block_id, 'stat_long_life', '1');
        $stat_avg_life        = $this->getBlockSetting($block_id, 'stat_avg_life', '1');
        $stat_most_chil       = $this->getBlockSetting($block_id, 'stat_most_chil', '1');
        $stat_avg_chil        = $this->getBlockSetting($block_id, 'stat_avg_chil', '1');

        extract($config, EXTR_OVERWRITE);

        if ($show_common_surnames === '1') {
            $query = DB::table('name')
                ->where('n_file', '=', $tree->id())
                ->where('n_type', '<>', '_MARNM')
                ->where('n_surn', '<>', '')
                ->where('n_surn', '<>', Individual::NOMEN_NESCIO)
                ->select([
                    $this->binaryColumn('n_surn', 'n_surn'),
                    $this->binaryColumn('n_surname', 'n_surname'),
                    new Expression('COUNT(*) AS total'),
                ])
                ->groupBy([
                    $this->binaryColumn('n_surn'),
                    $this->binaryColumn('n_surname'),
                ]);

            /** @var array<array<int>> $top_surnames */
            $top_surnames = [];

            foreach ($query->get() as $row) {
                $row->n_surn = $row->n_surn === '' ? $row->n_surname : $row->n_surn;
                $row->n_surn = I18N::strtoupper(I18N::language()->normalize($row->n_surn));

                $top_surnames[$row->n_surn][$row->n_surname] ??= 0;
                $top_surnames[$row->n_surn][$row->n_surname] += (int) $row->total;
            }

            uasort($top_surnames, static fn (array $x, array $y): int => array_sum($y) <=> array_sum($x));

            $top_surnames = array_slice($top_surnames, 0, $number_of_surnames, true);

            // Find a module providing individual lists
            $module = $this->module_service
                ->findByComponent(ModuleListInterface::class, $tree, Auth::user())
                ->first(static fn (ModuleInterface $module): bool => $module instanceof IndividualListModule);

            $surnames = view('lists/surnames-compact-list', [
                'module'   => $module,
                'totals'   => false,
                'surnames' => $top_surnames,
                'tree'     => $tree,
            ]);
        } else {
            $surnames = '';
        }

        $content = view('modules/gedcom_stats/statistics', [
            'show_last_update'     => $show_last_update,
            'show_common_surnames' => $show_common_surnames,
            'number_of_surnames'   => $number_of_surnames,
            'stat_indi'            => $stat_indi,
            'stat_fam'             => $stat_fam,
            'stat_sour'            => $stat_sour,
            'stat_media'           => $stat_media,
            'stat_repo'            => $stat_repo,
            'stat_surname'         => $stat_surname,
            'stat_events'          => $stat_events,
            'stat_users'           => $stat_users,
            'stat_first_birth'     => $stat_first_birth,
            'stat_last_birth'      => $stat_last_birth,
            'stat_first_death'     => $stat_first_death,
            'stat_last_death'      => $stat_last_death,
            'stat_long_life'       => $stat_long_life,
            'stat_avg_life'        => $stat_avg_life,
            'stat_most_chil'       => $stat_most_chil,
            'stat_avg_chil'        => $stat_avg_chil,
            'surnames'             => $surnames,
        ]);

        $content = $statistics->embedTags($content);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
                'title'      => $this->title(),
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
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $show_last_update     = Validator::parsedBody($request)->boolean('show_last_update', false);
        $show_common_surnames = Validator::parsedBody($request)->boolean('show_common_surnames', false);
        $number_of_surnames   = Validator::parsedBody($request)->integer('number_of_surnames');
        $stat_indi            = Validator::parsedBody($request)->boolean('stat_indi', false);
        $stat_fam             = Validator::parsedBody($request)->boolean('stat_fam', false);
        $stat_sour            = Validator::parsedBody($request)->boolean('stat_sour', false);
        $stat_other           = Validator::parsedBody($request)->boolean('stat_other', false);
        $stat_media           = Validator::parsedBody($request)->boolean('stat_media', false);
        $stat_repo            = Validator::parsedBody($request)->boolean('stat_repo', false);
        $stat_surname         = Validator::parsedBody($request)->boolean('stat_surname', false);
        $stat_events          = Validator::parsedBody($request)->boolean('stat_events', false);
        $stat_users           = Validator::parsedBody($request)->boolean('stat_users', false);
        $stat_first_birth     = Validator::parsedBody($request)->boolean('stat_first_birth', false);
        $stat_last_birth      = Validator::parsedBody($request)->boolean('stat_last_birth', false);
        $stat_first_death     = Validator::parsedBody($request)->boolean('stat_first_death', false);
        $stat_last_death      = Validator::parsedBody($request)->boolean('stat_last_death', false);
        $stat_long_life       = Validator::parsedBody($request)->boolean('stat_long_life', false);
        $stat_avg_life        = Validator::parsedBody($request)->boolean('stat_avg_life', false);
        $stat_most_chil       = Validator::parsedBody($request)->boolean('stat_most_chil', false);
        $stat_avg_chil        = Validator::parsedBody($request)->boolean('stat_avg_chil', false);

        $this->setBlockSetting($block_id, 'show_last_update', (string) $show_last_update);
        $this->setBlockSetting($block_id, 'show_common_surnames', (string) $show_common_surnames);
        $this->setBlockSetting($block_id, 'number_of_surnames', (string) $number_of_surnames);
        $this->setBlockSetting($block_id, 'stat_indi', (string) $stat_indi);
        $this->setBlockSetting($block_id, 'stat_fam', (string) $stat_fam);
        $this->setBlockSetting($block_id, 'stat_sour', (string) $stat_sour);
        $this->setBlockSetting($block_id, 'stat_other', (string) $stat_other);
        $this->setBlockSetting($block_id, 'stat_media', (string) $stat_media);
        $this->setBlockSetting($block_id, 'stat_repo', (string) $stat_repo);
        $this->setBlockSetting($block_id, 'stat_surname', (string) $stat_surname);
        $this->setBlockSetting($block_id, 'stat_events', (string) $stat_events);
        $this->setBlockSetting($block_id, 'stat_users', (string) $stat_users);
        $this->setBlockSetting($block_id, 'stat_first_birth', (string) $stat_first_birth);
        $this->setBlockSetting($block_id, 'stat_last_birth', (string) $stat_last_birth);
        $this->setBlockSetting($block_id, 'stat_first_death', (string) $stat_first_death);
        $this->setBlockSetting($block_id, 'stat_last_death', (string) $stat_last_death);
        $this->setBlockSetting($block_id, 'stat_long_life', (string) $stat_long_life);
        $this->setBlockSetting($block_id, 'stat_avg_life', (string) $stat_avg_life);
        $this->setBlockSetting($block_id, 'stat_most_chil', (string) $stat_most_chil);
        $this->setBlockSetting($block_id, 'stat_avg_chil', (string) $stat_avg_chil);
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
        $show_last_update     = $this->getBlockSetting($block_id, 'show_last_update', '1');
        $show_common_surnames = $this->getBlockSetting($block_id, 'show_common_surnames', '1');
        $number_of_surnames   = $this->getBlockSetting($block_id, 'number_of_surnames', self::DEFAULT_NUMBER_OF_SURNAMES);
        $stat_indi            = $this->getBlockSetting($block_id, 'stat_indi', '1');
        $stat_fam             = $this->getBlockSetting($block_id, 'stat_fam', '1');
        $stat_sour            = $this->getBlockSetting($block_id, 'stat_sour', '1');
        $stat_media           = $this->getBlockSetting($block_id, 'stat_media', '1');
        $stat_repo            = $this->getBlockSetting($block_id, 'stat_repo', '1');
        $stat_surname         = $this->getBlockSetting($block_id, 'stat_surname', '1');
        $stat_events          = $this->getBlockSetting($block_id, 'stat_events', '1');
        $stat_users           = $this->getBlockSetting($block_id, 'stat_users', '1');
        $stat_first_birth     = $this->getBlockSetting($block_id, 'stat_first_birth', '1');
        $stat_last_birth      = $this->getBlockSetting($block_id, 'stat_last_birth', '1');
        $stat_first_death     = $this->getBlockSetting($block_id, 'stat_first_death', '1');
        $stat_last_death      = $this->getBlockSetting($block_id, 'stat_last_death', '1');
        $stat_long_life       = $this->getBlockSetting($block_id, 'stat_long_life', '1');
        $stat_avg_life        = $this->getBlockSetting($block_id, 'stat_avg_life', '1');
        $stat_most_chil       = $this->getBlockSetting($block_id, 'stat_most_chil', '1');
        $stat_avg_chil        = $this->getBlockSetting($block_id, 'stat_avg_chil', '1');

        return view('modules/gedcom_stats/config', [
            'show_last_update'     => $show_last_update,
            'show_common_surnames' => $show_common_surnames,
            'number_of_surnames'   => $number_of_surnames,
            'stat_indi'            => $stat_indi,
            'stat_fam'             => $stat_fam,
            'stat_sour'            => $stat_sour,
            'stat_media'           => $stat_media,
            'stat_repo'            => $stat_repo,
            'stat_surname'         => $stat_surname,
            'stat_events'          => $stat_events,
            'stat_users'           => $stat_users,
            'stat_first_birth'     => $stat_first_birth,
            'stat_last_birth'      => $stat_last_birth,
            'stat_first_death'     => $stat_first_death,
            'stat_last_death'      => $stat_last_death,
            'stat_long_life'       => $stat_long_life,
            'stat_avg_life'        => $stat_avg_life,
            'stat_most_chil'       => $stat_most_chil,
            'stat_avg_chil'        => $stat_avg_chil,
        ]);
    }

    /**
     * This module assumes the database will use binary collation on the name columns.
     * Until we convert MySQL databases to use utf8_bin, we need to do this at run-time.
     *
     * @param string      $column
     * @param string|null $alias
     *
     * @return Expression
     */
    private function binaryColumn(string $column, string|null $alias = null): Expression
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $sql = 'CAST(' . $column . ' AS binary)';
        } else {
            $sql = $column;
        }

        if ($alias !== null) {
            $sql .= ' AS ' . $alias;
        }

        return new Expression($sql);
    }
}
