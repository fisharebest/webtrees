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

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class OnThisDayModule
 */
class OnThisDayModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Pagination
    private const LIMIT_LOW  = 10;
    private const LIMIT_HIGH = 20;

    // Initial sorting for datatables
    private const DATATABLES_ORDER = [
        'alpha' => [[0, 'asc']],
        'anniv' => [[2, 'asc']],
    ];

    // All standard GEDCOM 5.5.1 events except CENS, RESI and EVEN
    private const ALL_EVENTS = [
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

    private const DEFAULT_EVENTS = [
        'BIRT',
        'MARR',
        'DEAT',
    ];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('On this day');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “On this day” module */
        return I18N::translate('A list of the anniversaries that occur today.');
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
        $calendar_service = new CalendarService();

        $default_events = implode(',', self::DEFAULT_EVENTS);

        $filter    = (bool) $this->getBlockSetting($block_id, 'filter', '1');
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
        $events    = $this->getBlockSetting($block_id, 'events', $default_events);

        extract($config, EXTR_OVERWRITE);

        $event_array = explode(',', $events);

        // If we are only showing living individuals, then we don't need to search for DEAT events.
        if ($filter) {
            $event_array  = array_diff($event_array, Gedcom::DEATH_EVENTS);
        }

        $events_filter = implode('|', $event_array);

        $startjd = Carbon::now()->julianDay();
        $endjd   = $startjd;

        $facts = $calendar_service->getEventsList($startjd, $endjd, $events_filter, $filter, $sortStyle, $tree);

        if ($facts->isEmpty()) {
            $content = view('modules/todays_events/empty');
        } elseif ($infoStyle === 'list') {
            $content = view('lists/anniversaries-list', [
                'id'         => $block_id,
                'facts'      => $facts,
                'limit_low'  => self::LIMIT_LOW,
                'limit_high' => self::LIMIT_HIGH,
            ]);
        } else {
            $content = view('lists/anniversaries-table', [
                'facts'      => $facts,
                'limit_low'  => self::LIMIT_LOW,
                'limit_high' => self::LIMIT_HIGH,
                'order'      => self::DATATABLES_ORDER[$sortStyle],
            ]);
        }

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

        $this->setBlockSetting($block_id, 'filter', $params['filter']);
        $this->setBlockSetting($block_id, 'infoStyle', $params['infoStyle']);
        $this->setBlockSetting($block_id, 'sortStyle', $params['sortStyle']);
        $this->setBlockSetting($block_id, 'events', implode(',', $params['events'] ?? []));
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

        return view('modules/todays_events/config', [
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
