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
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Module\LifespansChartModule;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LifespansChartModule::class)]
class LifespansChartModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(LifespansChartModule::class));
    }

    public function testFindsIndividualsByDateAndPlace(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        Auth::login($user);

        $tree       = (new TreeService(new GedcomImportService()))->create('name', 'title');
        $individual = $tree->createIndividual("0 @@ INDI\n1 NAME Alice /Smith/\n1 BIRT\n2 DATE 01 JAN 1901\n2 PLAC London\n1 DEAT\n2 DATE 01 JAN 1905\n2 PLAC London");
        $other = $tree->createIndividual("0 @@ INDI\n1 NAME Bob /Jones/\n1 BIRT\n2 DATE 01 JAN 1950\n2 PLAC Paris\n1 BAPL\n2 DATE 01 JAN 1903");

        $module = new class () extends LifespansChartModule {
            /** @return array<string> */
            public function individualsByDate(Date $start, Date $end, Tree $tree): array
            {
                return $this->findIndividualsByDate($start, $end, $tree);
            }

            /** @return array<string> */
            public function individualsByPlace(Place $place, Tree $tree): array
            {
                return $this->findIndividualsByPlace($place, $tree);
            }
        };

        self::assertSame(
            [$individual->xref(), $individual->xref()],
            $module->individualsByDate(new Date('01 JAN 1900'), new Date('31 DEC 1910'), $tree)
        );
        self::assertSame(
            [$individual->xref()],
            $module->individualsByPlace(new Place('London', $tree), $tree)
        );
        self::assertSame([$other->xref()], $module->individualsByPlace(new Place('Paris', $tree), $tree));
        self::assertSame([], $module->individualsByPlace(new Place('Madrid', $tree), $tree));
    }
}
