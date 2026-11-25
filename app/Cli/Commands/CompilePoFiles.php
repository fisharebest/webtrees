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

namespace Fisharebest\Webtrees\Cli\Commands;

use Fisharebest\Webtrees\I18N\Translation;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_key_exists;
use function array_unique;
use function array_values;
use function basename;
use function count;
use function dirname;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function is_dir;
use function is_file;
use function json_encode;
use function mkdir;
use function rtrim;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function substr;
use function trim;
use function var_export;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;

final class CompilePoFiles extends AbstractCommand
{
    private const string DEFAULT_SOURCE = Webtrees::ROOT_DIR . 'resources/lang';
    private const string JAVASCRIPT_TEMPLATE = 'webtrees-js.pot';
    private const string JAVASCRIPT_DESTINATION = Webtrees::ROOT_DIR . 'public/js/i18n';

    protected function configure(): void
    {
        $this
            ->setName(name: 'build:compile-po-files')
            ->setDescription(description: 'Compile PO files into PHP and JavaScript translations')
            ->addOption(name: 'source', shortcut: 's', mode: InputOption::VALUE_REQUIRED, description: 'Source folder containing LANG/messages.po files', default: self::DEFAULT_SOURCE)
            ->addOption(name: 'destination', shortcut: 'd', mode: InputOption::VALUE_REQUIRED, description: 'Destination folder for LANG/messages.php files')
            ->addOption(name: 'js-destination', mode: InputOption::VALUE_REQUIRED, description: 'Destination folder for JavaScript catalogs', default: self::JAVASCRIPT_DESTINATION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $source                 = rtrim($this->stringOption(input: $input, name: 'source'), DIRECTORY_SEPARATOR);
        $destination            = $this->stringOption(input: $input, name: 'destination');
        $destination            = $destination === '' ? $source : rtrim($destination, DIRECTORY_SEPARATOR);
        $javascript_destination = rtrim($this->stringOption(input: $input, name: 'js-destination'), DIRECTORY_SEPARATOR);

        if (!is_dir($source)) {
            $io->error('The source directory does not exist: ' . $source);

            return self::FAILURE;
        }

        if (!is_dir($destination)) {
            $io->error('The destination directory does not exist: ' . $destination);

            return self::FAILURE;
        }

        $javascript_template = $source . DIRECTORY_SEPARATOR . self::JAVASCRIPT_TEMPLATE;

        if (!is_file($javascript_template)) {
            $io->error('The JavaScript POT template does not exist: ' . $javascript_template);

            return self::FAILURE;
        }

        $template_content = file_get_contents($javascript_template);

        if ($template_content === false) {
            $io->error('Unable to read JavaScript POT template: ' . $javascript_template);

            return self::FAILURE;
        }

        $message_ids = $this->extractJavaScriptMessageIdsFromPot($template_content);

        if ($message_ids === []) {
            $io->error('No JavaScript messages found in: ' . $javascript_template);

            return self::FAILURE;
        }

        $po_file_pattern = $source . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'messages.po';
        $po_files        = glob(pattern: $po_file_pattern);

        if ($po_files === false || $po_files === []) {
            $io->error('Failed to find any PO files matching ' . $po_file_pattern);

            return self::FAILURE;
        }

        if (!is_dir($javascript_destination) && !mkdir($javascript_destination, 0777, true) && !is_dir($javascript_destination)) {
            $io->error('Unable to create JavaScript catalog directory: ' . $javascript_destination);

            return self::FAILURE;
        }

        $error = false;

        foreach ($po_files as $po_file) {
            $stream = fopen($po_file, 'rb');
            $translation = Translation::fromPoStream($stream);
            fclose($stream);
            $translations     = $translation->toArray();
            $language         = basename(path: dirname(path: $po_file));
            $php_file         = $destination . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'messages.php';
            $javascript_file = $javascript_destination . DIRECTORY_SEPARATOR . $language . '.js';
            $catalog         = $this->buildJavaScriptCatalog($message_ids, $translations);
            $script          = $this->buildJavaScriptCatalogScript($catalog);
            $php_code        = "<?php\n\nreturn " . var_export(value: $translations, return: true) . ";\n";
            $bytes           = file_put_contents(filename: $php_file, data: $php_code);

            if ($bytes === false) {
                $io->error('Failed to write to ' . $php_file);
                $error = true;
            } else {
                $io->success('Created ' . $php_file . ' with ' . count(value: $translations) . ' translations');
            }

            if (file_put_contents($javascript_file, $script) === false) {
                $io->error('Failed to write to ' . $javascript_file);
                $error = true;
            } else {
                $io->success('Created ' . $javascript_file . ' with ' . count($catalog) . ' translations');
            }
        }

        return $error ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<int,string>
     */
    private function extractJavaScriptMessageIdsFromPot(string $pot_contents): array
    {
        $message_ids = [];

        foreach (explode("\n", $pot_contents) as $line) {
            $line = trim($line);

            if (!str_starts_with($line, 'msgid "') || !str_ends_with($line, '"')) {
                continue;
            }

            $message_id = substr($line, 7, -1);

            if ($message_id === '') {
                continue;
            }

            $message_ids[] = str_replace(['\\"', '\\\\'], ['"', '\\'], $message_id);
        }

        return array_values(array_unique($message_ids));
    }

    /**
     * @param array<int,string>    $message_ids
     * @param array<string,string> $translations
     *
     * @return array<string,string>
     */
    private function buildJavaScriptCatalog(array $message_ids, array $translations): array
    {
        $catalog = [];

        foreach ($message_ids as $message_id) {
            $catalog[$message_id] = array_key_exists($message_id, $translations)
                ? $translations[$message_id]
                : $message_id;
        }

        return $catalog;
    }

    /**
     * @param array<string,string> $catalog
     */
    private function buildJavaScriptCatalogScript(array $catalog): string
    {
        return 'window.webtrees = window.webtrees || {};' . "\n"
            . 'window.webtrees.i18nCatalog = Object.assign(window.webtrees.i18nCatalog || {}, '
            . json_encode($catalog, JSON_THROW_ON_ERROR)
            . ');' . "\n";
    }
}
