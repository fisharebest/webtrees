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

namespace Fisharebest\Webtrees\Tests\Unit\Schema;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Schema\Migration43;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Migration43::class)]
class Migration43Test extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(Migration43::class));
    }

    public function testUpsertsLanguageSetting(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $tree->setPreference('LANGUAGE', 'fr');
        $migration = new Migration43();

        $migration->upgrade();
        self::assertSame('fr', DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'LANGUAGE')
            ->value('setting_value'));

        $tree->setPreference('LANGUAGE', 'de');
        $migration->upgrade();

        self::assertSame('de', DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'LANGUAGE')
            ->value('setting_value'));
        self::assertSame(1, DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'LANGUAGE')
            ->count());
    }
}
