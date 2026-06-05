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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InputDefinition::class)]
class InputDefinitionTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $input = new InputDefinition(
            name:    'pid',
            type:    'text',
            lookup:  'INDI',
            options: 'Y;N',
            default: 'Y',
        );

        self::assertSame('pid', $input->name);
        self::assertSame('text', $input->type);
        self::assertSame('INDI', $input->lookup);
        self::assertSame('Y;N', $input->options);
        self::assertSame('Y', $input->default);
        self::assertSame('', $input->value);
        self::assertSame('', $input->extra);
        self::assertSame('', $input->control);
    }

    public function testWithValueReturnsNewInstance(): void
    {
        $input = new InputDefinition(
            name:    'pid',
            type:    'text',
            lookup:  'INDI',
            options: '',
            default: '',
        );

        $updated = $input->withValue('I123');

        self::assertSame('I123', $updated->value);
        self::assertSame('', $input->value);
        self::assertSame('pid', $updated->name);
    }

    public function testWithControlReturnsNewInstance(): void
    {
        $input = new InputDefinition(
            name:    'pid',
            type:    'text',
            lookup:  'INDI',
            options: '',
            default: '',
        );

        $updated = $input->withControl('<input>', 'extra html');

        self::assertSame('<input>', $updated->control);
        self::assertSame('extra html', $updated->extra);
        self::assertSame('', $input->control);
    }

    public function testWithControlPreservesExistingExtraWhenNotProvided(): void
    {
        $input = new InputDefinition(
            name:    'pid',
            type:    'text',
            lookup:  'INDI',
            options: '',
            default: '',
            extra:   'original',
        );

        $updated = $input->withControl('<select>');

        self::assertSame('original', $updated->extra);
    }
}
