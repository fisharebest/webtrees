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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\SurnameTraditionFactoryInterface;
use Fisharebest\Webtrees\Factories\SurnameTraditionFactory;
use Fisharebest\Webtrees\SurnameTradition\DefaultSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\IcelandicSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\MatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition;

/**
 * @covers \Fisharebest\Webtrees\Factories\SurnameTraditionFactory
 */
class SurnameTraditionFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new SurnameTraditionFactory();

        self::assertInstanceOf(DefaultSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::DEFAULT));
        self::assertInstanceOf(IcelandicSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::ICELANDIC));
        self::assertInstanceOf(LithuanianSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::LITHUANIAN));
        self::assertInstanceOf(MatrilinealSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::MATRILINEAL));
        self::assertInstanceOf(PaternalSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::PATERNAL));
        self::assertInstanceOf(PatrilinealSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::PATRILINEAL));
        self::assertInstanceOf(PolishSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::POLISH));
        self::assertInstanceOf(PortugueseSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::PORTUGUESE));
        self::assertInstanceOf(SpanishSurnameTradition::class, $factory->make(SurnameTraditionFactoryInterface::SPANISH));
    }

    public function testCreateInvalid(): void
    {
        $factory = new SurnameTraditionFactory();

        self::assertInstanceOf(DefaultSurnameTradition::class, $factory->make('FOOBAR'));
    }

    public function testAllDescriptions(): void
    {
        $descriptions = Registry::surnameTraditionFactory()->list();
        self::assertCount(9, $descriptions);
    }
}
