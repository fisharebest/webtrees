<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Encodings\ANSEL;
use Fisharebest\Webtrees\Encodings\ASCII;
use Fisharebest\Webtrees\Encodings\CP437;
use Fisharebest\Webtrees\Encodings\CP850;
use Fisharebest\Webtrees\Encodings\EncodingInterface;
use Fisharebest\Webtrees\Encodings\MacRoman;
use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF16LE;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Encodings\Windows1250;
use Fisharebest\Webtrees\Encodings\Windows1251;
use Fisharebest\Webtrees\Encodings\Windows1252;
use Fisharebest\Webtrees\TestCase;

use function count;

/**
 * Test harness for the class EncodingFactory
 *
 * @covers \Fisharebest\Webtrees\Factories\EncodingFactory
 */
class EncodingFactoryTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Factories\EncodingFactory::detect
     */
    public function testDetectUsingByteOrderMark(): void
    {
        $factory = new EncodingFactory();

        $this->assertInstanceOf(
            UTF8::class,
            $factory->detect(UTF8::BYTE_ORDER_MARK)
        );

        $this->assertInstanceOf(
            UTF16BE::class,
            $factory->detect(UTF16BE::BYTE_ORDER_MARK)
        );

        $this->assertInstanceOf(
            UTF16LE::class,
            $factory->detect(UTF16LE::BYTE_ORDER_MARK)
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\EncodingFactory::detect
     */
    public function testDetectUtf16UsingNullBytes(): void
    {
        $factory = new EncodingFactory();

        $this->assertInstanceOf(
            UTF16BE::class,
            $factory->detect("\x000")
        );

        $this->assertInstanceOf(
            UTF16LE::class,
            $factory->detect("0\x00")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\EncodingFactory::detect
     */
    public function testDetectByCharAndVers(): void
    {
        $factory = new EncodingFactory();

        $this->assertInstanceOf(
            MacRoman::class,
            $factory->detect("0 HEAD\n1 CHAR MACINTOSH\n0 TRLR")
        );
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\EncodingFactory::make
     */
    public function testMake(): void
    {
        $factory = new EncodingFactory();

        $this->assertInstanceOf(UTF8::class, $factory->make(UTF8::NAME));
        $this->assertInstanceOf(UTF16BE::class, $factory->make(UTF16BE::NAME));
        $this->assertInstanceOf(UTF16LE::class, $factory->make(UTF16LE::NAME));
        $this->assertInstanceOf(ANSEL::class, $factory->make(ANSEL::NAME));
        $this->assertInstanceOf(ASCII::class, $factory->make(ASCII::NAME));
        $this->assertInstanceOf(CP437::class, $factory->make(CP437::NAME));
        $this->assertInstanceOf(CP850::class, $factory->make(CP850::NAME));
        $this->assertInstanceOf(Windows1250::class, $factory->make(Windows1250::NAME));
        $this->assertInstanceOf(Windows1251::class, $factory->make(Windows1251::NAME));
        $this->assertInstanceOf(Windows1252::class, $factory->make(Windows1252::NAME));
        $this->assertInstanceOf(MacRoman::class, $factory->make(MacRoman::NAME));

        $this->expectException(DomainException::class);
        $factory->make('Not the name of a valid encoding');
    }

    /**
     * @covers \Fisharebest\Webtrees\Factories\EncodingFactory::list
     */
    public function testList(): void
    {
        $factory = new EncodingFactory();

        $encodings = $factory->list();

        $this->assertCount(13, $encodings);

        foreach ($encodings as $key => $value) {
            $this->assertIsString($key);
            $this->assertIsString($value);
            $this->assertInstanceOf(EncodingInterface::class, $factory->make($key));
        }
    }
}
