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

namespace Fisharebest\Webtrees\Tests\Unit;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\View;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(View::class)]
class ViewTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(View::class));
    }

    public function testListCountsAndTreeImportStatus(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('first', 'First');
        $other_tree     = $tree_service->create('second', 'Second');

        $import_service->importRecord('0 @R1@ REPO', $tree, true);
        $import_service->importRecord('0 @S1@ SOUR', $tree, true);
        $import_service->importRecord('0 @S2@ SOUR', $tree, true);
        $import_service->importRecord('0 @S3@ SOUR', $other_tree, true);

        foreach (['S1', 'S2'] as $xref) {
            DB::query(table: 'link')->insert([
                'l_file' => $tree->id(), 'l_from' => $xref, 'l_type' => 'REPO', 'l_to' => 'R1',
            ]);
        }

        DB::query(table: 'link')->insert([
            'l_file' => $other_tree->id(), 'l_from' => 'S3', 'l_type' => 'REPO', 'l_to' => 'R1',
        ]);
        DB::query(table: 'link')->insert([
            'l_file' => $tree->id(), 'l_from' => 'S1', 'l_type' => 'OTHER', 'l_to' => 'R1',
        ]);

        $repository = Registry::repositoryFactory()->make('R1', $tree);
        self::assertNotNull($repository);

        $html = (new View('lists/repositories-table', [
            'repositories' => new Collection([$repository]),
            'tree'         => $tree,
        ]))->render();

        self::assertMatchesRegularExpression('/data-sort="2">\s*2\s*<\/td>/', $html);

        foreach (
            [
                'locations-table'  => 'locations',
                'media-table'      => 'media_objects',
                'notes-table'      => 'notes',
                'sources-table'    => 'sources',
                'submitters-table' => 'submitters',
            ] as $template => $parameter
        ) {
            $html = (new View('lists/' . $template, [
                $parameter => new Collection(),
                'tree'     => $tree,
            ]))->render();

            self::assertStringContainsString('data-columns=', $html);
        }

        $html = (new View('admin/trees', [
            'all_trees' => new Collection([$tree]),
            'title'     => 'Trees',
            'tree'      => $tree,
        ]))->render();

        self::assertStringNotContainsString('id="import' . $tree->id() . '"', $html);

        DB::query(table: 'gedcom_chunk')->insert([
            'gedcom_id'  => $tree->id(),
            'chunk_data' => "0 HEAD\n1 SOUR webtrees\n0 TRLR",
            'imported'   => 0,
        ]);

        $html = (new View('admin/trees', [
            'all_trees' => new Collection([$tree]),
            'title'     => 'Trees',
            'tree'      => $tree,
        ]))->render();

        self::assertStringContainsString('id="import' . $tree->id() . '"', $html);
    }
}
