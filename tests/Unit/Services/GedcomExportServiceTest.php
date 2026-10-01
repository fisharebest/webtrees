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
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;

use function fclose;
use function stream_get_contents;

#[CoversClass(GedcomExportService::class)]
class GedcomExportServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomExportService::class));
    }

    public function testExportsAllRecordTypesInXrefOrderWithAndWithoutPrivacyFiltering(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('first', 'First');

        foreach (['2', '10'] as $suffix) {
            $import_service->importRecord("0 @I" . $suffix . "@ INDI\n1 NAME Individual /Test/", $tree, true);
            $import_service->importRecord("0 @F" . $suffix . "@ FAM\n1 MARR\n2 DATE 1 JAN 1900", $tree, true);
            $import_service->importRecord("0 @S" . $suffix . "@ SOUR\n1 TITL Source", $tree, true);
            $import_service->importRecord("0 @M" . $suffix . "@ OBJE\n1 FILE example.jpg\n2 FORM jpg", $tree, true);
            $import_service->importRecord('0 @N' . $suffix . '@ NOTE Example', $tree, true);
        }

        $factory = new Psr17Factory();
        $service = new GedcomExportService($factory, $factory);

        foreach ([AccessLevel::Hidden, AccessLevel::Manager] as $access_level) {
            $stream = $service->export($tree, true, access_level: $access_level, line_endings: 'LF');
            $gedcom = stream_get_contents($stream);
            fclose($stream);

            self::assertIsString($gedcom);
            foreach (['INDI' => 'I', 'FAM' => 'F', 'SOUR' => 'S', 'OBJE' => 'M', 'NOTE' => 'N'] as $tag => $prefix) {
                self::assertMatchesRegularExpression('/0 @' . $prefix . '2@ ' . $tag . '.*0 @' . $prefix . '10@ ' . $tag . '/s', $gedcom);
            }
        }
    }
}
