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
use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\MediaCrop;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Cache\Adapter\NullAdapter;

#[CoversClass(Individual::class)]
#[CoversClass(MediaFile::class)]
#[CoversClass(MediaCrop::class)]
class IndividualTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(Individual::class));
    }

    public function testDisplayImageShowsTheCropOfTheMediaLink(): void
    {
        $individual = $this->individualWithPhoto("\n2 CROP\n3 TOP 10\n3 LEFT 20\n3 HEIGHT 30\n3 WIDTH 40");

        $html = $individual->displayImage(100, 100, 'crop', []);

        self::assertStringContainsString('crop=10%2C20%2C30%2C40', $html);
    }

    public function testDisplayImageWithoutCropShowsTheWholeImage(): void
    {
        $individual = $this->individualWithPhoto('');

        $html = $individual->displayImage(100, 100, 'crop', []);

        self::assertStringContainsString('fit=crop', $html);
        self::assertStringNotContainsString('crop=', $html);
    }

    private function individualWithPhoto(string $crop): Individual
    {
        self::createDatabase();

        $cache_factory = self::createStub(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);

        $tree = (new TreeService(new GedcomImportService()))->create('tree', 'Tree');
        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        Auth::login($user);

        $media = $tree->createMediaObject("0 @@ OBJE\n1 FILE photo.jpg\n2 FORM jpg\n3 TYPE photo");

        return $tree->createIndividual("0 @@ INDI\n1 NAME Anna /Muster/\n1 OBJE @" . $media->xref() . '@' . $crop);
    }
}
