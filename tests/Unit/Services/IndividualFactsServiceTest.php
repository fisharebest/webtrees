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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\IndividualFactsService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_map;

#[CoversClass(IndividualFactsService::class)]
class IndividualFactsServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(IndividualFactsService::class));
    }

    public function testRelativeFactsDeduplicatesRepeatedSpouseEventsWithoutMergingDistinctFacts(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree = (new TreeService($import_service))->create('first', 'First');
        $tree->setPreference('SHOW_RELATIVES_EVENTS', '_DEAT_SPOU');

        $import_service->importRecord("0 @I1@ INDI\n1 SEX M\n1 BIRT\n2 DATE 1 JAN 1900\n1 DEAT\n2 DATE 1 JAN 2000\n1 FAMS @F1@\n1 FAMS @F2@\n1 FAMS @F3@", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 SEX F\n1 DEAT\n2 DATE 1 JAN 1970\n1 BURI\n2 DATE 2 JAN 1970", $tree, true);
        $import_service->importRecord("0 @I3@ INDI\n1 SEX F\n1 DEAT\n2 DATE 1 JAN 1970", $tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @I1@\n1 WIFE @I2@", $tree, true);
        $import_service->importRecord("0 @F2@ FAM\n1 HUSB @I1@\n1 WIFE @I2@", $tree, true);
        $import_service->importRecord("0 @F3@ FAM\n1 HUSB @I1@\n1 WIFE @I3@", $tree, true);

        $individual = Registry::individualFactory()->make('I1', $tree);
        self::assertInstanceOf(Individual::class, $individual);

        $facts = (new IndividualFactsService(new LinkedRecordService(), new ModuleService()))->relativeFacts($individual);

        self::assertCount(3, $facts);
        self::assertSame(['I2', 'I2', 'I3'], array_map(
            static fn (Fact $fact): string => $fact->record()->xref(),
            $facts->values()->array(),
        ));
    }
}
