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
use Fisharebest\Webtrees\Report\TextBox;
use Fisharebest\Webtrees\Report\NullElement;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Element::class)]
#[CoversClass(TextBox::class)]
class TextBoxTest extends TestCase
{
    public function testCanBeConstructedAndAcceptNestedElements(): void
    {
        $text_box = new TextBox(100.0, 0.0, true, '', true, 0.0, 0.0, false, true, false);
        $text_box->addElement(new NullElement());

        self::assertInstanceOf(TextBox::class, $text_box);
    }
}
