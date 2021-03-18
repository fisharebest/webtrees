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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UpcomingAnniversariesModule
 */
class UpcomingAnniversariesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Default values for new blocks.
    private const DEFAULT_DAYS   = '7';
    private const DEFAULT_FILTER = '1';
    private const DEFAULT_SORT   = 'alpha';
    private const DEFAULT_STYLE  = 'table';

    // Initial sorting for datatables
    private const DATATABLES_ORDER = [
        'alpha' => [[0, 'asc']],
        'anniv' => [[1, 'asc']],
    ];

    // Can show this number of days into the future.
    private const MAX_DAYS = 30;

    // Pagination
    private const LIMIT_LOW  = 10;
    private const LIMIT_HIGH = 20;

    // All standard GEDCOM 5.5.1 events except CENS, RESI and EVEN
    private const ALL_EVENTS = [
        'ADOP' => 'INDI:ADOP',
        'ANUL' => 'FAM:ANUL',
        'BAPM' => 'INDI:BAPM',
        'BARM' => 'INDI:BARM',
        'BASM' => 'INDI:BASM',
        'BIRT' => 'INDI:BIRT',
        'BLES' => 'INDI:BLES',
        'BURI' => 'INDI:BURI',
        'CHR'  => 'INDI:CHR',
        'CHRA' => 'INDI:CHRA',
        'CONF' => 'INDI:CONF',
        'CREM' => 'INDI:CREM',
        'DEAT' => 'INDI:DEAT',
        'DIV'  => 'FAM:DIV',
        'DIVF' => 'FAM:DIVF',
        'EMIG' => 'INDI:EMIG',
        'ENGA' => 'FAM:ENGA',
        'FCOM' => 'INDI:FCOM',
        'GRAD' => 'INDI:GRAD',
        'IMMI' => 'INDI:IMMI',
        'MARB' => 'FAM:MARB',
        'MARC' => 'FAM:MARC',
        'MARL' => 'FAM:MARL',
        'MARR' => 'FAM:MARR',
        'MARS' => 'FAM:MARS',
        'NATU' => 'INDI:NATU',
        'ORDN' => 'INDI:ORDN',
        'PROB' => 'INDI:PROB',
        'RETI' => 'INDI:RETI',
        'WILL' => 'INDI:WILL',
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
        return I18N::translate('Upcoming events');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Upcoming events” module */
        return I18N::translate('A list of the anniversaries that will occur in the near future.');
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

        $days      = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $filter    = (bool) $this->getBlockSetting($block_id, 'filter', self::DEFAULT_FILTER);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT);
        $events    = $this->getBlockSetting($block_id, 'events', $default_events);

        extract($config, EXTR_OVERWRITE);

        $event_array = explode(',', $events);

        // If we are only showing living individuals, then we don't need to search for DEAT events.
        if ($filter) {
            $event_array  = array_diff($event_array, Gedcom::DEATH_EVENTS);
        }

        $events_filter = implode('|', $event_array);

        $startjd = Carbon::now()->julianDay() + 1;
        $endjd   = Carbon::now()->julianDay() + $days;

        $facts = $calendar_service->getEventsList($startjd, $endjd, $events_filter, $filter, $sortStyle, $tree);

        if ($facts->isEmpty()) {
            if ($endjd == $startjd) {
                $content = view('modules/upcoming_events/empty', [
                    'message' => I18N::translate('No events exist for tomorrow.'),
                ]);
            } else {
                /* I18N: translation for %s==1 is unused; it is translated separately as “tomorrow” */                $content = view('modules/upcoming_events/empty', [
                    'message' => I18N::plural('No events exist for the next %s day.', 'No events exist for the next %s days.', $endjd - $startjd + 1, I18N::number($endjd - $startjd + 1)),
                ]);
            }
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

        $this->setBlockSetting($block_id, 'days', $params['days']);
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

        $days       = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $filter     = $this->getBlockSetting($block_id, 'filter', self::DEFAULT_FILTER);
        $info_style = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);
        $sort_style = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT);
        $events     = $this->getBlockSetting($block_id, 'events', $default_events);

        $event_array = explode(',', $events);

        $all_events = [];
        foreach (self::ALL_EVENTS as $event => $tag) {
            $all_events[$event] = Registry::elementFactory()->make($tag)->label();
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

        return view('modules/upcoming_events/config', [
            'all_events'  => $all_events,
            'days'        => $days,
            'event_array' => $event_array,
            'filter'      => $filter,
            'info_style'  => $info_style,
            'info_styles' => $info_styles,
            'max_days'    => self::MAX_DAYS,
            'sort_style'  => $sort_style,
            'sort_styles' => $sort_styles,
        ]);
    }
}
