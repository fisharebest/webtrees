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

namespace Fisharebest\Webtrees\Tests\Feature;

use Fisharebest\Webtrees\Elements\AbstractXrefElement;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AbstractXrefElement::class)]
#[CoversClass(GedcomEditService::class)]
class VoidPointersTest extends TestCase
{
    private Tree $tree;

    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
        (new Gedcom())->registerTags(Registry::elementFactory(), true);

        $this->tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
    }

    /**
     * @return array<array{0:string}>
     */
    public static function pointers(): array
    {
        return [
            ['INDI:ASSO'],
            ['INDI:ALIA'],
            ['INDI:ADOP:FAMC'],
            ['INDI:BIRT:SOUR'],
            ['INDI:OBJE'],
            ['INDI:NOTE'],
            ['INDI:SNOTE'],
            ['SOUR:REPO'],
            ['FAM:SUBM'],
        ];
    }

    #[DataProvider('pointers')]
    public function testPointerToNothingIsOffered(string $tag): void
    {
        $html = Registry::elementFactory()->make($tag)->edit('id', 'values[]', '', $this->tree);

        self::assertMatchesRegularExpression('/<option value="@VOID@"\s*>/', $html);
    }

    #[DataProvider('pointers')]
    public function testPointerToNothingIsSelected(string $tag): void
    {
        $html = Registry::elementFactory()->make($tag)->edit('id', 'values[]', '@VOID@', $this->tree);

        self::assertStringContainsString('<option value="@VOID@" selected="selected">', $html);
    }

    public function testSavePointerToNothing(): void
    {
        $gedcom_edit_service = new GedcomEditService();

        self::assertSame(
            "\n1 BIRT\n2 ASSO @VOID@\n3 PHRASE John Smith\n3 RELA Godparent\n2 SOUR @VOID@\n1 ALIA @VOID@",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '3', '3', '2', '1'],
                ['BIRT', 'ASSO', 'PHRASE', 'RELA', 'SOUR', 'ALIA'],
                ['', '@VOID@', 'John Smith', 'Godparent', '@VOID@', '@VOID@']
            )
        );

        // An empty pointer with a phrase is a pointer to nothing
        self::assertSame(
            "\n1 BIRT\n2 ASSO @VOID@\n3 PHRASE John Smith",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '3'],
                ['BIRT', 'ASSO', 'PHRASE'],
                ['', '', 'John Smith']
            )
        );

        // An empty pointer without a phrase is nothing
        self::assertSame(
            "\n1 BIRT",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '3'],
                ['BIRT', 'ASSO', 'ROLE'],
                ['', '', 'GODP']
            )
        );
    }
}
