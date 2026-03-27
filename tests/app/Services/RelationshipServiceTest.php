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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RelationshipService::class)]
class RelationshipServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    private RelationshipService $relationship_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship_service = new RelationshipService();
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(RelationshipService::class));
    }

    /**
     * Two known spouses should have a non-empty relationship name.
     */
    public function testGetCloseRelationshipNameForSpouses(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        // X1030 = Queen Elizabeth II, X1041 = her husband (family f1)
        $elizabeth = Registry::individualFactory()->make('X1030', $tree);
        $husband = Registry::individualFactory()->make('X1041', $tree);

        self::assertNotNull($elizabeth, 'Elizabeth (X1030) should exist');
        self::assertNotNull($husband, 'Husband (X1041) should exist');

        $name = $this->relationship_service->getCloseRelationshipName($elizabeth, $husband);

        self::assertSame('husband', $name);
    }

    /**
     * A parent-child relationship should produce a non-empty name.
     */
    public function testGetCloseRelationshipNameForParentChild(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        // X1030 = Elizabeth, X1052 = her son (child of family f1)
        $elizabeth = Registry::individualFactory()->make('X1030', $tree);
        $son = Registry::individualFactory()->make('X1052', $tree);

        self::assertNotNull($elizabeth, 'Elizabeth (X1030) should exist');
        self::assertNotNull($son, 'Son (X1052) should exist');

        $name = $this->relationship_service->getCloseRelationshipName($elizabeth, $son);

        self::assertSame('son', $name);
    }

    /**
     * Same person to itself should return 'herself' or 'himself'.
     */
    public function testGetCloseRelationshipNameForSamePerson(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $elizabeth = Registry::individualFactory()->make('X1030', $tree);
        self::assertNotNull($elizabeth);

        $name = $this->relationship_service->getCloseRelationshipName($elizabeth, $elizabeth);

        self::assertSame('herself', $name);
    }

    /**
     * Reverse direction: child to parent.
     */
    public function testGetCloseRelationshipNameChildToParent(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $elizabeth = Registry::individualFactory()->make('X1030', $tree);
        $son = Registry::individualFactory()->make('X1052', $tree);

        self::assertNotNull($elizabeth);
        self::assertNotNull($son);

        $name = $this->relationship_service->getCloseRelationshipName($son, $elizabeth);

        self::assertSame('mother', $name);
    }

    private function loginAsAdmin(): void
    {
        $user = (new UserService())->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);
    }
}
