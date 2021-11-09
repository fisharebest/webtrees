<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
    /**
     * @covers \Fisharebest\Webtrees\Services\GedcomEditService::editLinesToGedcom
     */
    public function testEditLinesToGedcom(): void
    {
        $gedcom_edit_service = new GedcomEditService();

        $this->assertSame(
            "1 BIRT Y",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1'],
                ['BIRT'],
                ['Y']
            )
        );

        $this->assertSame(
            "1 BIRT Y\n2 ADDR England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2'],
                ['BIRT', 'ADDR'],
                ['Y', 'England']
            )
        );

        $this->assertSame(
            "1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2'],
                ['BIRT', 'PLAC'],
                ['Y', 'England']
            )
        );

        $this->assertSame(
            "1 BIRT\n2 PLAC England\n2 SOUR @S1@\n3 PAGE 123",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '@S1@', '123']
            )
        );

        // Missing SOUR, so ignore PAGE
        $this->assertSame(
            "1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123']
            )
        );

        $this->assertSame(
            "1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123']
            )
        );

        $this->assertSame(
            "1 BIRT\n2 PLAC England\n1 DEAT\n2 PLAC Scotland",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3', '1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE', 'DEAT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123', 'Y', 'Scotland', '', '123']
            )
        );

        $this->assertSame(
            "0 NOTE @N1@\n1 CONC foo\n1 CONT bar\n1 RESN locked",
            $gedcom_edit_service->editLinesToGedcom(
                'NOTE',
                ['0', '1', '1'],
                ['NOTE', 'CONC', 'RESN'],
                ['@N1@', "foo\nbar", 'locked']
            )
        );
    }
}
