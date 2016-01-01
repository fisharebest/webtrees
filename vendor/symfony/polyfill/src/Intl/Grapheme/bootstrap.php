<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Intl\Grapheme as p;

if (!function_exists('grapheme_strlen')) {
    define('GRAPHEME_EXTR_COUNT', 0);
    define('GRAPHEME_EXTR_MAXBYTES', 1);
    define('GRAPHEME_EXTR_MAXCHARS', 2);

    function grapheme_extract($s, $size, $type = 0, $start = 0, &$next = 0) { return p\Grapheme::grapheme_extract($s, $size, $type, $start, $next); }
    function grapheme_stripos($s, $needle, $offset = 0) { return p\Grapheme::grapheme_stripos($s, $needle, $offset); }
    function grapheme_stristr($s, $needle, $beforeNeedle = false) { return p\Grapheme::grapheme_stristr($s, $needle, $beforeNeedle); }
    function grapheme_strlen($s) { return p\Grapheme::grapheme_strlen($s); }
    function grapheme_strpos($s, $needle, $offset = 0) { return p\Grapheme::grapheme_strpos($s, $needle, $offset); }
    function grapheme_strripos($s, $needle, $offset = 0) { return p\Grapheme::grapheme_strripos($s, $needle, $offset); }
    function grapheme_strrpos($s, $needle, $offset = 0) { return p\Grapheme::grapheme_strrpos($s, $needle, $offset); }
    function grapheme_strstr($s, $needle, $beforeNeedle = false) { return p\Grapheme::grapheme_strstr($s, $needle, $beforeNeedle); }
    function grapheme_substr($s, $start, $len = 2147483647) { return p\Grapheme::grapheme_substr($s, $start, $len); }
}
