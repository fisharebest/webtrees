<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\I18N;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_keys;
use function response;
use function strip_tags;
use function view;

/**
 * Show help text.
 */
class HelpText implements RequestHandlerInterface
{
    private const FRENCH_DATES = [
        '@#DFRENCH R@ 12',
        '@#DFRENCH R@ VEND 12',
        'ABT @#DFRENCH R@ BRUM 12',
        'BET @#DFRENCH R@ FRIM 12 AND @#DFRENCH R@ NIVO 12',
        'FROM @#DFRENCH R@ PLUV 12 TO @#DFRENCH R@ VENT 12',
        'AFT @#DFRENCH R@ GERM 12',
        'BEF @#DFRENCH R@ FLOR 12',
        'ABT @#DFRENCH R@ PRAI 12',
        'FROM @#DFRENCH R@ MESS 12',
        'TO @#DFRENCH R@ THER 12',
        'EST @#DFRENCH R@ FRUC 12',
        '@#DFRENCH R@ 03 COMP 12',
    ];

    private const HIJRI_DATES = [
        '@#DHIJRI@ 1497',
        '@#DHIJRI@ MUHAR 1497',
        'ABT @#DHIJRI@ SAFAR 1497',
        'BET @#DHIJRI@ RABIA 1497 AND @#DHIJRI@ RABIT 1497',
        'FROM @#DHIJRI@ JUMAA 1497 TO @#DHIJRI@ JUMAT 1497',
        'AFT @#DHIJRI@ RAJAB 1497',
        'BEF @#DHIJRI@ SHAAB 1497',
        'ABT @#DHIJRI@ RAMAD 1497',
        'FROM @#DHIJRI@ SHAWW 1497',
        'TO @#DHIJRI@ DHUAQ 1497',
        '@#DHIJRI@ 03 DHUAH 1497',
    ];

    private const JALALI_DATES = [
        '@#DJALALI@ 1497',
        '@#DJALALI@ FARVA 1497',
        'ABT @#DJALALI@ ORDIB 1497',
        'BET @#DJALALI@ KHORD 1497 AND @#DHIJRI@ TIR 1497',
        'FROM @#DJALALI@ MORDA 1497 TO @#DHIJRI@ SHAHR 1497',
        'AFT @#DJALALI@ MEHR 1497',
        'BEF @#DJALALI@ ABAN 1497',
        'ABT @#DJALALI@ AZAR 1497',
        'FROM @#DJALALI@ DEY 1497',
        'TO @#DJALALI@ BAHMA 1497',
        '@#DJALALI@ 03 XXXXX 1497',
    ];

    private const JEWISH_DATES = [
        '@#DHEBREW@ 5481',
        '@#DHEBREW@ TSH 5481',
        'ABT @#DHEBREW@ CSH 5481',
        'BET @#DHEBREW@ KSL 5481 AND @#DHEBREW@ TVT 5481',
        'FROM @#DHEBREW@ SHV 5481 TO @#DHEBREW@ ADR 5481',
        'AFT @#DHEBREW@ ADR 5481',
        'AFT @#DHEBREW@ ADS 5480',
        'BEF @#DHEBREW@ NSN 5481',
        'ABT @#DHEBREW@ IYR 5481',
        'FROM @#DHEBREW@ SVN 5481',
        'TO @#DHEBREW@ TMZ 5481',
        'EST @#DHEBREW@ AAV 5481',
        '@#DHEBREW@ 03 ELL 5481',
    ];

    private const JULIAN_DATES = [
        '@#DJULIAN@ 14 JAN 1700',
        '@#DJULIAN@ 44 B.C.',
        '@#DJULIAN@ 20 FEB 1742/43',
        'BET @#DJULIAN@ 01 SEP 1752 AND @#DGREGORIAN@ 30 SEP 1752',
    ];

    private const DATE_SHORTCUTS = [
        '1900'           => [],
        'JAN 1900'       => [],
        'FEB 1900'       => [],
        'MAR 1900'       => [],
        'APR 1900'       => [],
        'MAY 1900'       => [],
        'JUN 1900'       => [],
        'JUL 1900'       => [],
        'AUG 1900'       => [],
        'SEP 1900'       => [],
        'OCT 1900'       => [],
        'NOV 1900'       => [],
        'DEC 1900'       => [],
        'ABT 1900'       => ['~1900'],
        'EST 1900'       => ['*1900'],
        'CAL 1900'       => ['#1900'],
        'INT 1900 (...)' => [],
    ];

    private const DATE_RANGE_SHORTCUTS = [
        'BET 1900 AND 1910'         => ['1900-1910'],
        'AFT 1900'                  => ['>1900'],
        'BEF 1910'                  => ['<1910'],
        'BET JAN 1900 AND MAR 1900' => ['Q1 1900'],
        'BET APR 1900 AND JUN 1900' => ['Q2 1900'],
        'BET JUL 1900 AND SEP 1900' => ['Q3 1900'],
        'BET OCT 1900 AND DEC 1900' => ['Q4 1900'],
    ];

