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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Module\FixDuplicateLinks;

#[CoversClass(FixDuplicateLinks::class)]
class FixDuplicateLinksTest extends TestCase
{
    protected FixDuplicateLinks $fixDuplicateLinks;

    protected Tree $tree;

    protected bool $restore_session_user = false;

    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $this->tree = $tree_service->create('name', 'title');

        $this->fixDuplicateLinks = new FixDuplicateLinks(new DataFixService());

        $user_service = new UserService(new \Fisharebest\Webtrees\Clock\SystemClock());
        $user         = $user_service->create('user', 'real', 'email', 'pass');
        Auth::login($user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->restore_session_user) {
            Session::forget('wt_user');
        }

        unset($this->fixDuplicateLinks, $this->tree);
    }

    public function testModuleMetadata(): void
    {
        self::assertNotEmpty($this->fixDuplicateLinks->title());
        self::assertNotEmpty($this->fixDuplicateLinks->description());
    }

    public function testRecordsToFix(): void
    {
        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, []);
        self::assertCount(1, $records);

        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, ['start' => 'X1', 'end' => 'X9']);
        self::assertCount(1, $records);

        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, ['start' => 'X2', 'end' => 'X9']);
        self::assertCount(0, $records);
    }

    public function testRangesApplyToEveryRecordType(): void
    {
        DB::query(table: 'families')->insert([
            'f_id' => 'F2', 'f_file' => $this->tree->id(), 'f_gedcom' => '0 @F2@ FAM', 'f_numchil' => 0,
        ]);
        DB::query(table: 'individuals')->insert([
            'i_id' => 'I2', 'i_file' => $this->tree->id(), 'i_rin' => '', 'i_sex' => 'U', 'i_gedcom' => '0 @I2@ INDI',
        ]);
        DB::query(table: 'media')->insert([
            'm_id' => 'M2', 'm_file' => $this->tree->id(), 'm_gedcom' => '0 @M2@ OBJE',
        ]);
        DB::query(table: 'sources')->insert([
            's_id' => 'S2', 's_file' => $this->tree->id(), 's_name' => '', 's_gedcom' => '0 @S2@ SOUR',
        ]);

        foreach (['N2' => 'NOTE', 'R2' => 'REPO', 'U2' => 'SUBM'] as $xref => $type) {
            DB::query(table: 'other')->insert([
                'o_id' => $xref, 'o_file' => $this->tree->id(), 'o_type' => $type, 'o_gedcom' => '0 @' . $xref . '@ ' . $type,
            ]);
        }

        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, ['start' => 'F2', 'end' => 'U2']);

        self::assertEqualsCanonicalizing(
            ['F2:FAM', 'I2:INDI', 'M2:OBJE', 'N2:NOTE', 'R2:REPO', 'S2:SOUR', 'U2:SUBM'],
            $records->map(static fn (object $record): string => $record->xref . ':' . $record->type)->all(),
        );
        self::assertCount(8, $this->fixDuplicateLinks->recordsToFix($this->tree, ['start' => 'Z9']));
    }

    public function testPendingRecordsRetainTheirTypeAndStatusFilters(): void
    {
        $changes = [
            ['F2', '0 @F2@ FAM\\n1 HUSB @I1@', '', 'pending'],
            ['F3', '', '0 @F3@ FAM\\n1 WIFE @I2@', 'pending'],
            ['I4', '', '0 @I4@ INDI\\n1 NAME Other', 'pending'],
            ['F5', '', '0 @F5@ FAM\\n1 CHIL @I3@', 'accepted'],
        ];

        foreach ($changes as [$xref, $old_gedcom, $new_gedcom, $status]) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $this->tree->id(),
                'xref'       => $xref,
                'old_gedcom' => $old_gedcom,
                'new_gedcom' => $new_gedcom,
                'status'     => $status,
                'user_id'    => Auth::user()->id(),
            ]);
        }

        $records = $this->fixDuplicateLinks->recordsToFix($this->tree, []);

        self::assertTrue($records->contains(static fn (object $record): bool => $record->xref === 'F2' && $record->type === 'FAM'));
        self::assertTrue($records->contains(static fn (object $record): bool => $record->xref === 'F3' && $record->type === 'FAM'));
        self::assertFalse($records->contains(static fn (object $record): bool => $record->xref === 'I4' && $record->type === 'FAM'));
        self::assertFalse($records->contains(static fn (object $record): bool => $record->xref === 'F5'));
    }

    public function testDoesRecordNeedUpdate(): void
    {
        $family = $this->tree->createFamily("0 @@ FAM\n1 HUSB @X1@\n1 CHIL @X2@");
        self::assertFalse($this->fixDuplicateLinks->doesRecordNeedUpdate($family, []));

        $family->createFact('1 CHIL @X2@', true);
        self::assertTrue($this->fixDuplicateLinks->doesRecordNeedUpdate($family, []));
    }

    public function testPreviewUpdate(): void
    {
        $family = $this->tree->createFamily("0 @@ FAM\n1 HUSB @X1@\n1 CHIL @X2@\n1 CHIL @X2@");

        self::assertStringContainsString(
            '<del>1 CHIL @X2@</del>',
            $this->fixDuplicateLinks->previewUpdate($family, [])
        );
    }

    public function testUpdateRecord(): void
    {
        $family = $this->tree->createFamily("0 @@ FAM\n1 HUSB @X1@\n1 CHIL @X2@\n1 CHIL @X2@");
        $this->fixDuplicateLinks->updateRecord($family, []);

        self::assertCount(1, $family->facts(['CHIL']));
    }
}
