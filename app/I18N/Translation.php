<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\I18N;

use Fisharebest\Webtrees\Enums\ByteOrder;
use RuntimeException;

use function array_filter;
use function array_map;
use function dechex;
use function explode;
use function fclose;
use function fgetcsv;
use function file;
use function fopen;
use function fread;
use function fseek;
use function implode;
use function is_array;
use function ksort;
use function preg_match;
use function preg_split;
use function str_starts_with;
use function strtr;
use function substr;
use function trim;
use function unpack;

final readonly class Translation
{
    public const string PLURAL_SEPARATOR  = "\x00";
    public const string CONTEXT_SEPARATOR = "\x04";

    private const array PO_ESCAPE_CHARACTERS = [
        '\\\\' => '\\',
        '\\a'  => "\x07",
        '\\b'  => "\x08",
        '\\f'  => "\x0c",
        '\\n'  => "\n",
        '\\r'  => "\r",
        '\\t'  => "\t",
        '\\v'  => "\x0b",
        '\\"'  => '"',
    ];

    /**
     * @param array<string,string> $translations An association of English -> translated messages
     */
    private function __construct(private array $translations)
    {
    }

    /**
     * @param array<string,string> $extra
     */
    public function withMessages(array $extra): self
    {
        return new self($extra + $this->translations);
    }

    /**
     * Create a Translation from a .csv file (semicolon-delimited).
     */
    public static function fromCsvFile(string $filename): self
    {
        $translations = [];

        $fp = fopen($filename, 'rb');

        if ($fp === false) {
            throw new RuntimeException('Cannot open file: ' . $filename);
        }

        while (($data = fgetcsv($fp, 0, ';')) !== false) {
            if (isset($data[0], $data[1])) {
                $translations[$data[0]] = $data[1];
            }
        }

        fclose($fp);

        return new self($translations);
    }

    /**
     * Create a Translation from a compiled .mo (gettext) file.
     * We don't use this - but third-party modules might.
     *
     * @link https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
     */
    public static function fromMoFile(string $filename): self
    {
        $fp = fopen($filename, 'rb');

        if ($fp === false) {
            throw new RuntimeException('Cannot open file: ' . $filename);
        }

        $translations = self::parseMoFile($fp);

        fclose($fp);

        return new self($translations);
    }

    /**
     * Create a Translation from a .po (gettext source) file.
     *
     * @link https://www.gnu.org/software/gettext/manual/html_node/PO-Files.html
     */
    public static function fromPoFile(string $filename): self
    {
        $lines = file($filename);

        if ($lines === false) {
            throw new RuntimeException('Cannot read file: ' . $filename);
        }

        return new self(self::parsePoFile($lines));
    }

    /**
     * Create a Translation from a .php file that returns an array.
     */
    public static function fromPhpFile(string $filename): self
    {
        $translations = include $filename;

        if (!is_array($translations)) {
            throw new RuntimeException('File does not return an array: ' . $filename);
        }

        return new self($translations);
    }

    /**
     * The translation strings.
     *
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return $this->translations;
    }

    /**
     * @param resource $fp
     *
     * @return int[]
     */
    private static function readMoData($fp, int $offset, int $count, ByteOrder $byte_order): array
    {
        fseek($fp, $offset);

        return unpack($byte_order->value . $count, fread($fp, $count * 4));
    }

    /**
     * @param resource $fp
     *
     * @return array<string,string>
     */
    private static function parseMoFile($fp): array
    {
        $translations = [];

        $magic = self::readMoData($fp, 0, 1, ByteOrder::LittleEndian);

        $byte_order = ByteOrder::fromMoMagicString(dechex($magic[1]));

        // Read the lookup tables
        [, $number_of_strings, $offset_original, $offset_translated] = self::readMoData($fp, 8, 3, $byte_order);
        $lookup_original   = self::readMoData($fp, $offset_original, $number_of_strings * 2, $byte_order);
        $lookup_translated = self::readMoData($fp, $offset_translated, $number_of_strings * 2, $byte_order);

        // Read the strings
        for ($n = 1; $n < $number_of_strings; ++$n) {
            fseek($fp, $lookup_original[$n * 2 + 2]);
            $original = fread($fp, $lookup_original[$n * 2 + 1]);
            fseek($fp, $lookup_translated[$n * 2 + 2]);
            $translated              = fread($fp, $lookup_translated[$n * 2 + 1]);
            $translations[$original] = $translated;
        }

        return $translations;
    }

    /**
     * @param array<string> $lines
     *
     * @return array<string,string>
     */
    private static function parsePoFile(array $lines): array
    {
        $translations = [];

        // Strip comments
        $lines = array_filter($lines, fn(string $line): bool => !str_starts_with((string) $line, '#'));

        // Trim carriage-returns, newlines, spaces
        $lines = array_map(trim(...), $lines);

        // Merge continuation lines
        $tmp = trim(implode("\n", $lines));
        $tmp = str_replace("\"\n\"", '', $tmp);

        // Split into separate translations
        $entries = preg_split("/\n{2,}/", $tmp);

        foreach ($entries as $entry) {
            $parts = explode("\n", $entry);

            $msgctxt      = '';
            $msgid        = '';
            $msgid_plural = '';
            $msgstr       = '';
            $plurals      = [];

            foreach ($parts as $part) {
                $fragments = explode(' ', $part, 2);
                $keyword   = $fragments[0];
                $text      = substr($fragments[1], 1, -1);
                $text      = strtr($text, self::PO_ESCAPE_CHARACTERS);
                switch ($keyword) {
                    case 'msgctxt':
                        $msgctxt = $text;
                        break;
                    case 'msgid':
                        $msgid = $text;
                        break;
                    case 'msgid_plural':
                        $msgid_plural = $text;
                        break;
                    case 'msgstr':
                        $msgstr = $text;
                        break;
                    default:
                        if (preg_match('/^msgstr\[(\d+)]/', $keyword, $match)) {
                            $plurals[$match[1]] = $text;
                        }
                }
            }

            if ($msgctxt !== '') {
                $msgid = $msgctxt . self::CONTEXT_SEPARATOR . $msgid;
            }

            if ($msgid_plural !== '') {
                $msgid .= self::PLURAL_SEPARATOR . $msgid_plural;
                ksort($plurals);
                $msgstr = implode(self::PLURAL_SEPARATOR, $plurals);
            }

            if ($msgid !== '' && trim($msgstr, self::PLURAL_SEPARATOR) !== '') {
                $translations[$msgid] = $msgstr;
            }
        }

        return $translations;
    }
}
