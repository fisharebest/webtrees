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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Webtrees;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;

use function ob_get_clean;
use function ob_start;

#[CoversClass(AbstractParser::class)]
#[CoversClass(ParserGenerate::class)]
class ParserGenerateTest extends TestCase
{
    protected static bool $uses_database = true;

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

        $report_file = Webtrees::ROOT_DIR . 'tests/data/report-with-mismatched-tags.xml';

        ob_start();
        try {
            new ParserGenerate($report_file, new HtmlRenderer(), [], $tree);
            self::fail('Expected LogicException for malformed XML was not thrown.');
        } catch (LogicException $exception) {
            self::assertStringContainsString('XML error', $exception->getMessage());
            self::assertStringContainsString($report_file, $exception->getMessage());
        } finally {
            ob_get_clean();
        }
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

        $report_file = Webtrees::ROOT_DIR . 'tests/data/report-with-unknown-element.xml';

        ob_start();
        try {
            new ParserGenerate($report_file, new HtmlRenderer(), [], $tree);
            self::fail('Expected LogicException for unknown XML element was not thrown.');
        } catch (LogicException $e) {
            self::assertStringContainsString('<Whoops>', $e->getMessage());
            self::assertStringContainsString($report_file, $e->getMessage());
        } finally {
            ob_get_clean();
        }
    }
}
