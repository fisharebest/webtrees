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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\BranchesListModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

use function array_map;

#[CoversClass(BranchesListModule::class)]
class BranchesListModuleTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(BranchesListModule::class));
    }

    public function testLoadsDistinctMatchingSurnamesAndSoundexMatches(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        Auth::login($user);

        $tree  = (new TreeService(new GedcomImportService()))->create('name', 'title');
        $smith = $tree->createIndividual("0 @@ INDI\n1 NAME Alice /Smith/\n1 NAME Alice /Smith/");
        $smyth = $tree->createIndividual("0 @@ INDI\n1 NAME Bob /Smyth/");
        $tree->createIndividual("0 @@ INDI\n1 NAME Carol /Jones/");

        $module = new BranchesListModule(new ModuleService());
        $method = new ReflectionMethod($module, 'loadIndividuals');

        $exact = $method->invoke($module, $tree, 'Smith', false, false);
        self::assertIsArray($exact);
        self::assertSame([$smith->xref()], array_map(static fn (Individual $individual): string => $individual->xref(), $exact));

        $phonetic = $method->invoke($module, $tree, 'Smith', false, true);
        self::assertIsArray($phonetic);
        self::assertEqualsCanonicalizing(
            [$smith->xref(), $smyth->xref()],
            array_map(static fn (Individual $individual): string => $individual->xref(), $phonetic)
        );

        $daitch_mokotoff = $method->invoke($module, $tree, 'Smith', true, false);
        self::assertIsArray($daitch_mokotoff);
        self::assertEqualsCanonicalizing(
            [$smith->xref(), $smyth->xref()],
            array_map(static fn (Individual $individual): string => $individual->xref(), $daitch_mokotoff)
        );
    }
}
