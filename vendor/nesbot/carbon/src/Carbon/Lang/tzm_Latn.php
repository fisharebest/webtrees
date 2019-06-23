<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
\Symfony\Component\Translation\PluralizationRules::set(function ($number) {
    return $number === 1 ? 0 : 1;
}, 'tzm');

/*
 * Authors:
 * - Josh Soref
 * - JD Isaacks
 */
return [
    'year' => ':count asgas|:count isgasn',
    'a_year' => 'asgas|:count isgasn',
    'month' => ':count ayowr|:count iyyirn',
    'a_month' => 'ayowr|:count iyyirn',
    'week' => ':count imalass',
    'a_week' => ':imalass',
    'day' => ':count ass|:count ossan',
    'a_day' => 'ass|:count ossan',
    'hour' => ':count saɛa|:count tassaɛin',
    'a_hour' => 'saɛa|:count tassaɛin',
    'minute' => ':count minuḍ',
    'a_minute' => 'minuḍ|:count minuḍ',
    'second' => ':count imik',
    'a_second' => 'imik|:count imik',
    'ago' => 'yan :time',
    'from_now' => 'dadkh s yan :time',
    'diff_yesterday' => 'assant',
    'diff_tomorrow' => 'aska',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD/MM/YYYY',
        'LL' => 'D MMMM YYYY',
        'LLL' => 'D MMMM YYYY HH:mm',
        'LLLL' => 'dddd D MMMM YYYY HH:mm',
    ],
    'calendar' => [
        'sameDay' => '[asdkh g] LT',
        'nextDay' => '[aska g] LT',
        'nextWeek' => 'dddd [g] LT',
        'lastDay' => '[assant g] LT',
        'lastWeek' => 'dddd [g] LT',
        'sameElse' => 'L',
    ],
    'months' => ['innayr', 'brˤayrˤ', 'marˤsˤ', 'ibrir', 'mayyw', 'ywnyw', 'ywlywz', 'ɣwšt', 'šwtanbir', 'ktˤwbrˤ', 'nwwanbir', 'dwjnbir'],
    'months_short' => ['innayr', 'brˤayrˤ', 'marˤsˤ', 'ibrir', 'mayyw', 'ywnyw', 'ywlywz', 'ɣwšt', 'šwtanbir', 'ktˤwbrˤ', 'nwwanbir', 'dwjnbir'],
    'weekdays' => ['asamas', 'aynas', 'asinas', 'akras', 'akwas', 'asimwas', 'asiḍyas'],
    'weekdays_short' => ['asamas', 'aynas', 'asinas', 'akras', 'akwas', 'asimwas', 'asiḍyas'],
    'weekdays_min' => ['asamas', 'aynas', 'asinas', 'akras', 'akwas', 'asimwas', 'asiḍyas'],
    'meridiem' => ['Zdat azal', 'Ḍeffir aza'],
    'first_day_of_week' => 6,
    'day_of_first_week_of_year' => 1,
];
