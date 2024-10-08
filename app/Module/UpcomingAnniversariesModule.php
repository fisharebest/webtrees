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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UpcomingAnniversariesModule
 */
class UpcomingAnniversariesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private const SORT_STYLE_DATE = 'anniv';
    private const SORT_STYLE_NAME = 'alpha';

    private const LAYOUT_STYLE_LIST  = 'list';
    private const LAYOUT_STYLE_TABLE = 'table';

    // Default values for new blocks.
    private const DEFAULT_DAYS   = '7';
    private const DEFAULT_FILTER = '1';
    private const DEFAULT_SORT   = self::SORT_STYLE_NAME;
    private const DEFAULT_STYLE  = self::LAYOUT_STYLE_TABLE;

    // Initial sorting for datatables
    private const DATATABLES_ORDER = [
        self::SORT_STYLE_NAME => [[0, 'asc']],
        self::SORT_STYLE_DATE => [[1, 'asc']],
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

    private CalendarService $calendar_service;

    /**
     * @param CalendarService $calendar_service
     */
    public function __construct(CalendarService $calendar_service)
    {
        $this->calendar_service = $calendar_service;
    }

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

    public function description(): string
    {
        /* I18N: Description of the “Upcoming events” module */
        return I18N::translate('A list of the anniversaries that will occur in the near future.');
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
        $default_events = implode(',', self::DEFAULT_EVENTS);

        $days      = (int)$this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $filter    = (bool)$this->getBlockSetting($block_id, 'filter', self::DEFAULT_FILTER);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);
        $sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT);
        $events    = $this->getBlockSetting($block_id, 'events', $default_events);

        extract($config, EXTR_OVERWRITE);

        $event_array = explode(',', $events);

        // If we are only showing living individuals, then we don't need to search for DEAT events.
        if ($filter) {
            $event_array = array_diff($event_array, Gedcom::DEATH_EVENTS);
        }

        $events_filter = implode('|', $event_array);

        $startjd = Registry::timestampFactory()->now()->addDays(1)->julianDay();
        $endjd   = Registry::timestampFactory()->now()->addDays($days)->julianDay();

        $facts = $this->calendar_service->getEventsList($startjd, $endjd, $events_filter, $filter, $sortStyle, $tree);

        if ($facts->isEmpty()) {
            if ($endjd === $startjd) {
                if ($filter && Auth::check()) {
                    $message = I18N::translate('No events for living individuals exist for tomorrow.');
                } else {
                    $message = I18N::translate('No events exist for tomorrow.');
                }
            } else {
                if ($filter && Auth::check()) {
                    $message = I18N::plural('No events for living people exist for the next %s day.', 'No events for living people exist for the next %s days.', $endjd - $startjd + 1, I18N::number($endjd - $startjd + 1));
                } else {
                    $message = I18N::plural('No events exist for the next %s day.', 'No events exist for the next %s days.', $endjd - $startjd + 1, I18N::number($endjd - $startjd + 1));
                }
            }

            $content = view('modules/upcoming_events/empty', ['message' => $message]);
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
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $days       = Validator::parsedBody($request)->isBetween(1, self::MAX_DAYS)->integer('days');
        $filter     = Validator::parsedBody($request)->boolean('filter');
        $info_style = Validator::parsedBody($request)->isInArrayKeys($this->infoStyles())->string('infoStyle');
        $sort_style = Validator::parsedBody($request)->isInArrayKeys($this->sortStyles())->string('sortStyle');
        $events     = Validator::parsedBody($request)->array('events');

        $this->setBlockSetting($block_id, 'days', (string)$days);
        $this->setBlockSetting($block_id, 'filter', (string)$filter);
        $this->setBlockSetting($block_id, 'infoStyle', $info_style);
        $this->setBlockSetting($block_id, 'sortStyle', $sort_style);
        $this->setBlockSetting($block_id, 'events', implode(',', $events));
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

        return view('modules/upcoming_events/config', [
            'all_events'  => $all_events,
            'days'        => $days,
            'event_array' => $event_array,
            'filter'      => $filter,
            'info_style'  => $info_style,
            'info_styles' => $this->infoStyles(),
            'max_days'    => self::MAX_DAYS,
            'sort_style'  => $sort_style,
            'sort_styles' => $this->sortStyles(),
        ]);
    }

    /**
     * @return array<string,string>
     */
    private function infoStyles(): array
    {
        return [
            self::LAYOUT_STYLE_LIST  => /* I18N: An option in a list-box */ I18N::translate('list'),
            self::LAYOUT_STYLE_TABLE => /* I18N: An option in a list-box */ I18N::translate('table'),
        ];
    }

    /**
     * @return array<string,string>
     */
    private function sortStyles(): array
    {
        return [
            self::SORT_STYLE_NAME => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
            self::SORT_STYLE_DATE => /* I18N: An option in a list-box */ I18N::translate('sort by date'),
        ];
    }
}
