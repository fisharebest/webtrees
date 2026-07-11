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
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AbstractParser::class)]
#[CoversClass(ParserGenerate::class)]
class ParserGenerateTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testTextInsideTextBoxIsRenderedInlineInHtml(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $this->importTree('demo.ged');

        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/report-with-textbox-inline-text.xml';

        $renderer = new HtmlRenderer();
        (new ParserGenerate($report_file, $renderer, [], $tree, Webtrees::NAME, Registry::timestampFactory()->make(0)))->process();
        $html = $renderer->output();

        // Text flows inside a positioned container div
        self::assertStringContainsString(
            'class="report-block report-text" style="left:2pt;top:2pt;',
            $html
        );
        // Individual text runs are inline spans with style classes
        self::assertStringContainsString(
            '<span class="text">Hello </span>',
            $html
        );
        self::assertStringContainsString(
            '<span class="name">world</span>',
            $html
        );
        self::assertStringContainsString(
            'border:solid black 1pt',
            $html
        );
        self::assertStringNotContainsString('<div style="position:absolute;top:0pt;left:0pt;width:', $html);
    }

    /**
     * Malformed XML (e.g. mismatched open/close tags) must produce a clear
     * exception rather than silently producing broken output.
     */
    public function testMalformedXmlIsRejected(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $this->importTree('demo.ged');

        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/report-with-mismatched-tags.xml';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('XML error');

        (new ParserGenerate($report_file, new HtmlRenderer(), [], $tree, Webtrees::NAME, Registry::timestampFactory()->now()))->process();
    }

    /**
     * The parser must fail loudly on XML elements that are not part of the
     * report schema rather than silently skipping them, so that typos like
     * <SerVar> for <SetVar> are caught instead of hidden.
     */
    public function testUnknownXmlElementIsRejected(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $this->importTree('demo.ged');

        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/report-with-unknown-element.xml';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('<Whoops>');

        (new ParserGenerate($report_file, new HtmlRenderer(), [], $tree, Webtrees::NAME, Registry::timestampFactory()->now()))->process();
    }

    public function testInvalidStyleFlagsAreRejected(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $this->importTree('demo.ged');

        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/report-with-invalid-style-flags.xml';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid style flags "x". Use only lowercase b, i, u, and d.');

        (new ParserGenerate($report_file, new HtmlRenderer(), [], $tree, Webtrees::NAME, Registry::timestampFactory()->now()))->process();
    }

    public function testFontVariablesAreIgnoredInFavorOfEngineDefaults(): void
    {
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = $this->importTree('demo.ged');

        $report_file = Webtrees::ROOT_DIR . 'tests/data/reports/report-with-textbox-inline-text.xml';

        $renderer = new HtmlRenderer();
        (new ParserGenerate(
            $report_file,
            $renderer,
            [
                'primary_font' => 'INVALID-FONT-NAME',
                'fallback_fonts' => 'notosansthai,INVALID-FONT-NAME',
            ],
            $tree,
            Webtrees::NAME,
            Registry::timestampFactory()->make(0),
        ))->process();

        $html = $renderer->output();

        self::assertStringContainsString('<span class="text">Hello </span>', $html);
    }
}
