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
use Fisharebest\Webtrees\Functions\FunctionsMedia;

/**
 * Unit tests for the global functions in the file includes/functions/functions_mediadb.php
 */
class FunctionsMediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the function return_bytes().
     */
    public function testFunctionReturnBytes()
    {
        $this->assertSame(-1, FunctionsMedia::sizeToBytes(''));
        $this->assertSame(-1, FunctionsMedia::sizeToBytes('-1'));
        $this->assertSame(42, FunctionsMedia::sizeToBytes('42'));
        $this->assertSame(42, FunctionsMedia::sizeToBytes('42b'));
        $this->assertSame(42, FunctionsMedia::sizeToBytes('42B'));
        $this->assertSame(43008, FunctionsMedia::sizeToBytes('42k'));
        $this->assertSame(43008, FunctionsMedia::sizeToBytes('42K'));
        $this->assertSame(44040192, FunctionsMedia::sizeToBytes('42m'));
        $this->assertSame(44040192, FunctionsMedia::sizeToBytes('42M'));
        $this->assertSame(45097156608, FunctionsMedia::sizeToBytes('42g'));
        $this->assertSame(45097156608, FunctionsMedia::sizeToBytes('42G'));
    }
}
