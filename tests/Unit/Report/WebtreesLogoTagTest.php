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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Report\AbstractParser;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\ParserGenerate;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AbstractParser::class)]
#[CoversClass(ParserGenerate::class)]
class WebtreesLogoTagTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * Default dimensions (80pt × 20pt) and a clickable link are rendered.
     */
    public function testDefaultDimensions(): void
    {
        $html = $this->renderReport('report-with-webtrees-logo.xml');

        // The logo is a clickable link to the home page
        self::assertStringContainsString('href="', $html);
        self::assertStringContainsString('width:80pt', $html);
        self::assertStringContainsString('height:20pt', $html);
        // SVG data is embedded inline
        self::assertStringContainsString('data:image/svg+xml;base64,', $html);
    }

    /**
     * When only width is specified, height is calculated from 4:1 aspect ratio.
     */
    public function testWidthOnlyCalculatesHeight(): void
    {
        $html = $this->renderReport('report-with-webtrees-logo-width-only.xml');

        self::assertStringContainsString('width:200pt', $html);
        self::assertStringContainsString('height:50pt', $html);
        self::assertStringContainsString('href="', $html);
    }

    /**
     * When only height is specified, width is calculated from 4:1 aspect ratio.
     */
    public function testHeightOnlyCalculatesWidth(): void
    {
        $html = $this->renderReport('report-with-webtrees-logo-height-only.xml');

        self::assertStringContainsString('width:200pt', $html);
        self::assertStringContainsString('height:50pt', $html);
        self::assertStringContainsString('href="', $html);
    }

    /**
     * Render a test report XML to HTML and return the output.
     */
    private function renderReport(string $filename): string
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $this->importTree('demo.ged');

        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/' . $filename;

        $renderer = new HtmlRenderer();
        (new ParserGenerate(
            $report_file,
            $renderer,
            [],
            $tree,
            Webtrees::NAME,
            Registry::timestampFactory()->make(0),
        ))->process();

        return $renderer->output();
    }
}
