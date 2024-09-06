<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FixDuplicateLinks::class)]
#[CoversTrait(ModuleDataFixTrait::class)]
class FixDuplicateLinksTest extends TestCase
{
    protected static bool $uses_database = true;

    protected FixDuplicateLinks $fixDuplicateLinks;

    protected Tree $tree;

    protected bool $restore_session_user = false;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $tree_service = new TreeService(new GedcomImportService());
        $this->tree = $tree_service->create('name', 'title');

        $this->fixDuplicateLinks = new FixDuplicateLinks(new DataFixService());

        $user_service = new UserService();
        $user         = $user_service->create('user', 'real', 'email', 'pass');
        Auth::login($user);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->restore_session_user) {
            Session::forget('wt_user');
        }

        unset($this->fixDuplicateLinks, $this->tree);
    }

    /**
     * Test the module returns a title and a description
     */
    public function testModuleMetadata(): void
    {
        self::assertNotEmpty($this->fixDuplicateLinks->title());
        self::assertNotEmpty($this->fixDuplicateLinks->description());
    }

    /**
     * Test the trait's recordsToFix method
     */
    public function testRecordsToFix(): void
    {
        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, []);
        self::assertInstanceOf(Collection::class, $records);
        self::assertCount(1, $records);

        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, ['start' => 'X1', 'end' => 'X9']);
        self::assertCount(1, $records);

        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, ['start' => 'X2', 'end' => 'X9']);
        self::assertCount(0, $records);
    }

    /**
     * Test the doesRecordNeedUpdate method on a negative and positive test
     */
    public function testDoesRecordNeedUpdate(): void
    {
        $family = $this->tree->createFamily("0 @@ FAM\n1 HUSB @X1@\n1 CHIL @X2@");
        self::assertFalse($this->fixDuplicateLinks->doesRecordNeedUpdate($family, []));

        $family->createFact('1 CHIL @X2@', true);
        self::assertTrue($this->fixDuplicateLinks->doesRecordNeedUpdate($family, []));
    }

    /**
     * Test the preview of the update
     */
    public function testPreviewUpdate(): void
    {
        $family = $this->tree->createFamily("0 @@ FAM\n1 HUSB @X1@\n1 CHIL @X2@\n1 CHIL @X2@");

        self::assertStringContainsString(
            '<del>1 CHIL @X2@</del>',
            $this->fixDuplicateLinks->previewUpdate($family, [])
        );
    }

    /**
     * Test the update of the record
     */
    public function testUpdateRecord(): void
    {
        $family = $this->tree->createFamily("0 @@ FAM\n1 HUSB @X1@\n1 CHIL @X2@\n1 CHIL @X2@");
        $this->fixDuplicateLinks->updateRecord($family, []);

        self::assertCount(1, $family->facts(['CHIL']));
    }
}
