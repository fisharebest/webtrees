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

use Fisharebest\Webtrees\Report\StyleRepository;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StyleRepository::class)]
class StyleRepositoryTest extends TestCase
{
    public function testStoresAndReturnsStylesByName(): void
    {
        $report_style_repository = new StyleRepository();
        $style = new Style(name: 'body', style: 'b', size: 10.0);

        $report_style_repository->add($style);

        self::assertSame($style, $report_style_repository->get('body'));
        self::assertSame(['body' => $style], $report_style_repository->all());
    }
}
