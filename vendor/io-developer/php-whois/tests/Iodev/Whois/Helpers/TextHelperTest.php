<?php

namespace Iodev\Whois\Helpers;

use InvalidArgumentException;

class TextHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $filename
     * @return bool|string
     */
    private static function loadContent($filename)
    {
        $file = __DIR__ . '/text_data/' . $filename;
        if (!file_exists($file)) {
            throw new InvalidArgumentException("File '$file' not found");
        }
        return file_get_contents($file);
    }

    public function assertToUtf8($inputFile, $outputFile)
    {
        $input = self::loadContent($inputFile);
        $output = preg_replace('~\r\n|\r|\n~ui', '\n', self::loadContent($outputFile));
        $utf8 = preg_replace('~\r\n|\r|\n~ui', '\n', TextHelper::toUtf8($input));
        self::assertEquals($output, $utf8);
    }

    public function test_toUtf8_FIN()
    {
        self::assertToUtf8('encoding.fin.in.txt', 'encoding.fin.out.txt');
    }

    public function test_toUtf8_UKR()
    {
        self::assertToUtf8('encoding.ukr.in.txt', 'encoding.ukr.out.txt');
    }
}
