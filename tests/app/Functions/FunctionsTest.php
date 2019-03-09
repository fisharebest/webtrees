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
use Fisharebest\Webtrees\Functions\Functions;

/**
 * Unit tests for the global functions in the file includes/functions/functions.php
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests for function isFileExternal()
     */
    public function testFunctionIsFileExternal()
    {
        $this->assertEquals(Functions::isFileExternal('http://www.example.com/file.txt'), true);
        $this->assertEquals(Functions::isFileExternal('file.txt'), false);
        $this->assertEquals(Functions::isFileExternal('folder/file.txt'), false);
        $this->assertEquals(Functions::isFileExternal('folder\\file.txt'), false);
        $this->assertEquals(Functions::isFileExternal('/folder/file.txt'), false);
        $this->assertEquals(Functions::isFileExternal('\\folder\\file.txt'), false);
        $this->assertEquals(Functions::isFileExternal('C:\\folder\\file.txt'), false);
    }
}
