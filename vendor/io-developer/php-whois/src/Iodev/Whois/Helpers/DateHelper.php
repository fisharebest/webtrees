<?php

namespace Iodev\Whois\Helpers;

class DateHelper
{
    /**
     * @param string $datestamp
     * @param bool $inverseMMDD
     * @return int
     */
    public static function parseDate($datestamp, $inverseMMDD = false)
    {
        $s = trim($datestamp);
        if (preg_match('/^\d{2}[-\s]+\w+[-\s]+\d{4}[-\s]+\d{2}:\d{2}(:\d{2})?([-\s]+\w+)?/ui', $s)) {
            // pass
        } elseif (preg_match('/^(\d{4})\.\s*(\d{2})\.\s*(\d{2})\.?\s*$/ui', $s, $m)) {
            $s = "{$m[1]}-{$m[2]}-{$m[3]}T00:00:00";
        } elseif (preg_match('/^\d{4}\.\d{2}\.\d{2}\s+\d{2}:\d{2}:\d{2}/ui', $s)) {
            $s = str_replace(".", "-", $s);
        } elseif (preg_match('/^(\d{2})-(\w+)-(\d{4})\s+(\d{2}:\d{2}:\d{2})/ui', $s, $m)) {
            $mon = self::textMonthToDigital($m[2]);
            $s = "{$m[3]}-{$mon}-{$m[1]}T{$m[4]}";
        } elseif (preg_match('/^(\d{2})[-\.](\d{2})[-\.](\d{4})$/ui', $s, $m)) {
            $s = "{$m[3]}-{$m[2]}-{$m[1]}T00:00:00";
        } elseif (preg_match('/^(\d{2})[-\s]+(\w+)[-\s]+(\d{4})/ui', $s, $m)) {
            $mon = self::textMonthToDigital($m[2]);
            $s = "{$m[3]}-{$mon}-{$m[1]}T00:00:00";
        } elseif (preg_match('/^(\d{4})(\d{2})(\d{2})$/ui', preg_replace('/\s*#.*/ui', '', $s), $m)) {
            $s = "{$m[1]}-{$m[2]}-{$m[3]}T00:00:00";
        } elseif (preg_match('~^(\d{2})/(\d{2})/(\d{4})$~ui', $s, $m)) {
            $s = $inverseMMDD
                ? "{$m[3]}-{$m[2]}-{$m[1]}T00:00:00"
                : "{$m[3]}-{$m[1]}-{$m[2]}T00:00:00";
        } elseif (preg_match('/^(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2}:\d{2})\s+\(GMT([-+]\d+:\d{2})\)$/ui', $s, $m)) {
            $s = "{$m[1]}T{$m[2]}{$m[3]}";
        }
        // Fix timezone parsing for PHP 5.4
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $s = str_replace('WAT', '+0100', $s);
        }
        return (int)strtotime($s);
    }

    /**
     * @param string $text
     * @return int
     */
    public static function parseDateInText($text)
    {
        if (preg_match('~\b(\d{1,2})(nd|th|st)?[-\s]+([a-z]+)[-\s]+(\d{4})\b~ui', $text, $m)) {
            return strtotime("{$m[1]} {$m[3]} {$m[4]} 00:00");
        }
        if (preg_match('~\b(\d{1,2})(nd|th|st)?[-\s]+([a-z]+)\b~ui', $text, $m)) {
            $y = date('Y');
            return strtotime("{$m[1]} {$m[3]} $y 00:00");
        }
        return 0;
    }

    /**
     * @param $mon
     * @return string
     */
    public static function textMonthToDigital($mon)
    {
        $mond = [
            'jan' => '01',
            'january' => '01',
            'feb' => '02',
            'february' => '02',
            'mar' => '03',
            'march' => '03',
            'apr' => '04',
            'april' => '04',
            'may' => '05',
            'jun' => '06',
            'june' => '06',
            'jul' => '07',
            'july' => '07',
            'aug' => '08',
            'august' => '08',
            'sep' => '09',
            'september' => '09',
            'oct' => '10',
            'october' => '10',
            'nov' => '11',
            'november' => '11',
            'dec' => '12',
            'december' => '12',
        ];
        return $mond[strtolower($mon)];
    }
}
