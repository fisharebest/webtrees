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

namespace Fisharebest\Webtrees\Tests\Unit\Cli\Commands;

use Fisharebest\Webtrees\Cli\Commands\CompilePoFiles;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Tester\CommandTester;

use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

#[CoversClass(CompilePoFiles::class)]
class CompilePoFilesTest extends TestCase
{
    public function testCompilesPhpAndJavaScriptWithoutChangingPoFiles(): void
    {
        $directory = sys_get_temp_dir() . '/webtrees-compile-' . uniqid('', true);
        $language_directory = $directory . '/lang/fr';
        $javascript_directory = $directory . '/js';
        mkdir($language_directory, 0777, true);

        $po_content = <<<'PO'
msgid ""
msgstr ""

msgid "next"
msgstr "suivant"

msgid "unused"
msgstr "inutilisé"
PO;
        $pot_content = <<<'POT'
msgid ""
msgstr ""

msgid "next"
msgstr ""

msgid "missing"
msgstr ""

msgid "next"
msgstr ""
POT;
        file_put_contents($language_directory . '/messages.po', $po_content);
        file_put_contents($directory . '/lang/webtrees-js.pot', $pot_content);

        try {
            $tester = new CommandTester(new CompilePoFiles());
            self::assertSame(0, $tester->execute([
                '--source' => $directory . '/lang',
                '--js-destination' => $javascript_directory,
            ]));

            self::assertSame($po_content, file_get_contents($language_directory . '/messages.po'));
            self::assertSame(['next' => 'suivant', 'unused' => 'inutilisé'], require $language_directory . '/messages.php');
            self::assertStringContainsString('window.webtrees = window.webtrees || {};', file_get_contents($javascript_directory . '/fr.js'));
            self::assertStringContainsString('{"next":"suivant","missing":"missing"}', file_get_contents($javascript_directory . '/fr.js'));
        } finally {
            if (is_dir($javascript_directory)) {
                unlink($javascript_directory . '/fr.js');
                rmdir($javascript_directory);
            }

            if (is_file($language_directory . '/messages.php')) {
                unlink($language_directory . '/messages.php');
            }

            unlink($language_directory . '/messages.po');
            unlink($directory . '/lang/webtrees-js.pot');
            rmdir($language_directory);
            rmdir($directory . '/lang');
            rmdir($directory);
        }
    }

    public function testRequiresJavaScriptTemplate(): void
    {
        $directory = sys_get_temp_dir() . '/webtrees-compile-' . uniqid('', true);
        mkdir($directory);

        try {
            $tester = new CommandTester(new CompilePoFiles());
            self::assertSame(1, $tester->execute(['--source' => $directory, '--js-destination' => $directory]));
            self::assertStringContainsString('The JavaScript POT template does not exist', $tester->getDisplay());
        } finally {
            rmdir($directory);
        }
    }
}
