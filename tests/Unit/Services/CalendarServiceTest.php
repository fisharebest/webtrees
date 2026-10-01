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
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_map;
use function range;

#[CoversClass(CalendarService::class)]
class CalendarServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(CalendarService::class));
    }

    public function testAnniversaryFiltersPreserveFactTypesSexAndTree(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('test', 'Test', 'test@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('first', 'First');
        $other_tree     = $tree_service->create('second', 'Second');

        $import_service->importRecord("0 @I1@ INDI\n1 NAME Alice /One/\n1 SEX F\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Bob /Two/\n1 SEX M\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I3@ INDI\n1 NAME Carol /Three/\n1 SEX F\n1 BIRT\n2 DATE 2 JAN 1900", $tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @I2@\n1 WIFE @I1@\n1 MARR\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I4@ INDI\n1 BIRT\n2 DATE 1 JAN 1900", $other_tree, true);

        $day     = (new GregorianDate(['2020', 'JAN', '1']))->minimumJulianDay();
        $service = new CalendarService();

        self::assertSame(['I1', 'I2', 'X1'], array_map(static fn ($fact): string => $fact->record()->xref(), $service->getAnniversaryEvents($day, 'BIRT', $tree)));
        self::assertSame(['I1'], array_map(static fn ($fact): string => $fact->record()->xref(), $service->getAnniversaryEvents($day, 'BIRT', $tree, '', 'F')));
        self::assertSame(['F1'], array_map(static fn ($fact): string => $fact->record()->xref(), $service->getAnniversaryEvents($day, 'MARR', $tree)));
    }

    public function testHebrewAnniversaryUsesTheMatchingCalendar(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('test', 'Test', 'test@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('first', 'First');
        $import_service->importRecord("0 @I1@ INDI\n1 BIRT\n2 DATE @#DHEBREW@ 15 KSL 5750", $tree, true);

        $day   = (new JewishDate(['5784', 'KSL', '15']))->minimumJulianDay();
        $facts = (new CalendarService())->getAnniversaryEvents($day, 'BIRT', $tree);

        self::assertSame(['I1'], array_map(static fn ($fact): string => $fact->record()->xref(), $facts));
    }

    public function testFirstKislevIncludesLastDayOfShortCheshvan(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('test', 'Test', 'test@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('first', 'First');

        $target_year = 0;
        $record_year = 0;
        foreach (range(5700, 5790) as $year) {
            if ((new JewishDate([(string) $year, 'CSH', '1']))->daysInMonth() === 29 && $year >= 5780) {
                $target_year = $year;
            } elseif ((new JewishDate([(string) $year, 'CSH', '1']))->daysInMonth() === 30 && $record_year === 0) {
                $record_year = $year;
            }
        }

        self::assertNotSame(0, $target_year);
        self::assertNotSame(0, $record_year);

        $import_service->importRecord("0 @I1@ INDI\n1 BIRT\n2 DATE @#DHEBREW@ 30 CSH " . $record_year, $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 BIRT\n2 DATE @#DHEBREW@ 1 KSL " . $record_year, $tree, true);

        $day   = (new JewishDate([(string) $target_year, 'KSL', '1']))->minimumJulianDay();
        $facts = (new CalendarService())->getAnniversaryEvents($day, 'BIRT', $tree);

        self::assertSame(['I2', 'I1'], array_map(static fn ($fact): string => $fact->record()->xref(), $facts));
    }
}
