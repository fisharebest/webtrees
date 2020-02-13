<?php

namespace Iodev\Whois\Helpers;

class TextHelper
{
    /**
     * @param string $text
     * @return string
     */
    public static function toUtf8($text)
    {
        $srcEncoding = strtolower(mb_detect_encoding($text));
        if (!empty($srcEncoding) && $srcEncoding !== 'utf-8') {
            return mb_convert_encoding($text, 'utf-8', $srcEncoding);
        }
        if (mb_check_encoding($text, 'utf-8')) {
            return $text;
        }
        if (mb_check_encoding($text, 'windows-1252')) {
            return iconv('windows-1252', 'utf-8', $text);
        }
        if (mb_check_encoding($text, 'windows-1251')) {
            return iconv('windows-1251', 'utf-8', $text);
        }
        return $text;
    }
}
