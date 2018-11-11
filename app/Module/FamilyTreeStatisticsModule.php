<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FamilyTreeStatisticsModule
 */
class FamilyTreeStatisticsModule extends AbstractModule implements ModuleBlockInterface
{
    /** Show this number of surnames by default */
    const DEFAULT_NUMBER_OF_SURNAMES = '10';

    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Statistics');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of “Statistics” module */
        return I18N::translate('The size of the family tree, earliest and latest events, common names, etc.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param bool     $template
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, bool $template = true, array $cfg = []): string
    {
        global $ctype;

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

        extract($cfg, EXTR_OVERWRITE);

        if ($show_common_surnames) {
            // Use the count of base surnames.
            $top_surnames = Database::prepare(
                "SELECT n_surn FROM `##name`" .
                " WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '')" .
                " GROUP BY n_surn" .
                " ORDER BY COUNT(n_surn) DESC" .
                " LIMIT :limit"
            )->execute([
                'tree_id' => $tree->id(),
                'limit'   => $number_of_surnames,
            ])->fetchOneColumn();

            $all_surnames = [];
            foreach ($top_surnames as $top_surname) {
                $variants = Database::prepare(
                    "SELECT n_surname COLLATE utf8_bin, COUNT(*) FROM `##name` WHERE n_file = :tree_id AND n_surn COLLATE :collate = :surname GROUP BY 1"
                )->execute([
                    'collate' => I18N::collation(),
                    'surname' => $top_surname,
                    'tree_id' => $tree->id(),
                ])->fetchAssoc();

                $all_surnames[$top_surname] = $variants;
            }

            uksort($all_surnames, [I18N::class, 'strcasecmp']);

            $surnames = FunctionsPrintLists::surnameList($all_surnames, 2, false, 'individual-list', $tree);
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
            'stats'                => new Stats($tree),
            'surnames'             => $surnames,
        ]);

        if ($template) {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->getName(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->getName(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->getName()),
                'id'         => $block_id,
                'config_url' => $config_url,
                'title'      => $this->getTitle(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /** {@inheritdoc} */
    public function loadAjax(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isUserBlock(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGedcomBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
    {
        $this->setBlockSetting($block_id, 'show_last_update', $request->get('show_last_update', ''));
        $this->setBlockSetting($block_id, 'show_common_surnames', $request->get('show_common_surnames', ''));
        $this->setBlockSetting($block_id, 'number_of_surnames', $request->get('number_of_surnames', self::DEFAULT_NUMBER_OF_SURNAMES));
        $this->setBlockSetting($block_id, 'stat_indi', $request->get('stat_indi', ''));
        $this->setBlockSetting($block_id, 'stat_fam', $request->get('stat_fam', ''));
        $this->setBlockSetting($block_id, 'stat_sour', $request->get('stat_sour', ''));
        $this->setBlockSetting($block_id, 'stat_other', $request->get('stat_other', ''));
        $this->setBlockSetting($block_id, 'stat_media', $request->get('stat_media', ''));
        $this->setBlockSetting($block_id, 'stat_repo', $request->get('stat_repo', ''));
        $this->setBlockSetting($block_id, 'stat_surname', $request->get('stat_surname', ''));
        $this->setBlockSetting($block_id, 'stat_events', $request->get('stat_events', ''));
        $this->setBlockSetting($block_id, 'stat_users', $request->get('stat_users', ''));
        $this->setBlockSetting($block_id, 'stat_first_birth', $request->get('stat_first_birth', ''));
        $this->setBlockSetting($block_id, 'stat_last_birth', $request->get('stat_last_birth', ''));
        $this->setBlockSetting($block_id, 'stat_first_death', $request->get('stat_first_death', ''));
        $this->setBlockSetting($block_id, 'stat_last_death', $request->get('stat_last_death', ''));
        $this->setBlockSetting($block_id, 'stat_long_life', $request->get('stat_long_life', ''));
        $this->setBlockSetting($block_id, 'stat_avg_life', $request->get('stat_avg_life', ''));
        $this->setBlockSetting($block_id, 'stat_most_chil', $request->get('stat_most_chil', ''));
        $this->setBlockSetting($block_id, 'stat_avg_chil', $request->get('stat_avg_chil', ''));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id)
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

        echo view('modules/gedcom_stats/config', [
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
