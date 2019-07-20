<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

final class UrlEncoder
{
    protected static $dontEncode = [
        '%21' => '!',
        '%23' => '#',
        '%24' => '$',
        '%26' => '&',
        '%27' => '\'',
        '%28' => '(',
        '%29' => ')',
        '%2A' => '*',
        '%2B' => '+',
        '%2C' => ',',
        '%2D' => '-',
        '%2E' => '.',
        '%2F' => '/',
        '%3A' => ':',
        '%3B' => ';',
        '%3D' => '=',
        '%3F' => '?',
        '%40' => '@',
        '%5F' => '_',
        '%7E' => '~',
    ];

    protected static $dontDecode = [
        ';',
        '/',
        '?',
        ':',
        '@',
        '&',
        '=',
        '+',
        '$',
        ',',
        '#',
    ];

    /**
     * @param string $uri
     *
     * @return string
     */
    public static function unescapeAndEncode(string $uri): string
    {
        $decoded = \html_entity_decode($uri);

        return self::encode(self::decode($decoded));
    }

    /**
     * Decode a percent-encoded URI
     *
     * @param string $uri
     *
     * @return string
     */
    private static function decode(string $uri): string
    {
        return \preg_replace_callback('/%([0-9a-f]{2})/iu', function ($matches) {
            $char = \chr(\hexdec($matches[1]));

            if (\in_array($char, self::$dontDecode, true)) {
                return \strtoupper($matches[0]);
            }

            return $char;
        }, $uri);
    }

    /**
     * Encode a URI, preserving already-encoded and excluded characters
     *
     * @param string $uri
     *
     * @return string
     */
    private static function encode(string $uri): string
    {
        return \preg_replace_callback('/(%[0-9a-f]{2})|./isu', function ($matches) {
            // Keep already-encoded characters as-is
            if (\count($matches) > 1) {
                return $matches[0];
            }

            // Keep excluded characters as-is
            if (\in_array($matches[0], self::$dontEncode)) {
                return $matches[0];
            }

            // Otherwise, encode the character
            return \rawurlencode($matches[0]);
        }, $uri);
    }
}
