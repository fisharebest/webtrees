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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Module\AbstractIndividualListModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Module\IndividualListModule;

use function array_sum;

#[CoversClass(IndividualListModule::class)]
class IndividualListModuleTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(IndividualListModule::class));
    }

    public function testSelectsNamesWithOptionalSurnameFilter(): void
    {
        self::createDatabase();
        $user = (new UserService(new SystemClock()))->create('test', 'Test', 'test@example.com', 'secret');
        Auth::login($user);
        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');

        $module = new class extends IndividualListModule {
            /**
             * @param array<string> $surnames
             * @return Collection<int,\Fisharebest\Webtrees\Individual>
             */
            public function selectIndividuals(Tree $tree, array $surnames): Collection
            {
                return $this->individuals($tree, $surnames, '', false, false);
            }
        };

        self::assertCount(1, $module->selectIndividuals($tree, ['DOE']));
        self::assertCount(0, $module->selectIndividuals($tree, ['MISSING']));
        self::assertCount(1, $module->selectIndividuals($tree, []));
        self::assertSame(1, array_sum($module->givenNameInitials($tree, ['DOE'], false, false)));
        self::assertSame(0, array_sum($module->givenNameInitials($tree, ['MISSING'], false, false)));
    }
}
