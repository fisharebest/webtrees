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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Report\CellAlign;
use Fisharebest\Webtrees\Report\ImageData;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ImageData::class)]
class ImageDataTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $data = new ImageData(
            src: '/path/to/photo.jpg',
            align: CellAlign::Center,
        );

        self::assertSame('/path/to/photo.jpg', $data->src);
        self::assertSame(CellAlign::Center, $data->align);
    }
}
