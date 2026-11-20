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

namespace Fisharebest\Webtrees\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use TypeError;

use function e;

#[CoversFunction('e')]
class HtmlEscapeTest extends TestCase
{
    public function testEscapesHtmlContentAndAttributeValues(): void
    {
        self::assertSame('&lt;a title=&quot;x&quot;&gt;&amp;&#039;&lt;/a&gt;', e('<a title="x">&\'</a>'));
        self::assertSame('0', e(0));
        self::assertSame('', e(null));
        self::assertSame('text', e(new class () {
            public function __toString(): string
            {
                return 'text';
            }
        }));
    }

    public function testDoubleEncodingCanBeDisabled(): void
    {
        self::assertSame('&amp;lt;', e('&lt;'));
        self::assertSame('&lt;', e('&lt;', false));
    }

    public function testInvalidValuesAreRejected(): void
    {
        $this->expectException(TypeError::class);
        (new ReflectionFunction('e'))->invoke(['unexpected']);
    }
}
