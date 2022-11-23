<?php

namespace Fisharebest\Localization;

use InvalidArgumentException;

/**
 * Class Translation - a set of translated messages, such as a .MO file.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class Translation
{
    // Constants for processing .MO files
    const MO_MAGIC_LITTLE_ENDIAN = '950412de';
    const MO_MAGIC_BIG_ENDIAN    = 'de120495';
    const PACK_LITTLE_ENDIAN     = 'V';
    const PACK_BIG_ENDIAN        = 'N';
    const PLURAL_SEPARATOR       = "\x00";
    const CONTEXT_SEPARATOR      = "\x04";

    /** @var array<array-key,string> An association of English -> translated messages */
    private $translations;

    /**
     * The code for this variant.
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->translations = array();

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
     * @return array<array-key,string>
     */
    public function asArray()
    {
        return $this->translations;
    }

    /**
     * Read specific binary data (32 bit words) from a .MO file
     *
     * @param resource $fp
     * @param int      $offset
     * @param int      $count
     * @param string   $pack "N" for big-endian, "V" for little-endian
     *
     * @return int[]
     */
    private function readMoWords($fp, $offset, $count, $pack)
    {
        fseek($fp, $offset);

        return unpack($pack . $count, fread($fp, $count * 4));
    }

    /**
     * Read and parse a .MO (gettext) file
     *
     * @link https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
     *
     * @param resource $fp
     *
     * @return void
     */
    private function readMoFile($fp)
    {
        // How is the numeric data packed in the .MO file?
        $magic = $this->readMoWords($fp, 0, 1, self::PACK_LITTLE_ENDIAN);

        switch (dechex($magic[1])) {
            case self::MO_MAGIC_LITTLE_ENDIAN:
                $pack = self::PACK_LITTLE_ENDIAN;
                break;
            case self::MO_MAGIC_BIG_ENDIAN:
                $pack = self::PACK_BIG_ENDIAN;
                break;
            default:
                // Not a valid .MO file.
                throw new InvalidArgumentException('Invalid .MO file');
        }

        // Read the lookup tables
        list(, $number_of_strings, $offset_original, $offset_translated) = $this->readMoWords($fp, 8, 3, $pack);
        $lookup_original   = $this->readMoWords($fp, $offset_original, $number_of_strings * 2, $pack);
        $lookup_translated = $this->readMoWords($fp, $offset_translated, $number_of_strings * 2, $pack);

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
     * Read and parse a .PO (gettext) file
     *
     * @link https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
     *
     * @param string[] $lines
     *
     * @return void
     */
    private function readPoFile($lines)
    {
        // Strip comments
        $lines = array_filter($lines, function ($line) {
            return strpos($line, '#') !== 0;
        });

        // Trim carriage-returns, newlines, spaces
        $lines = array_map('trim', $lines);

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
            $plurals      = array();

            foreach ($parts as $part) {
                $fragments = explode(' ', $part, 2);
                $keyword   = $fragments[0];
                $text      = substr($fragments[1], 1, -1);
                $text      = $this->unescapePoText($text);
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
                        if (preg_match('/^msgstr\[(\d+)\]/', $keyword, $match)) {
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

    /**
     * @param string $text
     *
     * @return string
     */
    private function unescapePoText($text)
    {
        return strtr($text, array(
            '\\\\' => '\\',
            '\\a'  => "\x07",
            '\\b'  => "\x08",
            '\\f'  => "\x0c",
            '\\n'  => "\n",
            '\\r'  => "\r",
            '\\t'  => "\t",
            '\\v'  => "\x0b",
            '\\"'  => '"',
        ));
    }
}
