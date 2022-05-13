<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class GedcomEditService
 *
 * @covers \Fisharebest\Webtrees\Services\GedcomEditService
 */
class GedcomEditServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Services\GedcomEditService::editLinesToGedcom
     */
    public function testEditLinesToGedcom(): void
    {
        $gedcom_edit_service = new GedcomEditService();

        static::assertSame(
            '1 BIRT Y',
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1'],
                ['BIRT'],
                ['Y'],
                false
            )
        );

        static::assertSame(
            "\n1 BIRT Y\n2 ADDR England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2'],
                ['BIRT', 'ADDR'],
                ['Y', 'England']
            )
        );

        static::assertSame(
            "\n1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2'],
                ['BIRT', 'PLAC'],
                ['Y', 'England']
            )
        );

        static::assertSame(
            "\n1 BIRT\n2 PLAC England\n2 SOUR @S1@\n3 PAGE 123",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '@S1@', '123']
            )
        );

        // Missing SOUR, so ignore PAGE
        static::assertSame(
            "\n1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123']
            )
        );

        static::assertSame(
            "\n1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123']
            )
        );

        static::assertSame(
            "\n1 BIRT\n2 PLAC England\n1 DEAT\n2 PLAC Scotland",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3', '1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE', 'DEAT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123', 'Y', 'Scotland', '', '123']
            )
        );
    }
}
