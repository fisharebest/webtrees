<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Report\Footnote;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Element::class)]
#[CoversClass(Footnote::class)]
class FootnoteTest extends TestCase
{
    use ElementTestTrait;

    public function testStoresNumberAndText(): void
    {
        $footnote = new Footnote($this->makeStyle('footnote', '', 8.0));
        $footnote->addText('Source text');
        $footnote->setNumber(1);

        self::assertSame('Source text', $footnote->getValue());
        self::assertSame(1, $footnote->number);
    }
}
