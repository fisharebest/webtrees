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

final class Translation
{
    public const string PLURAL_SEPARATOR       = "\x00";
    public const string CONTEXT_SEPARATOR      = "\x04";

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

    /** @var array<string,string> An association of English -> translated messages */
    private array $translations;

    public function __construct(string $filename)
    {
        $this->translations = [];

        switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case 'csv':
                $fp = fopen($filename, 'rb');
                if ($fp !== false) {
                    while (($data = fgetcsv($fp, 0, ';')) !== false) {
                        $this->translations[$data[0]] = $data[1];
                    }
                    fclose($fp);
                }
                break;

            case 'mo':
                $fp = fopen($filename, 'rb');
                if ($fp !== false) {
                    $this->readMoFile($fp);
                    fclose($fp);
                }
                break;

            case 'po':
                $this->readPoFile(file($filename));
                break;

            case 'php':
                $translations = include $filename;
                if (is_array($translations)) {
                    $this->translations = $translations;
                }
                break;
        }
    }

    /**
     * The translation strings
     *
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return $this->translations;
    }

    /**
     * @param resource  $fp
     *
     * @return int[]
     */
    private function readMoData($fp, int $offset, int $count, ByteOrder $byte_order): array
    {
        fseek($fp, $offset);

        return unpack($byte_order->value . $count, fread($fp, $count * 4));
    }

    /**
     * Read and parse a .MO (gettext) file
     *
     * @link https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
     *
     * @param resource $fp
     */
    private function readMoFile($fp): void
    {
        $magic = $this->readMoData($fp, 0, 1, ByteOrder::LittleEndian);

        $byte_order = ByteOrder::fromMoMagicString(dechex($magic[1]));

        // Read the lookup tables
        [, $number_of_strings, $offset_original, $offset_translated] = $this->readMoData($fp, 8, 3, $byte_order);
        $lookup_original   = $this->readMoData($fp, $offset_original, $number_of_strings * 2, $byte_order);
        $lookup_translated = $this->readMoData($fp, $offset_translated, $number_of_strings * 2, $byte_order);

        // Read the strings
        for ($n = 1; $n < $number_of_strings; ++$n) {
            fseek($fp, $lookup_original[$n * 2 + 2]);
            $original = fread($fp, $lookup_original[$n * 2 + 1]);
            fseek($fp, $lookup_translated[$n * 2 + 2]);
            $translated                    = fread($fp, $lookup_translated[$n * 2 + 1]);
            $this->translations[$original] = $translated;
        }
    }

    /**
     * @link https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
     *
     * @param array<string> $lines
     */
    private function readPoFile(array $lines): void
    {
        // Strip comments
        $lines = array_filter($lines, fn(string $line): bool => !str_starts_with((string) $line, '#'));

        // Trim carriage-returns, newlines, spaces
        $lines = array_map(trim(...), $lines);

        // Merge continuation lines
        $tmp = trim(implode("\n", $lines));
        $tmp = str_replace("\"\n\"", '', $tmp);

        // Split into separate translations
        $translations = preg_split("/\n{2,}/", $tmp);

        foreach ($translations as $translation) {
            $parts = explode("\n", $translation);

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
                $this->translations[$msgid] = $msgstr;
            }
        }
    }
}
