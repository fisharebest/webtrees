<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use DomainException;
use Fisharebest\Webtrees\Contracts\EncodingFactoryInterface;
use Fisharebest\Webtrees\Encodings\ANSEL;
use Fisharebest\Webtrees\Encodings\ASCII;
use Fisharebest\Webtrees\Encodings\CP437;
use Fisharebest\Webtrees\Encodings\CP850;
use Fisharebest\Webtrees\Encodings\EncodingInterface;
use Fisharebest\Webtrees\Encodings\ISO88591;
use Fisharebest\Webtrees\Encodings\ISO88592;
use Fisharebest\Webtrees\Encodings\MacRoman;
use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF16LE;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Encodings\Windows1250;
use Fisharebest\Webtrees\Encodings\Windows1251;
use Fisharebest\Webtrees\Encodings\Windows1252;
use Fisharebest\Webtrees\Exceptions\InvalidGedcomEncodingException;

use function explode;
use function ltrim;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function strstr;

/**
 * Create an encoding object.
 */
class EncodingFactory implements EncodingFactoryInterface
{
    /**
     * Detect an encoding from a GEDCOM header record.
     *
     * @param string $header
     *
     * @return EncodingInterface|null
     * @throws InvalidGedcomEncodingException
     */
    public function detect(string $header): ?EncodingInterface
    {
        $utf_bom = [
            '/^' . UTF8::BYTE_ORDER_MARK . '/'    => UTF8::NAME,
            '/^' . UTF16BE::BYTE_ORDER_MARK . '/' => UTF16BE::NAME,
            '/^' . UTF16LE::BYTE_ORDER_MARK . '/' => UTF16LE::NAME,
        ];

        foreach ($utf_bom as $regex => $encoding) {
            if (preg_match($regex, $header) === 1) {
                return $this->make($encoding);
            }
        }

        $utf16 = [
            "\x000" => UTF16BE::NAME,
            "0\x00" => UTF16LE::NAME,
        ];

        foreach ($utf16 as $start => $encoding) {
            if (str_starts_with($header, $start)) {
                return $this->make($encoding);
            }
        }

        // Standardize whitespace to simplify matching.
        $header = strtr(ltrim($header), ["\r\n" => "\n", "\n\r" => "\n", "\r" => "\n"]);

        while (str_contains($header, "\n ") || str_contains($header, " \n") || str_contains($header, '  ')) {
            $header = strtr($header, ["\n " => "\n", " \n" => "\n", '  ' => ' ']);
        }

        // We need a complete header record
        $header = strstr($header, "\n0", true);

        if ($header === false) {
            return null;
        }

        // Some of these come from Tamura Jones, the rest from webtrees users.
        $character_sets = [
            'ASCII'             => ASCII::NAME,
            'ANSEL'             => ANSEL::NAME,
            'UTF-8'             => UTF8::NAME,
            'UNICODE'           => UTF8::NAME, // If the null byte test failed, this can't be UTF16
            'ASCII/MacOS Roman' => MacRoman::NAME, // GEDitCOM
            'ASCII/MACINTOSH'   => MacRoman::NAME, // MacFamilyTree < 8.3.5
            'MACINTOSH'         => MacRoman::NAME, // MacFamilyTree >= 8.3.5
            'CP437'             => CP437::NAME,
            'IBMPC'             => CP437::NAME,
            'IBM'               => CP437::NAME, // Reunion
            'IBM-PC'            => CP437::NAME, // CumberlandFamilyTree
            'OEM'               => CP437::NAME, // Généatique
            'CP850'             => CP850::NAME,
            'MSDOS'             => CP850::NAME,
            'IBM-DOS'           => CP850::NAME, // Reunion, EasyTree
            'MS-DOS'            => CP850::NAME, // AbrEdit FTM for Windows
            'ANSI'              => CP850::NAME,
            'WINDOWS'           => CP850::NAME, // Parentele
            'IBM WINDOWS'       => CP850::NAME, // EasyTree, Généalogie, Reunion, TribalPages
            'IBM_WINDOWS'       => CP850::NAME, // EasyTree
            'CP1250'            => Windows1250::NAME,
            'windows-1250'      => Windows1250::NAME, // GenoPro, Rodokmen Pro
            'CP1251'            => Windows1251::NAME,
            'WINDOWS-1251'      => Windows1251::NAME, // Rodovid
            'CP1252'            => Windows1252::NAME, // Lifelines
            'ISO-8859-1'        => ISO88591::NAME, // Cumberland Family Tree, Lifelines
            'ISO8859-1'         => ISO88591::NAME, // Scion Genealogist
            'ISO8859'           => ISO88591::NAME, // Genealogica Grafica
            'LATIN-1'           => ISO88591::NAME,
            'LATIN1'            => ISO88591::NAME, // GenealogyJ
            'ISO-8859-2'        => ISO88592::NAME,
            'ISO8859-2'         => ISO88592::NAME,
            'LATIN-2'           => ISO88592::NAME,
            'LATIN2'            => ISO88592::NAME,
        ];

        foreach ($character_sets as $pattern => $encoding) {
            if (str_contains($pattern, '/')) {
                [$char, $vers] = explode('/', $pattern);
                $regex = "\n1 CHAR " . $char . "\n2 VERS " . $vers;
            } else {
                $regex = "\n1 CHAR(?:ACTER)? " . $pattern;
            }

            if (preg_match('/' . $regex . '/i', $header) === 1) {
                return $this->make($encoding);
            }
        }

        if (preg_match('/1 CHAR (.+)/', $header, $match) === 1) {
            throw new InvalidGedcomEncodingException($match[1]);
        }

        return $this->make(UTF8::NAME);
    }

    /**
     * Create a named encoding.
     *
     * @param string $name
     *
     * @return EncodingInterface
     * @throws DomainException
     */
    public function make(string $name): EncodingInterface
    {
        switch ($name) {
            case UTF8::NAME:
                return new UTF8();

            case UTF16BE::NAME:
                return new UTF16BE();

            case UTF16LE::NAME:
                return new UTF16LE();

            case ANSEL::NAME:
                return new ANSEL();

            case ASCII::NAME:
                return new ASCII();

            case CP437::NAME:
                return new CP437();

            case CP850::NAME:
                return new CP850();

            case Windows1250::NAME:
                return new Windows1250();

            case Windows1251::NAME:
                return new Windows1251();

            case Windows1252::NAME:
                return new Windows1252();

            case MacRoman::NAME:
                return new MacRoman();

            case ISO88591::NAME:
                return new ISO88591();

            case ISO88592::NAME:
                return new ISO88592();

            default:
                throw new DomainException('Invalid encoding: ' . $name);
        }
    }

    /**
     * A list of supported encodings and their names.
     *
     * @return array<string,string>
     */
    public function list(): array
    {
        return [
            UTF8::NAME        => 'UTF-8',
            UTF16BE::NAME     => 'UTF-16BE',
            UTF16LE::NAME     => 'UTF-16LE',
            ANSEL::NAME       => 'ANSEL',
            ASCII::NAME       => 'ASCII',
            ISO88591::NAME    => 'ISO-8859-1',
            ISO88592::NAME    => 'ISO-8859-2',
            Windows1250::NAME => 'Windows 1250',
            Windows1251::NAME => 'Windows 1251',
            Windows1252::NAME => 'Windows 1252',
            CP437::NAME       => 'CP437',
            CP850::NAME       => 'CP850',
            MacRoman::NAME    => 'MacOS Roman',
        ];
    }
}
