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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FamilyTreeStatisticsModule
 */
class FamilyTreeStatisticsModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /** Show this number of surnames by default */
    private const DEFAULT_NUMBER_OF_SURNAMES = '10';

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
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $context
     * @param string[] $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $statistics = app(Statistics::class);

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

        if ($show_common_surnames) {
            // Use the count of base surnames.
            $top_surnames = DB::table('name')
                ->where('n_file', '=', $tree->id())
                ->where('n_type', '<>', '_MARNM')
                ->whereNotIn('n_surn', ['@N.N.', ''])
                ->groupBy(['n_surn'])
                ->orderByDesc(new Expression('COUNT(n_surn)'))
                ->take($number_of_surnames)
                ->pluck('n_surn');

            $all_surnames = [];

            foreach ($top_surnames as $top_surname) {
                $variants = DB::table('name')
                    ->where('n_file', '=', $tree->id())
                    ->where(new Expression('n_surn /*! COLLATE utf8_bin */'), '=', $top_surname)
                    ->groupBy(['surname'])
                    ->select([new Expression('n_surname /*! COLLATE utf8_bin */ AS surname'), new Expression('count(*) AS total')])
                    ->pluck('total', 'surname')
                    ->all();

                $all_surnames[$top_surname] = $variants;
            }

            uksort($all_surnames, [I18N::class, 'strcasecmp']);

            //find a module providing individual lists
            $module = app(ModuleService::class)->findByComponent(ModuleListInterface::class, $tree, Auth::user())->first(static function (ModuleInterface $module) {
                return $module instanceof IndividualListModule;
            });

            $surnames = FunctionsPrintLists::surnameList($all_surnames, 2, false, $module, $tree);
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
            'stats'                => $statistics,
            'surnames'             => $surnames,
        ]);

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
        $params = (array) $request->getParsedBody();

        $this->setBlockSetting($block_id, 'show_last_update', $params['show_last_update'] ?? '');
        $this->setBlockSetting($block_id, 'show_common_surnames', $params['show_common_surnames'] ?? '');
        $this->setBlockSetting($block_id, 'number_of_surnames', $params['number_of_surnames']);
        $this->setBlockSetting($block_id, 'stat_indi', $params['stat_indi'] ?? '');
        $this->setBlockSetting($block_id, 'stat_fam', $params['stat_fam'] ?? '');
        $this->setBlockSetting($block_id, 'stat_sour', $params['stat_sour'] ?? '');
        $this->setBlockSetting($block_id, 'stat_other', $params['stat_other'] ?? '');
        $this->setBlockSetting($block_id, 'stat_media', $params['stat_media'] ?? '');
        $this->setBlockSetting($block_id, 'stat_repo', $params['stat_repo'] ?? '');
        $this->setBlockSetting($block_id, 'stat_surname', $params['stat_surname'] ?? '');
        $this->setBlockSetting($block_id, 'stat_events', $params['stat_events'] ?? '');
        $this->setBlockSetting($block_id, 'stat_users', $params['stat_users'] ?? '');
        $this->setBlockSetting($block_id, 'stat_first_birth', $params['stat_first_birth'] ?? '');
        $this->setBlockSetting($block_id, 'stat_last_birth', $params['stat_last_birth'] ?? '');
        $this->setBlockSetting($block_id, 'stat_first_death', $params['stat_first_death'] ?? '');
        $this->setBlockSetting($block_id, 'stat_last_death', $params['stat_last_death'] ?? '');
        $this->setBlockSetting($block_id, 'stat_long_life', $params['stat_long_life'] ?? '');
        $this->setBlockSetting($block_id, 'stat_avg_life', $params['stat_avg_life'] ?? '');
        $this->setBlockSetting($block_id, 'stat_most_chil', $params['stat_most_chil'] ?? '');
        $this->setBlockSetting($block_id, 'stat_avg_chil', $params['stat_avg_chil'] ?? '');
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
}
