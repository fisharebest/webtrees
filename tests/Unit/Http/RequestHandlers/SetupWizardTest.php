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

namespace Fisharebest\Webtrees\Tests\Unit\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\RequestHandlers\SetupWizard;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

use function view;

#[CoversClass(SetupWizard::class)]
class SetupWizardTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(SetupWizard::class));
    }

    public function testFirebirdConnectionView(): void
    {
        $html = view('setup/step-4-database-firebird', [
            'dbca'     => '',
            'dbcert'   => '',
            'dbhost'   => 'localhost',
            'dbkey'    => '',
            'dbname'   => '/var/db/family & friends.fdb',
            'dbpass'   => 'secret',
            'dbport'   => '3050',
            'dbtype'   => 'firebird',
            'dbuser'   => 'SYSDBA',
            'dbverify' => '',
            'errors'   => new Collection(),
            'lang'     => 'en-US',
            'tblpfx'   => 'wt_',
            'warnings' => new Collection(),
            'wtemail'  => '',
            'wtname'   => '',
            'wtpass'   => '',
            'wtuser'   => '',
        ]);

        self::assertStringContainsString('Database connection', $html);
        self::assertStringContainsString('Firebird', $html);
        self::assertStringContainsString('name="dbtype" type="hidden" value="firebird"', $html);
        self::assertStringContainsString('name="dbport" pattern="\d+" type="text" value="3050"', $html);
        self::assertStringContainsString('name="dbname" type="text" value="/var/db/family &amp; friends.fdb"', $html);
        self::assertStringContainsString('name="step" type="submit" value="5"', $html);
        self::assertStringContainsString('name="step" type="submit" value="3"', $html);
    }
}
