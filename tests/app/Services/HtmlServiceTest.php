<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class HtmlService
 */
class HtmlServiceTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Services\HtmlService::sanitize
     *
     * @return void
     */
    public function testAllowedHtml(): void
    {
        $html_service = new HtmlService();

        $dirty = '<div class="foo">bar</div>';
        $clean = $html_service->sanitize($dirty);

        $this->assertSame($dirty, $clean);
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\HtmlService::sanitize
     *
     * @return void
     */
    public function testDisallowedHtml(): void
    {
        $html_service = new HtmlService();

        $dirty = '<div class="foo" onclick="alert(123)">bar</div>';
        $clean = $html_service->sanitize($dirty);

        $this->assertSame('<div class="foo">bar</div>', $clean);
    }
}
