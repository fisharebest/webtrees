<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Authors:
 * - monkeycon
 * - François B
 * - Jason Katz-Brown
 * - Serhan Apaydın
 * - Matt Johnson
 * - JD Isaacks
 * - Zeno Zeng
 * - Chris Hemp
 * - shankesgk2
 */
return [
    'year' => ':count年',
    'y' => ':count年',
    'month' => ':count个月',
    'm' => ':count个月',
    'week' => ':count周',
    'w' => ':count周',
    'day' => ':count天',
    'd' => ':count天',
    'hour' => ':count小时',
    'h' => ':count小时',
    'minute' => ':count分钟',
    'min' => ':count分钟',
    'second' => ':count秒',
    'a_second' => '{1}几秒|]1,Inf[:count秒',
    's' => ':count秒',
    'ago' => ':time前',
    'from_now' => ':time内',
    'after' => ':time后',
    'before' => ':time前',
    'diff_yesterday' => '昨天',
    'diff_tomorrow' => '明天',
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'YYYY/MM/DD',
        'LL' => 'YYYY年M月D日',
        'LLL' => 'YYYY年M月D日Ah点mm分',
        'LLLL' => 'YYYY年M月D日ddddAh点mm分',
    ],
    'calendar' => [
        'sameDay' => '[今天]LT',
        'nextDay' => '[明天]LT',
        'nextWeek' => '[下]ddddLT',
        'lastDay' => '[昨天]LT',
        'lastWeek' => '[上]ddddLT',
        'sameElse' => 'L',
    ],
    'ordinal' => function ($number, $period) {
        switch ($period) {
            case 'd':
            case 'D':
            case 'DDD':
                return $number.'日';
            case 'M':
                return $number.'月';
            case 'w':
            case 'W':
                return $number.'周';
            default:
                return $number;
        }
    },
    'meridiem' => function ($hour, $minute) {
        $time = $hour * 100 + $minute;
        if ($time < 600) {
            return '凌晨';
        }
        if ($time < 900) {
            return '早上';
        }
        if ($time < 1130) {
            return '上午';
        }
        if ($time < 1230) {
            return '中午';
        }
        if ($time < 1800) {
            return '下午';
        }

        return '晚上';
    },
    'months' => ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
    'months_short' => ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
    'weekdays' => ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
    'weekdays_short' => ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
    'weekdays_min' => ['日', '一', '二', '三', '四', '五', '六'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 4,
    'list' => '',
];
