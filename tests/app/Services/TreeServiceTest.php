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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TreeService::class)]
class TreeServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    private TreeService $tree_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tree_service = new TreeService(new GedcomImportService());
        $this->loginAsAdmin();
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(TreeService::class));
    }

    /**
     * Create a tree and verify it exists in the database.
     */
    public function testCreateTree(): void
    {
        $tree = $this->tree_service->create('test-tree', 'Test Tree');

        self::assertInstanceOf(Tree::class, $tree);
        self::assertSame('test-tree', $tree->name());
        self::assertSame('Test Tree', $tree->title());

        $exists = DB::table('gedcom')
            ->where('gedcom_id', '=', $tree->id())
            ->exists();

        self::assertTrue($exists, 'Tree must exist in database after creation');
    }

    /**
     * Delete a tree and verify all records are removed.
     */
    public function testDeleteTree(): void
    {
        $tree = $this->importTree('demo.ged');
        $tree_id = $tree->id();

        // Verify it has data
        $indi_count = DB::table('individuals')
            ->where('i_file', '=', $tree_id)
            ->count();
        self::assertGreaterThan(0, $indi_count);

        // Delete
        $this->tree_service->delete($tree);

        // Verify removal
        $remaining = DB::table('individuals')
            ->where('i_file', '=', $tree_id)
            ->count();
        self::assertSame(0, $remaining, 'No individuals should remain after tree deletion');

        $tree_exists = DB::table('gedcom')
            ->where('gedcom_id', '=', $tree_id)
            ->exists();
        self::assertFalse($tree_exists, 'Tree should not exist in database after deletion');
    }

    /**
     * all() returns a collection of trees.
     */
    public function testAllReturnsTrees(): void
    {
        $this->tree_service->create('tree-a', 'Tree A');
        $this->tree_service->create('tree-b', 'Tree B');

        $trees = $this->tree_service->all();

        self::assertGreaterThanOrEqual(2, $trees->count());
    }

    /**
     * find() retrieves a tree by its ID.
     */
    public function testFindTreeById(): void
    {
        $tree = $this->tree_service->create('find-test', 'Find Test');

        $found = $this->tree_service->find($tree->id());

        self::assertSame($tree->id(), $found->id());
        self::assertSame('find-test', $found->name());
    }

    /**
     * titles() returns an array of tree titles keyed by name.
     */
    public function testTitlesReturnsArray(): void
    {
        $this->tree_service->create('titles-test', 'Titles Test Tree');

        $titles = $this->tree_service->titles();

        self::assertIsArray($titles);
        self::assertNotEmpty($titles);
    }

    /**
     * uniqueTreeName() returns a non-empty string.
     */
    public function testUniqueTreeName(): void
    {
        $name = $this->tree_service->uniqueTreeName();

        self::assertNotEmpty($name, 'Unique tree name should not be empty');
        self::assertIsString($name);
    }

    private function loginAsAdmin(): void
    {
        $user_service = new UserService();
        $user = $user_service->findByUserName('admin');
        if ($user === null) {
            $user = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
            $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        }
        Auth::login($user);
    }
}
