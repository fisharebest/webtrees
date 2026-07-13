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

use LogicException;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\ParserSetup;
use Fisharebest\Webtrees\Webtrees;

#[CoversClass(AbstractParser::class)]
#[CoversClass(ParserSetup::class)]
class ParserSetupTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(ParserSetup::class));
    }

    public function testInvalidStyleFlagsAreRejected(): void
    {
        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/report-with-invalid-style-flags.xml';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid style flags "x". Use only lowercase b, i, u, and d.');

        (new ParserSetup($report_file))->process();
    }
}
