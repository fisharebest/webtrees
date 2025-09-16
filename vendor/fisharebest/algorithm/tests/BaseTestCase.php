<?php

namespace Fisharebest\Tests\Algorithm;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_TestCase;

/**
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach <greg@subaqua.co.uk>
 * @license   GPL-3.0+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses>.
 */

if (class_exists('PHPUnit_Framework_TestCase')) {
    class BaseTestCase extends PHPUnit_Framework_TestCase
    {
    }
} else {
    class BaseTestCase extends TestCase
    {
    }
}
