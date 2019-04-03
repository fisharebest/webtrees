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

use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class YahrzeitModule
 */
class YahrzeitModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Default values for new blocks.
    private const DEFAULT_CALENDAR = 'jewish';
    private const DEFAULT_DAYS     = '7';
    private const DEFAULT_STYLE    = 'table';

    // Can show this number of days into the future.
    private const MAX_DAYS = 30;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module. Yahrzeiten (the plural of Yahrzeit) are special anniversaries of deaths in the Hebrew faith/calendar. */
        return I18N::translate('Yahrzeiten');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Yahrzeiten” module. A “Hebrew death” is a death where the date is recorded in the Hebrew calendar. */
        return I18N::translate('A list of the Hebrew death anniversaries that will occur in the near future.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $ctype
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $ctype = '', array $cfg = []): string
    {
        $calendar_service = new CalendarService();

        $days      = (int) $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);
        $calendar  = $this->getBlockSetting($block_id, 'calendar', self::DEFAULT_CALENDAR);

        extract($cfg, EXTR_OVERWRITE);

        $jewish_calendar = new JewishCalendar();
        $startjd         = Carbon::now()->julianDay();
        $endjd           = $startjd + $days - 1;

        // The standard anniversary rules cover most of the Yahrzeit rules, we just
        // need to handle a few special cases.
        // Fetch normal anniversaries, with an extra day before/after
        $yahrzeits = [];
        for ($jd = $startjd - 1; $jd <= $endjd + $days; ++$jd) {
            foreach ($calendar_service->getAnniversaryEvents($jd, 'DEAT _YART', $tree) as $fact) {
                // Exact hebrew dates only
                $date = $fact->date();
                if ($date->minimumDate() instanceof JewishDate && $date->minimumJulianDay() === $date->maximumJulianDay()) {
                    // ...then adjust DEAT dates (but not _YART)
                    if ($fact->getTag() === 'DEAT') {
                        $today     = new JewishDate($jd);
                        $hd        = $fact->date()->minimumDate();
                        $hd1       = new JewishDate($hd);
                        ++$hd1->year;
                        $hd1->setJdFromYmd();
                        // Special rules. See http://www.hebcal.com/help/anniv.html
                        // Everything else is taken care of by our standard anniversary rules.
                        if ($hd->day == 30 && $hd->month == 2 && $hd->year != 0 && $hd1->daysInMonth() < 30) {
                            // 30 CSH - Last day in CSH
                            $jd = $jewish_calendar->ymdToJd($today->year, 3, 1) - 1;
                        } elseif ($hd->day == 30 && $hd->month == 3 && $hd->year != 0 && $hd1->daysInMonth() < 30) {
                            // 30 KSL - Last day in KSL
                            $jd = $jewish_calendar->ymdToJd($today->year, 4, 1) - 1;
                        } elseif ($hd->day == 30 && $hd->month == 6 && $hd->year != 0 && $today->daysInMonth() < 30 && !$today->isLeapYear()) {
                            // 30 ADR - Last day in SHV
                            $jd = $jewish_calendar->ymdToJd($today->year, 6, 1) - 1;
                        }
                    }

                    // Filter adjusted dates to our date range
                    if ($jd >= $startjd && $jd < $startjd + $days) {
                        // upcomming yahrzeit dates
                        switch ($calendar) {
                            case 'gregorian':
                                $yahrzeit_date = new GregorianDate($jd);
                                break;
                            case 'jewish':
                            default:
                                $yahrzeit_date = new JewishDate($jd);
                                break;
                        }
                        $yahrzeit_date = new Date($yahrzeit_date->format('%@ %A %O %E'));

                        $yahrzeits[] = (object) [
                            'individual'    => $fact->record(),
                            'fact_date'     => $fact->date(),
                            'fact'          => $fact,
                            'jd'            => $jd,
                            'yahrzeit_date' => $yahrzeit_date,
                        ];
                    }
                }
            }
        }

        switch ($infoStyle) {
            case 'list':
                $content = view('modules/yahrzeit/list', [
                    'yahrzeits' => $yahrzeits,
                ]);
                break;
            case 'table':
            default:
                $content = view('modules/yahrzeit/table', [
                    'yahrzeits' => $yahrzeits,
                ]);
                break;
        }

        if ($ctype !== '') {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $config_url,
                'title'      => $this->title(),
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
        $this->setBlockSetting($block_id, 'days', $request->get('days', self::DEFAULT_DAYS));
        $this->setBlockSetting($block_id, 'infoStyle', $request->get('infoStyle', self::DEFAULT_STYLE));
        $this->setBlockSetting($block_id, 'calendar', $request->get('calendar', self::DEFAULT_CALENDAR));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): void
    {
        $calendar  = $this->getBlockSetting($block_id, 'calendar', 'jewish');
        $days      = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');

        $styles = [
            /* I18N: An option in a list-box */
            'list'  => I18N::translate('list'),
            /* I18N: An option in a list-box */
            'table' => I18N::translate('table'),
        ];

        $calendars = [
            'jewish'    => I18N::translate('Jewish'),
            'gregorian' => I18N::translate('Gregorian'),
        ];

        echo view('modules/yahrzeit/config', [
            'calendar'  => $calendar,
            'calendars' => $calendars,
            'days'      => $days,
            'infoStyle' => $infoStyle,
            'max_days'  => self::MAX_DAYS,
            'styles'    => $styles,
        ]);
    }
}
