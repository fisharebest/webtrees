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
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OnThisDayModule
 */
class OnThisDayModule extends AbstractModule implements ModuleBlockInterface
{
    // All standard GEDCOM 5.5.1 events except CENS, RESI and EVEN
    const ALL_EVENTS = [
        'ADOP',
        'ANUL',
        'BAPM',
        'BARM',
        'BASM',
        'BIRT',
        'BLES',
        'BURI',
        'CHR',
        'CHRA',
        'CONF',
        'CREM',
        'DEAT',
        'DIV',
        'DIVF',
        'EMIG',
        'ENGA',
        'FCOM',
        'GRAD',
        'IMMI',
        'MARB',
        'MARC',
        'MARL',
        'MARR',
        'MARS',
        'NATU',
        'ORDN',
        'PROB',
        'RETI',
        'WILL',
    ];

    const DEFAULT_EVENTS = [
        'BIRT',
        'MARR',
        'DEAT',
    ];

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('On this day');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “On this day” module */
        return I18N::translate('A list of the anniversaries that occur today.');
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

        $calendar_service = new CalendarService();

        $default_events = implode(',', self::DEFAULT_EVENTS);

        $filter    = (bool) $this->getBlockSetting($block_id, 'filter', '1');
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
        $events    = $this->getBlockSetting($block_id, 'events', $default_events);

        extract($cfg, EXTR_OVERWRITE);

        $event_array = explode(',', $events);

        // If we are only showing living individuals, then we don't need to search for DEAT events.
        if ($filter) {
            $death_events = explode('|', WT_EVENTS_DEAT);
            $event_array  = array_diff($event_array, $death_events);
        }

        $events_filter = implode('|', $event_array);

        $startjd = WT_CLIENT_JD;
        $endjd   = WT_CLIENT_JD;

        $facts = $calendar_service->getEventsList($startjd, $endjd, $events_filter, $filter, $sortStyle, $tree);

        if (empty($facts)) {
            $content = view('modules/todays_events/empty');
        } elseif ($infoStyle === 'list') {
            $content = view('modules/todays_events/list', [
                'facts' => $facts,
            ]);
        } else {
            $content = view('modules/todays_events/table', [
                'facts' => $facts,
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
        $this->setBlockSetting($block_id, 'filter', $request->get('filter', '1'));
        $this->setBlockSetting($block_id, 'infoStyle', $request->get('infoStyle', 'table'));
        $this->setBlockSetting($block_id, 'sortStyle', $request->get('sortStyle', 'alpha'));
        $this->setBlockSetting($block_id, 'events', implode(',', (array) $request->get('events')));
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
        $default_events = implode(',', self::DEFAULT_EVENTS);

        $filter    = $this->getBlockSetting($block_id, 'filter', '1');
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
        $events    = $this->getBlockSetting($block_id, 'events', $default_events);

        $event_array = explode(',', $events);

        $all_events = [];
        foreach (self::ALL_EVENTS as $event) {
            $all_events[$event] = GedcomTag::getLabel($event);
        }

        $info_styles = [
            /* I18N: An option in a list-box */
            'list'  => I18N::translate('list'),
            /* I18N: An option in a list-box */
            'table' => I18N::translate('table'),
        ];

        $sort_styles = [
            /* I18N: An option in a list-box */
            'alpha' => I18N::translate('sort by name'),
            /* I18N: An option in a list-box */
            'anniv' => I18N::translate('sort by date'),
        ];

        echo view('modules/todays_events/config', [
            'all_events'  => $all_events,
            'event_array' => $event_array,
            'filter'      => $filter,
            'infoStyle'   => $infoStyle,
            'info_styles' => $info_styles,
            'sortStyle'   => $sortStyle,
            'sort_styles' => $sort_styles,
        ]);
    }
}
