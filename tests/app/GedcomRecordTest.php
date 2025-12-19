<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GedcomRecord::class)]
class GedcomRecordTest extends TestCase
{
    protected static bool $uses_database = true;

    private UserInterface $user;
    private Tree $tree;

    public function setUp(): void
    {
        parent::setUp();
        $user_service = new UserService();
        $this->user   = $user_service->create('test', 'test', 'test', '*');
        Auth::login($this->user);

        $tree_service = new TreeService(new GedcomImportService());
        $this->tree   = $tree_service->create('test', 'test');
        $this->tree->setUserPreference($this->user, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MANAGER);
    }

    public function tearDown(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree_service->delete($this->tree);

        $user_service = new UserService();
        $user_service->delete($this->user);

        parent::tearDown();
    }

    public function testCreateFact(): void
    {
        $individual = $this->tree->createIndividual('0 @@ INDI');

        $individual->createFact('1 FACT foo', false);
        $individual->createFact('1 FACT foo', false);
        $individual->createFact('1 FACT bar', false);
        $individual->createFact('1 FACT bar', false);

        $facts = $individual->facts(['FACT']);

        $this->assertCount(4, $facts);

        $individual->createFact('1 FACT baz', false, $facts[2]->id());

        $facts = $individual->facts(['FACT']);

        $this->assertCount(5, $facts);
        $this->assertSame('1 FACT foo', $facts[0]->gedcom());
        $this->assertSame('1 FACT foo', $facts[1]->gedcom());
        $this->assertSame('1 FACT baz', $facts[2]->gedcom());
        $this->assertSame('1 FACT bar', $facts[3]->gedcom());
        $this->assertSame('1 FACT bar', $facts[4]->gedcom());
    }

    public function createFactWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $individual = $this->tree->createIndividual('0 @@ INDI');
        $individual->createFact('2 FOO bar', false);
    }

    public function testDeleteFact(): void
    {
        $individual = $this->tree->createIndividual('0 @@ INDI');

        $individual->createFact('1 FACT foo', false);
        $individual->createFact('1 FACT foo', false);
        $individual->createFact('1 FACT bar', false);
        $individual->createFact('1 FACT bar', false);

        $facts = $individual->facts(['FACT']);

        $individual->deleteFact($facts[2]->id(), false);

        $facts = $individual->facts(['FACT']);

        $this->assertCount(3, $facts);
        $this->assertSame('1 FACT foo', $facts[0]->gedcom());
        $this->assertSame('1 FACT foo', $facts[1]->gedcom());
        $this->assertSame('1 FACT bar', $facts[2]->gedcom());
    }

    public function testUpdateFact(): void
    {
        $individual = $this->tree->createIndividual('0 @@ INDI');

        $individual->createFact('1 FACT foo', false);
        $individual->createFact('1 FACT foo', false);
        $individual->createFact('1 FACT bar', false);
        $individual->createFact('1 FACT bar', false);

        $facts = $individual->facts(['FACT']);

        $individual->updateFact($facts[2]->id(), '1 FACT baz', false);

        $facts = $individual->facts(['FACT']);

        $this->assertCount(4, $facts);
        $this->assertSame('1 FACT foo', $facts[0]->gedcom());
        $this->assertSame('1 FACT foo', $facts[1]->gedcom());
        $this->assertSame('1 FACT baz', $facts[2]->gedcom());
        $this->assertSame('1 FACT bar', $facts[3]->gedcom());
    }

    public function updateFactWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $individual = $this->tree->createIndividual('0 @@ INDI');
        $individual->createFact('1 FACT foo', false);
        $facts = $individual->facts(['FACT']);
        $individual->updateFact($facts[0]->id(), '2 FOO bar', false);
    }
}
