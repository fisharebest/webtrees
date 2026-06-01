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

use DomainException;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function file_put_contents;
use function ob_get_clean;
use function ob_start;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

#[CoversClass(AbstractParser::class)]
#[CoversClass(ParserGenerate::class)]
class ParserGenerateTest extends TestCase
{
    protected static bool $uses_database = true;

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

        // A minimally-valid report skeleton with an unknown <Whoops/> tag
        // injected in the body.  The tag is not part of the schema so the
        // parser must throw a DomainException identifying it.
        $report_xml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <Report>
                <Doc showGeneratedBy="0">
                    <Style name="text" font="dejavusans" size="10" style=""/>
                    <Body>
                        <Whoops/>
                    </Body>
                </Doc>
            </Report>
            XML;

        $tmp_file = (string) tempnam(sys_get_temp_dir(), 'webtrees_report_test_');
        file_put_contents($tmp_file, $report_xml);

        try {
            ob_start();
            try {
                new ParserGenerate($tmp_file, new HtmlRenderer(), [], $tree);
                self::fail('Expected DomainException for unknown XML element was not thrown.');
            } catch (DomainException $e) {
                self::assertStringContainsString('<Whoops>', $e->getMessage());
                self::assertStringContainsString($tmp_file, $e->getMessage());
            } finally {
                ob_get_clean();
            }
        } finally {
            unlink($tmp_file);
        }
    }
}
