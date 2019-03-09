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

/**
 * Test harness for the class UserFavoritesModule
 */
class UserFavoritesModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare the environment for these tests
     */
    public function setUp()
    {
    }

    /**
     * Test that the class exists
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists('\Fisharebest\Webtrees\Module\UserFavoritesModule'));
    }
}
