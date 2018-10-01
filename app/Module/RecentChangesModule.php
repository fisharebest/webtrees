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
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RecentChangesModule
 */
class RecentChangesModule extends AbstractModule implements ModuleBlockInterface
{
    const DEFAULT_BLOCK      = '1';
    const DEFAULT_DAYS       = '7';
    const DEFAULT_HIDE_EMPTY = '0';
    const DEFAULT_SHOW_USER  = '1';
    const DEFAULT_SORT_STYLE = 'date_desc';
    const DEFAULT_INFO_STYLE = 'table';
    const MAX_DAYS           = '90';

    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Recent changes');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Recent changes” module */
        return I18N::translate('A list of records that have been updated recently.');
    }

    /** {@inheritdoc} */
    public function getBlock(Tree $tree, int $block_id, bool $template = true, array $cfg = []): string
    {
        global $ctype;

        $days      = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
        $show_user = (bool) $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);

        extract($cfg, EXTR_OVERWRITE);

        $records = $this->getRecentChanges($tree, $days);

        switch ($sortStyle) {
            case 'name':
                uasort($records, ['self', 'sortByNameAndChangeDate']);
                break;
            case 'date_asc':
                uasort($records, ['self', 'sortByChangeDateAndName']);
                $records = array_reverse($records);
                break;
            case 'date_desc':
                uasort($records, ['self', 'sortByChangeDateAndName']);
        }

        if (empty($records)) {
            $content = I18N::plural('There have been no changes within the last %s day.', 'There have been no changes within the last %s days.', $days, I18N::number($days));
        } elseif ($infoStyle === 'list') {
            $content = view('modules/recent_changes/changes-list', [
                'records'   => $records,
                'show_user' => $show_user,
            ]);
        } else {
            $content = view('modules/recent_changes/changes-table', [
                'records'   => $records,
                'show_user' => $show_user,
            ]);
        }

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
                'title'      => I18N::plural('Changes in the last %s day', 'Changes in the last %s days', $days, I18N::number($days)),
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
        $this->setBlockSetting($block_id, 'days', $request->get('days', self::DEFAULT_DAYS));
        $this->setBlockSetting($block_id, 'infoStyle', $request->get('infoStyle', self::DEFAULT_INFO_STYLE));
        $this->setBlockSetting($block_id, 'sortStyle', $request->get('sortStyle', self::DEFAULT_SORT_STYLE));
        $this->setBlockSetting($block_id, 'show_user', $request->get('show_user', self::DEFAULT_SHOW_USER));
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
        $days      = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
        $show_user = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);

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

        echo view('modules/recent_changes/config', [
            'days'        => $days,
            'infoStyle'   => $infoStyle,
            'info_styles' => $info_styles,
            'max_days'    => self::MAX_DAYS,
            'sortStyle'   => $sortStyle,
            'sort_styles' => $sort_styles,
            'show_user'   => $show_user,
        ]);
    }

    /**
     * Find records that have changed since a given julian day
     *
     * @param Tree $tree Changes for which tree
     * @param int  $days Number of days
     *
     * @return GedcomRecord[] List of records with changes
     */
    private function getRecentChanges(Tree $tree, int $days): array
    {
        $sql =
            "SELECT xref FROM `##change`" .
            " WHERE new_gedcom != '' AND change_time > DATE_SUB(NOW(), INTERVAL :days DAY) AND gedcom_id = :tree_id" .
            " GROUP BY xref" .
            " ORDER BY MAX(change_id) DESC";

        $vars = [
            'days'    => $days,
            'tree_id' => $tree->getTreeId(),
        ];

        $xrefs = Database::prepare($sql)->execute($vars)->fetchOneColumn();

        $records = [];
        foreach ($xrefs as $xref) {
            $record = GedcomRecord::getInstance($xref, $tree);
            if ($record && $record->canShow()) {
                $records[] = $record;
            }
        }

        return $records;
    }

    /**
     * Sort the records by (1) last change date and (2) name
     *
     * @param GedcomRecord $a
     * @param GedcomRecord $b
     *
     * @return int
     */
    private static function sortByChangeDateAndName(GedcomRecord $a, GedcomRecord $b): int
    {
        return $b->lastChangeTimestamp(true) - $a->lastChangeTimestamp(true) ?: GedcomRecord::compare($a, $b);
    }

    /**
     * Sort the records by (1) name and (2) last change date
     *
     * @param GedcomRecord $a
     * @param GedcomRecord $b
     *
     * @return int
     */
    private static function sortByNameAndChangeDate(GedcomRecord $a, GedcomRecord $b): int
    {
        return GedcomRecord::compare($a, $b) ?: $b->lastChangeTimestamp(true) - $a->lastChangeTimestamp(true);
    }
}