    private const DATE_PERIOD_SHORTCUTS = [
        'FROM 1900 TO 1910' => ['1900~1910'],
        'FROM 1900'         => ['1900-'],
        'TO 1910'           => ['-1900'],
    ];

    private const DMY_SHORTCUTS = [
        '11 DEC 1913' => [
            '11/12/1913',
            '11-12-1913',
            '11.12.1913',
        ],
        '01 FEB 2003' => [
            '01/02/03',
            '01-02-03',
            '01.02.03',
        ],
    ];

    private const MDY_SHORTCUTS = [
        '11 DEC 1913' => [
            '12/11/1913',
            '12-11-1913',
            '12.11.1913',
        ],
        '01 FEB 2003' => [
            '02/01/03',
            '02-01-03',
            '02.01.03',
        ],
    ];

    private const YMD_SHORTCUTS = [
        '11 DEC 1913' => [
            '11/12/1913',
            '11-12-1913',
            '11.12.1913',
        ],
        '01 FEB 2003' => [
            '03/02/01',
            '03-02-01',
            '03.02.01',
        ],
    ];

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $topic = $request->getAttribute('topic');

        $dmy = I18N::language()->dateOrder();

        switch ($topic) {
            case 'DATE':
                switch ($dmy) {
                    case 'YMD':
                        $date_shortcuts = self::DATE_SHORTCUTS + self::YMD_SHORTCUTS;
                        break;
                    case 'MDY':
                        $date_shortcuts = self::DATE_SHORTCUTS + self::MDY_SHORTCUTS;
                        break;
                    case 'DMY':
                    default:
                        $date_shortcuts = self::DATE_SHORTCUTS + self::DMY_SHORTCUTS;
                        break;
                }

                $title = I18N::translate('Date');
                $text  = view('help/date', [
                    'date_dates'            => $this->formatDates(array_keys($date_shortcuts)),
                    'date_shortcuts'        => $date_shortcuts,
                    'date_period_dates'     => $this->formatDates(array_keys(self::DATE_PERIOD_SHORTCUTS)),
                    'date_period_shortcuts' => self::DATE_PERIOD_SHORTCUTS,
                    'date_range_dates'      => $this->formatDates(array_keys(self::DATE_RANGE_SHORTCUTS)),
                    'date_range_shortcuts'  => self::DATE_RANGE_SHORTCUTS,
                    'french_dates'          => $this->formatDates(self::FRENCH_DATES),
                    'hijri_dates'           => $this->formatDates(self::HIJRI_DATES),
                    'jalali_dates'          => $this->formatDates(self::JALALI_DATES),
                    'jewish_dates'          => $this->formatDates(self::JEWISH_DATES),
                    'julian_dates'          => $this->formatDates(self::JULIAN_DATES),
                ]);
                break;

            case 'NAME':
                $title = I18N::translate('Name');
                $text  = view('help/name');
                break;

            case 'SURN':
                $title = I18N::translate('Surname');
                $text  = view('help/surname');
                break;

            case 'OBJE':
                $title = I18N::translate('Media object');
                $text  = view('help/media-object');
                break;

            case 'PLAC':
                $title = I18N::translate('Place');
                $text  = view('help/place');
                break;

            case 'RESN':
                $title = I18N::translate('Restriction');
                $text  = view('help/restriction');
                break;

            case 'ROMN':
                $title = I18N::translate('Romanized');
                $text  = view('help/romanized');
                break;

            case '_HEB':
                $title = I18N::translate('Hebrew');
                $text  = view('help/hebrew');
                break;

            case 'data-fixes':
                $title = I18N::translate('Data fixes');
                $text  = view('help/data-fixes');
                break;

            case 'edit_SOUR_EVEN':
                $title = I18N::translate('Associate events with this source');
                $text  = view('help/source-events');
                break;

            case 'pending_changes':
                $title = I18N::translate('Pending changes');
                $text  = view('help/pending-changes', [
                    'is_admin' => Auth::isAdmin(),
                ]);
                break;

            case 'relationship-privacy':
                $title = I18N::translate('Restrict to immediate family');
                $text  = view('help/relationship-privacy');
                break;

            default:
                $title = I18N::translate('Help');
                $text  = I18N::translate('The help text has not been written for this item.');
                break;
        }

        $html = view('modals/help', [
            'title' => $title,
            'text'  => $text,
        ]);

        return response($html);
    }

    /**
     * Format GEDCOM dates in the local language.
     *
     * @param array<string>|array<int> $gedcom_dates
     *
     * @return array<string>
     */
    private function formatDates(array $gedcom_dates): array
    {
        $dates = [];

        foreach ($gedcom_dates as $gedcom_date) {
            // PHP converts numeric array keys ('1900') to integers (1900), so reverse this.
            $gedcom_date = (string) $gedcom_date;

            $date                = new Date($gedcom_date);
            $dates[$gedcom_date] = strip_tags($date->display());
        }

        return $dates;
    }
}
