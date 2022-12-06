<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Console;

use Composer\Script\Event;
use Fisharebest\Localization\Translation;

use function basename;
use function count;
use function dirname;
use function file_put_contents;
use function glob;
use function var_export;

/**
 * Command-line utilities.
 */
class ComposerScripts
{
    // Location of our translation files.
    private const PO_FILE_PATTERN = 'resources/lang/*/*.po';

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
}
