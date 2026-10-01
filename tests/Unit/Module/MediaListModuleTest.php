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
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\MediaListModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

use function array_keys;

#[CoversClass(MediaListModule::class)]
class MediaListModuleTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(MediaListModule::class));
    }

    public function testFiltersMediaByFolderFormatAndTitle(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        $module       = new MediaListModule(new LinkedRecordService());

        self::assertTrue($module->listIsEmpty($tree));

        $files = [
            ['M1', $tree->id(), 'root.jpg', 'Root', 'photo'],
            ['M2', $tree->id(), 'images/cat.jpg', 'Feline portrait', 'photo'],
            ['M2', $tree->id(), 'images/secondary.jpg', 'Feline portrait', 'photo'],
            ['M3', $tree->id(), 'images/sub/deep.jpg', 'Deep', 'document'],
            ['M4', $tree->id(), 'https://example.com/remote.jpg', 'Remote', 'photo'],
            ['M5', $other_tree->id(), 'images/other.jpg', 'Other', 'photo'],
        ];
        foreach ($files as [$xref, $tree_id, $path, $title, $format]) {
            DB::query(table: 'media_file')->insert([
                'm_id'                 => $xref,
                'm_file'               => $tree_id,
                'multimedia_file_refn' => $path,
                'multimedia_format'    => 'jpg',
                'source_media_type'    => $format,
                'descriptive_title'    => $title,
            ]);

            DB::query(table: 'media')->upsert([
                'm_id'     => $xref,
                'm_file'   => $tree_id,
                'm_gedcom' => "0 @$xref@ OBJE\n1 FILE $path\n2 FORM jpg\n2 TITL $title",
            ], ['m_id', 'm_file'], ['m_gedcom']);
        }

        self::assertFalse($module->listIsEmpty($tree));

        $folders = new ReflectionMethod($module, 'allFolders');
        self::assertSame(['', 'images', 'images/sub'], array_keys($folders->invoke($module, $tree)));

        $media = new ReflectionMethod($module, 'allMedia');

        $root = $media->invoke($module, $tree, '', false, 'title', '', '');
        self::assertInstanceOf(Collection::class, $root);
        self::assertSame(['M4', 'M1'], $root->map(static fn (Media $item): string => $item->xref())->values()->all());

        $folder = $media->invoke($module, $tree, 'images', false, 'title', '', '');
        self::assertInstanceOf(Collection::class, $folder);
        self::assertSame(['M2'], $folder->map(static fn (Media $item): string => $item->xref())->values()->all());

        $subfolders = $media->invoke($module, $tree, 'images', true, 'file', '', '');
        self::assertInstanceOf(Collection::class, $subfolders);
        self::assertSame(['M2', 'M3'], $subfolders->map(static fn (Media $item): string => $item->xref())->values()->all());

        $filtered = $media->invoke($module, $tree, 'images', true, 'title', 'deep', 'document');
        self::assertInstanceOf(Collection::class, $filtered);
        self::assertSame(['M3'], $filtered->map(static fn (Media $item): string => $item->xref())->values()->all());

        $by_title = $media->invoke($module, $tree, 'images', false, 'title', 'portrait', 'photo');
        self::assertInstanceOf(Collection::class, $by_title);
        self::assertSame(['M2'], $by_title->map(static fn (Media $item): string => $item->xref())->values()->all());

        $other_tree_title = $media->invoke($module, $tree, 'images', true, 'title', 'Other', '');
        self::assertInstanceOf(Collection::class, $other_tree_title);
        self::assertTrue($other_tree_title->isEmpty());
    }
}
