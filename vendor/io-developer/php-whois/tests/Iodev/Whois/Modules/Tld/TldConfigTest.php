<?php

namespace Iodev\Whois\Modules\Tld;

use Iodev\Whois\Config;

class TldConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testUnnecessarySubzones()
    {
        $data = Config::load('module.tld.servers');

        $rootDict = [];
        foreach ($data as $row) {
            $zone = $row['zone'];
            if (preg_match('~^\.\w+$~', $zone)) {
                $rootDict[$zone][] = $row['host'];
            }
        }

        $found = [];
        foreach ($data as $row) {
            if (preg_match('~^.+?(\.\w+)$~', $row['zone'], $m)) {
                $zone = $m[1];
                if (!empty($rootDict[$zone]) && in_array($row['host'], $rootDict[$zone])) {
                    $found[] = "DUP HOST IN {$row['zone']} ($zone)   {$row['host']}";
                }
            }
        }

        self::assertEmpty($found, implode("\n", $found));
    }
}
