<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Console;

use Composer\Script\Event;
use Fisharebest\Localization\Translation;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use function basename;
use function dirname;
use function file_put_contents;
use function glob;
use function str_contains;
use function var_export;

/**
 * Command-line utilities.
 */
class ComposerScripts
{
    // Location of our translation files.
    private const PO_FILE_PATTERN = 'resources/lang/*/*.po';

    // Path to the root folder.
    private const ROOT_DIR = __DIR__ . '/../../';

    /**
     * Rebuild the .POT, .PO and .PHP file.
     *
     * @param Event $event
     */
    public static function languageFiles(Event $event): void
    {
        require $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';

        $io = $event->getIO();

        $po_files = glob(self::PO_FILE_PATTERN) ?: [];

        foreach ($po_files as $po_file) {
            $translation  = new Translation($po_file);
            $translations = $translation->asArray();
            $io->write($po_file . ': ' . count($translations));
            $php_file = dirname($po_file) . '/' . basename($po_file, '.po') . '.php';
            $php_code = "<?php\n\nreturn " . var_export($translations, true) . ";\n";

            file_put_contents($php_file, $php_code);
        }
    }

    /**
     * Ensure every class has a corresponding test script.
     *
     * @param Event $event
     */
    public static function missingTests(Event $event): void
    {
        require $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';

        $io = $event->getIO();

        $filesystem = new Filesystem(new Local(self::ROOT_DIR));

        $scripts = Collection::make($filesystem->listContents('/app/', true))
            ->filter(static function (array $file): bool {
                return $file['type'] !== 'dir';
            })
            ->map(static function (array $file): string {
                return $file['path'];
            })
            ->filter(static function (string $script): bool {
                return !str_contains($script, 'Interface.php') && !str_contains($script, 'Abstract');
            })
        ;

        foreach ($scripts as $script) {
            $class = strtr($script, ['app/' => '', '.php' => '', '/' => '\\']);
            $test  = strtr($script, ['app/' => 'tests/app/', '.php' => 'Test.php']);

            if (!$filesystem->has($test)) {
                $io->write('Creating test script for: ' . $class);
                $filesystem->write($test, self::testStub($class));
            }
        }
    }

    /**
     * Create an empty test script.
     *
     * @param string $class
     *
     * @return string
     */
    private static function testStub(string $class): string
    {
        $year       = date('Y');
        $namespace  = strtr(dirname(strtr($class, ['\\' => '/'])), ['/' => '\\']);
        $base_class = basename(strtr($class, ['\\' => '/']));

        if ($namespace === '.') {
            $namespace = 'Fisharebest\\Webtrees';
        } else {
            $namespace = 'Fisharebest\\Webtrees\\' . $namespace;
        }

        return <<<"end_of_file"
<?php

/**
 * webtrees: online genealogy
 * Copyright (C) {$year} webtrees development team
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

namespace {$namespace};

use Fisharebest\\Webtrees\\TestCase;

/**
 * Test harness for the class {$base_class}
 *
 * @covers {$namespace}\\{$base_class}
 */
class {$base_class}Test extends TestCase
{
}

end_of_file;
    }
}
