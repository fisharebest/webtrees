<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AbstractElement::class)]
#[CoversClass(RestrictionNotice::class)]
class RestrictionNoticeTest extends AbstractElementTestCase
{
    /**
     * Standard tests for all elements.
     */
    public static function setupBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$element = new RestrictionNotice('label');
    }

    public function testCanonical(): void
    {
        self::assertSame('PRIVACY', self::$element->canonical('pRiVacy'));
        self::assertSame('NONE', self::$element->canonical('NONE'));
        self::assertSame('CONFIDENTIAL', self::$element->canonical('Confidential'));
        self::assertSame('LOCKED', self::$element->canonical(', locked ,'));
        self::assertSame('CONFIDENTIAL, LOCKED', self::$element->canonical('locked confidential'));
        self::assertSame('PRIVACY, LOCKED', self::$element->canonical('locked, privacy'));
        self::assertSame('NONE, LOCKED', self::$element->canonical('locked,, none'));
    }
}
