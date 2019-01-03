<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use function date_default_timezone_get;
use function date_default_timezone_set;
use function error_reporting;
use ErrorException;
use function set_error_handler;

/**
 * Test the Webtrees class
 */
class WebtreesTest extends \Fisharebest\Webtrees\TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Webtrees::init
     * @return void
     */
    public function testInit(): void
    {
        date_default_timezone_set('Europe/London');
        error_reporting(0);
        set_error_handler(null);

        Webtrees::init();

        // webtrees always runs in UTC (and converts to local time on demand).
        $this->assertSame('UTC', date_default_timezone_get());

        // webtrees sets the error reporting level.
        $this->assertNotSame(0, error_reporting());
        $this->assertSame(Webtrees::ERROR_REPORTING, error_reporting());

        try {
            // Trigger an error
            fopen(__DIR__ . '/no-such-file', 'r');
        } catch (ErrorException $ex) {
            $this->assertSame(__FILE__, $ex->getFile());
        }

        // Disable error reporting (we could use "@"), and don't raise an exception.
        error_reporting(0);
        fopen(__DIR__ . '/no-such-file', 'r');
    }
}
