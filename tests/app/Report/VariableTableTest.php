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

use DomainException;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(VariableTable::class)]
class VariableTableTest extends TestCase
{
    public function testHasReturnsTrueForExistingKey(): void
    {
        $table = new VariableTable(['name' => 'John']);

        self::assertTrue($table->has('name'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $table = new VariableTable([]);

        self::assertFalse($table->has('name'));
    }

    public function testGetReturnsValue(): void
    {
        $table = new VariableTable(['color' => 'blue']);

        self::assertSame('blue', $table->get('color'));
    }

    public function testGetThrowsForMissingKey(): void
    {
        $table = new VariableTable([]);

        $this->expectException(DomainException::class);
        $table->get('missing');
    }

    public function testSetCreatesNewVariable(): void
    {
        $table = new VariableTable([]);

        $table->set('name', 'Jane');

        self::assertTrue($table->has('name'));
        self::assertSame('Jane', $table->get('name'));
    }

    public function testSetOverwritesExistingVariable(): void
    {
        $table = new VariableTable(['name' => 'John']);

        $table->set('name', 'Jane');

        self::assertSame('Jane', $table->get('name'));
    }
}
