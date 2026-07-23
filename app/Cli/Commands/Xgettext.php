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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_key_exists;
use function array_filter;
use function array_merge;
use function array_map;
use function array_unique;
use function array_values;
use function addcslashes;
use function basename;
use function copy;
use function count;
use function dirname;
use function escapeshellarg;
use function exec;
use function explode;
use function file_get_contents;
use function file_exists;
use function file_put_contents;
use function glob;
use function implode;
use function is_dir;
use function is_file;
use function is_string;
use function ltrim;
use function max;
use function microtime;
use function mkdir;
use function number_format;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function json_encode;
use function round;
use function rmdir;
use function rtrim;
use function sort;
use function ksort;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function str_replace;
use function sys_get_temp_dir;
use function substr;
use function trim;
use function uniqid;
use function unlink;
use function var_export;
use function stripcslashes;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;
use const STR_PAD_LEFT;

final class Xgettext extends AbstractCommand
{
    private const string ROOT_DIRECTORY = Webtrees::ROOT_DIR;

    private const string LANGUAGE_DIRECTORY = 'resources/lang';

    private const string PHP_TEMPLATE = self::LANGUAGE_DIRECTORY . '/webtrees-php.pot';

    private const string PHTML_TEMPLATE = self::LANGUAGE_DIRECTORY . '/webtrees-phtml.pot';

    private const string JAVASCRIPT_TEMPLATE = self::LANGUAGE_DIRECTORY . '/webtrees-js.pot';

    private const string XML_TEMPLATE = self::LANGUAGE_DIRECTORY . '/webtrees-xml.pot';

    private const string JAVASCRIPT_DIRECTORY = 'public/js/i18n';

    // These look like printf placeholders, but they aren't.
    private const string DATE_FORMAT = '%j %F %Y';
    private const string TIME_FORMAT = '%H:%i:%s';

    private string|null $template_merge_error = null;

    protected function configure(): void
    {
        $this
            ->setName('xgettext')
            ->setDescription('Extract PHP, PHTML, JavaScript and XML translation templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $template_rows = [];

        $php_files        = $this->collectFiles(self::ROOT_DIRECTORY, 'app', '.php');

        if ($php_files === []) {
            $io->error('No PHP files found for extraction.');

            return self::FAILURE;
        }

        $php_started_at = microtime(true);
        $php_extracted  = $this->runCommand($this->xgettextPhpCommand(self::PHP_TEMPLATE, $php_files), self::ROOT_DIRECTORY);

        if (!$php_extracted) {
            $io->error('Failed to generate PHP template: ' . self::PHP_TEMPLATE);

            return self::FAILURE;
        }

        $template_rows[] = [
            self::PHP_TEMPLATE,
            'app',
            '.php',
            $this->formatNumber(count($php_files)),
            $this->formatNumber($this->countMessages(self::ROOT_DIRECTORY . self::PHP_TEMPLATE)),
            $this->formatNumber((int) round((microtime(true) - $php_started_at) * 1000)) . 'ms',
        ];

        $phtml_files = $this->collectFiles(self::ROOT_DIRECTORY, 'resources/views', '.phtml');

        if ($phtml_files === []) {
            $io->error('No PHTML files found for extraction.');

            return self::FAILURE;
        }

        $phtml_started_at = microtime(true);
        $phtml_extracted  = $this->runCommand($this->xgettextPhpCommand(self::PHTML_TEMPLATE, $phtml_files), self::ROOT_DIRECTORY);

        if (!$phtml_extracted) {
            $io->error('Failed to generate PHTML template: ' . self::PHTML_TEMPLATE);

            return self::FAILURE;
        }

        $template_rows[] = [
            self::PHTML_TEMPLATE,
            'resources/views',
            '.phtml',
            $this->formatNumber(count($phtml_files)),
            $this->formatNumber($this->countMessages(self::ROOT_DIRECTORY . self::PHTML_TEMPLATE)),
            $this->formatNumber((int) round((microtime(true) - $phtml_started_at) * 1000)) . 'ms',
        ];

        $javascript_files = $this->collectFiles(self::ROOT_DIRECTORY, 'resources/js', '.js');

        if ($javascript_files === []) {
            $io->error('No JavaScript files found for extraction.');

            return self::FAILURE;
        }

        $javascript_started_at = microtime(true);
        $javascript_extracted  = $this->runCommand($this->xgettextJavaScriptCommand(self::JAVASCRIPT_TEMPLATE, $javascript_files), self::ROOT_DIRECTORY);

        if (!$javascript_extracted) {
            $io->error('Failed to generate JavaScript template: ' . self::JAVASCRIPT_TEMPLATE);

            return self::FAILURE;
        }

        $template_rows[] = [
            self::JAVASCRIPT_TEMPLATE,
            'resources/js',
            '.js',
            $this->formatNumber(count($javascript_files)),
            $this->formatNumber($this->countMessages(self::ROOT_DIRECTORY . self::JAVASCRIPT_TEMPLATE)),
            $this->formatNumber((int) round((microtime(true) - $javascript_started_at) * 1000)) . 'ms',
        ];

        $xml_files = $this->collectFiles(self::ROOT_DIRECTORY, 'resources/xml/reports', '.xml');

        if ($xml_files === []) {
            $io->error('No XML files found for extraction.');

            return self::FAILURE;
        }

        $xml_started_at = microtime(true);

        $xml_template_directory = $this->prepareXmlTemplateDirectory($xml_files);

        try {
            $xml_template_files  = $this->collectFiles($xml_template_directory, '', '.xml');
            $xml_template_output = self::ROOT_DIRECTORY . self::XML_TEMPLATE;
            $xml_extracted       = $this->runCommand($this->xgettextPhpCommand($xml_template_output, $xml_template_files), $xml_template_directory);

            if (!$xml_extracted) {
                $io->error('Failed to generate XML template: ' . self::XML_TEMPLATE);

                return self::FAILURE;
            }
        } finally {
            $this->deleteDirectory($xml_template_directory);
        }

        $template_rows[] = [
            self::XML_TEMPLATE,
            'resources/xml/reports',
            '.xml',
            $this->formatNumber(count($xml_files)),
            $this->formatNumber($this->countMessages(self::ROOT_DIRECTORY . self::XML_TEMPLATE)),
            $this->formatNumber((int) round((microtime(true) - $xml_started_at) * 1000)) . 'ms',
        ];

        $table = new Table($output);
        $table->setHeaders([
            'POT filename',
            'Directory',
            'Extension',
            'Files',
            'Messages',
            'Processing time',
        ]);
        $table->setRows($template_rows);

        $right_align = new TableStyle();
        $right_align->setPadType(STR_PAD_LEFT);
        $table->setColumnStyle(3, $right_align);
        $table->setColumnStyle(4, $right_align);
        $table->setColumnStyle(5, $right_align);
        $table->render();

        $language_catalog_filenames = $this->collectLanguageCatalogs();

        if ($language_catalog_filenames === []) {
            $io->error('No language catalogs found in: ' . self::LANGUAGE_DIRECTORY);

            return self::FAILURE;
        }

        $javascript_template_filename = self::ROOT_DIRECTORY . self::JAVASCRIPT_TEMPLATE;

        if (!is_file($javascript_template_filename)) {
            $io->error('The JavaScript POT template does not exist: ' . self::JAVASCRIPT_TEMPLATE);

            return self::FAILURE;
        }

        $javascript_template_content = file_get_contents($javascript_template_filename);

        if ($javascript_template_content === false) {
            $io->error('Unable to read JavaScript POT template: ' . self::JAVASCRIPT_TEMPLATE);

            return self::FAILURE;
        }

        $merged_template_filename = $this->createMergedTemplate();

        if ($merged_template_filename === null) {
            $io->error($this->template_merge_error ?? 'Failed to merge POT templates for updating language catalogs.');

            return self::FAILURE;
        }

        $javascript_message_ids = $this->extractJavaScriptMessageIdsFromPot($javascript_template_content);
        $javascript_directory   = self::ROOT_DIRECTORY . self::JAVASCRIPT_DIRECTORY;

        if (!is_dir($javascript_directory)) {
            mkdir($javascript_directory, 0777, true);
        }

        if (!is_dir($javascript_directory)) {
            $io->error('Unable to create JavaScript catalog directory: ' . self::JAVASCRIPT_DIRECTORY);

            return self::FAILURE;
        }

        $language_rows = [];
        $error         = false;

        try {
            foreach ($language_catalog_filenames as $language_catalog_filename) {
                $language_started_at = microtime(true);
                $language_name = basename(dirname($language_catalog_filename));
                $po_file       = self::ROOT_DIRECTORY . $language_catalog_filename;
                $php_file      = self::ROOT_DIRECTORY . self::LANGUAGE_DIRECTORY . '/' . $language_name . '/messages.php';
                $js_file       = self::ROOT_DIRECTORY . self::JAVASCRIPT_DIRECTORY . '/' . $language_name . '.js';

                $catalog_updated = $this->updateLanguageCatalog($po_file, $merged_template_filename);

                if (!$catalog_updated) {
                    $error = true;
                    $language_rows[] = [
                        $language_name,
                        $language_catalog_filename,
                        'failed',
                        self::JAVASCRIPT_DIRECTORY . '/' . $language_name . '.js',
                        'failed',
                        self::LANGUAGE_DIRECTORY . '/' . $language_name . '/messages.php',
                        $this->formatNumber((int) round((microtime(true) - $language_started_at) * 1000)) . 'ms',
                    ];
                    continue;
                }

                $po_status    = $this->countPoTranslationStatus($po_file);
                $translations = $this->compilePoFile($po_file, $php_file);

                if ($translations === null) {
                    $error = true;
                    $language_rows[] = [
                        $language_name,
                        $language_catalog_filename,
                        $this->formatStatus($po_status['translated'], $po_status['total']),
                        self::JAVASCRIPT_DIRECTORY . '/' . $language_name . '.js',
                        'failed',
                        self::LANGUAGE_DIRECTORY . '/' . $language_name . '/messages.php',
                        $this->formatNumber((int) round((microtime(true) - $language_started_at) * 1000)) . 'ms',
                    ];
                    continue;
                }

                $catalog      = $this->buildJavaScriptCatalog($javascript_message_ids, $translations);
                $script       = $this->buildJavaScriptCatalogScript($catalog);
                $bytes_written = file_put_contents($js_file, $script);

                if ($bytes_written === false) {
                    $error = true;
                }

                $js_status = $this->countJavaScriptTranslationStatus($javascript_message_ids, $translations);

                $language_rows[] = [
                    $language_name,
                    $language_catalog_filename,
                    $this->formatStatus($po_status['translated'], $po_status['total']),
                    self::JAVASCRIPT_DIRECTORY . '/' . $language_name . '.js',
                    $bytes_written === false ? 'failed' : $this->formatStatus($js_status['translated'], $js_status['total']),
                    self::LANGUAGE_DIRECTORY . '/' . $language_name . '/messages.php',
                    $this->formatNumber((int) round((microtime(true) - $language_started_at) * 1000)) . 'ms',
                ];
            }
        } finally {
            if (file_exists($merged_template_filename)) {
                unlink($merged_template_filename);
            }
        }

        $language_table = new Table($output);
        $language_table->setHeaders([
            'Language',
            'PO file',
            'PO status',
            'JS file',
            'JS status',
            'Compiled PHP file',
            'Processing time',
        ]);
        $language_table->setRows($language_rows);
        $language_table->setColumnStyle(2, $right_align);
        $language_table->setColumnStyle(4, $right_align);
        $language_table->setColumnStyle(6, $right_align);
        $language_table->render();

        $placeholder_check_failed = false;

        foreach ($language_catalog_filenames as $language_catalog_filename) {
            $po_file            = self::ROOT_DIRECTORY . $language_catalog_filename;
            $placeholder_issues = $this->validatePoPlaceholders($po_file);

            if ($placeholder_issues === []) {
                continue;
            }

            if (!$placeholder_check_failed) {
                $io->newLine();
                $io->error('Placeholder mismatch checks failed.');
                $placeholder_check_failed = true;
            }

            $error = true;
            $io->writeln('Placeholder mismatch in ' . $language_catalog_filename . ':');

            foreach ($placeholder_issues as $issue) {
                $io->writeln('  source: ' . $this->formatDisplayString($issue['source']));
                $io->writeln('  translated: ' . $this->formatDisplayString($issue['translated']));
            }
        }

        return $error ? self::FAILURE : self::SUCCESS;
    }

    private function createMergedTemplate(): string|null
    {
        $this->template_merge_error = null;
        $merged_template_filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'webtrees-pot-' . uniqid('', true) . '.pot';

        $merged_content = $this->mergePotContents([
            self::ROOT_DIRECTORY . self::PHP_TEMPLATE,
            self::ROOT_DIRECTORY . self::PHTML_TEMPLATE,
            self::ROOT_DIRECTORY . self::JAVASCRIPT_TEMPLATE,
            self::ROOT_DIRECTORY . self::XML_TEMPLATE,
        ]);

        if ($merged_content === null) {
            return null;
        }

        if (file_put_contents($merged_template_filename, $merged_content) === false) {
            $this->template_merge_error = 'Unable to write merged POT template: ' . $merged_template_filename;

            return null;
        }

        return $merged_template_filename;
    }

    /**
     * @return array<int,string>
     */
    private function collectLanguageCatalogs(): array
    {
        $pattern = self::ROOT_DIRECTORY . self::LANGUAGE_DIRECTORY . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'messages.po';
        $files   = glob($pattern);

        if ($files === false || $files === []) {
            return [];
        }

        $catalogs = [];

        foreach ($files as $file) {
            $catalogs[] = ltrim(str_replace(self::ROOT_DIRECTORY, '', $file), DIRECTORY_SEPARATOR);
        }

        sort($catalogs);

        return $catalogs;
    }

    private function updateLanguageCatalog(string $po_file, string $template_file): bool
    {
        $merged = $this->runCommand(sprintf(
            'msgmerge --no-wrap --add-location=file --no-fuzzy-matching --quiet --output=%s %s %s',
            escapeshellarg($po_file),
            escapeshellarg($po_file),
            escapeshellarg($template_file),
        ), self::ROOT_DIRECTORY);

        if (!$merged) {
            return false;
        }

        $sorted = $this->runCommand(sprintf(
            'msgcat --no-wrap --sort-output --output=%s %s',
            escapeshellarg($po_file),
            escapeshellarg($po_file),
        ), self::ROOT_DIRECTORY);

        if (!$sorted) {
            return false;
        }

        return true;
    }

    /**
     * @param array<int,string> $template_filenames
     */
    private function mergePotContents(array $template_filenames): string|null
    {
        $merged_header = null;
        $merged_entries = [];

        foreach ($template_filenames as $template_filename) {
            $content = file_get_contents($template_filename);

            if ($content === false) {
                $this->template_merge_error = 'Unable to read POT template: ' . $template_filename;

                return null;
            }

            $entries = $this->parsePotEntries($content);

            foreach ($entries as $entry) {
                if ($entry['msgid'] === '') {
                    $merged_header = $merged_header === null
                        ? $entry
                        : $this->mergePotEntry($merged_header, $entry);

                    continue;
                }

                $key = ($entry['msgctxt'] ?? '') . "\x04" . $entry['msgid'] . "\x04" . ($entry['msgid_plural'] ?? '');

                if (array_key_exists($key, $merged_entries)) {
                    $merged_entries[$key] = $this->mergePotEntry($merged_entries[$key], $entry);
                } else {
                    $merged_entries[$key] = $entry;
                }
            }
        }

        ksort($merged_entries);

        if ($merged_header === null) {
            $merged_header = [
                'translator_comments' => [],
                'extracted_comments' => [],
                'references' => [],
                'flags' => [],
                'msgctxt' => null,
                'msgid' => '',
                'msgid_plural' => null,
                'msgstr' => [0 => "Content-Type: text/plain; charset=UTF-8\\n"],
            ];
        }

        return $this->renderPotEntries($merged_header, array_values($merged_entries));
    }

    /**
     * @return array<int,array{translator_comments:array<int,string>,extracted_comments:array<int,string>,references:array<int,string>,flags:array<int,string>,msgctxt:string|null,msgid:string,msgid_plural:string|null,msgstr:array<int,string>}>
     */
    private function parsePotEntries(string $content): array
    {
        $lines    = explode("\n", str_replace("\r\n", "\n", $content));
        $lines[]  = '';
        $entries  = [];
        $entry    = null;
        $active_field = null;

        foreach ($lines as $line) {
            if ($line === '') {
                if ($entry !== null && $entry['msgid'] !== null) {
                    $entries[] = [
                        'translator_comments' => $entry['translator_comments'],
                        'extracted_comments' => $entry['extracted_comments'],
                        'references' => $entry['references'],
                        'flags' => $entry['flags'],
                        'msgctxt' => $entry['msgctxt'],
                        'msgid' => $entry['msgid'],
                        'msgid_plural' => $entry['msgid_plural'],
                        'msgstr' => $entry['msgstr'],
                    ];
                }

                $entry = null;
                $active_field = null;

                continue;
            }

            if ($entry === null) {
                $entry = [
                    'translator_comments' => [],
                    'extracted_comments' => [],
                    'references' => [],
                    'flags' => [],
                    'msgctxt' => null,
                    'msgid' => null,
                    'msgid_plural' => null,
                    'msgstr' => [],
                ];
            }

            if (str_starts_with($line, '#. ')) {
                $entry['extracted_comments'][] = substr($line, 3);

                continue;
            }

            if (str_starts_with($line, '#: ')) {
                $entry['references'] = array_merge($entry['references'], array_values(array_filter(explode(' ', trim(substr($line, 3))), static fn (string $value): bool => $value !== '')));

                continue;
            }

            if (str_starts_with($line, '#, ')) {
                $entry['flags'] = array_merge($entry['flags'], array_map(static fn (string $flag): string => trim($flag), explode(',', substr($line, 3))));

                continue;
            }

            if (str_starts_with($line, '# ')) {
                $entry['translator_comments'][] = substr($line, 2);

                continue;
            }

            if (preg_match('/^msgctxt "(.*)"$/', $line, $matches) === 1) {
                $entry['msgctxt'] = stripcslashes($matches[1]);
                $active_field = 'msgctxt';

                continue;
            }

            if (preg_match('/^msgid "(.*)"$/', $line, $matches) === 1) {
                $entry['msgid'] = stripcslashes($matches[1]);
                $active_field = 'msgid';

                continue;
            }

            if (preg_match('/^msgid_plural "(.*)"$/', $line, $matches) === 1) {
                $entry['msgid_plural'] = stripcslashes($matches[1]);
                $active_field = 'msgid_plural';

                continue;
            }

            if (preg_match('/^msgstr(?:\[(\d+)])? "(.*)"$/', $line, $matches) === 1) {
                $msgstr_index = $matches[1] === '' ? 0 : (int) $matches[1];
                $entry['msgstr'][$msgstr_index] = stripcslashes($matches[2]);
                $active_field = 'msgstr:' . $msgstr_index;

                continue;
            }

            if (preg_match('/^"(.*)"$/', $line, $matches) !== 1 || $active_field === null) {
                continue;
            }

            $continuation = stripcslashes($matches[1]);

            if ($active_field === 'msgctxt') {
                $entry['msgctxt'] = ($entry['msgctxt'] ?? '') . $continuation;

                continue;
            }

            if ($active_field === 'msgid') {
                $entry['msgid'] = ($entry['msgid'] ?? '') . $continuation;

                continue;
            }

            if ($active_field === 'msgid_plural') {
                $entry['msgid_plural'] = ($entry['msgid_plural'] ?? '') . $continuation;

                continue;
            }

            if (str_starts_with($active_field, 'msgstr:')) {
                $msgstr_index = (int) substr($active_field, 7);
                $entry['msgstr'][$msgstr_index] = ($entry['msgstr'][$msgstr_index] ?? '') . $continuation;
            }
        }

        return $entries;
    }

    /**
     * @param array{translator_comments:array<int,string>,extracted_comments:array<int,string>,references:array<int,string>,flags:array<int,string>,msgctxt:string|null,msgid:string,msgid_plural:string|null,msgstr:array<int,string>} $left
     * @param array{translator_comments:array<int,string>,extracted_comments:array<int,string>,references:array<int,string>,flags:array<int,string>,msgctxt:string|null,msgid:string,msgid_plural:string|null,msgstr:array<int,string>} $right
     *
     * @return array{translator_comments:array<int,string>,extracted_comments:array<int,string>,references:array<int,string>,flags:array<int,string>,msgctxt:string|null,msgid:string,msgid_plural:string|null,msgstr:array<int,string>}
     */
    private function mergePotEntry(array $left, array $right): array
    {
        $translator_comments = array_values(array_unique(array_merge($left['translator_comments'], $right['translator_comments'])));
        $extracted_comments  = array_values(array_unique(array_merge($left['extracted_comments'], $right['extracted_comments'])));
        $references          = array_values(array_unique(array_merge($left['references'], $right['references'])));
        $flags               = array_values(array_unique(array_merge($left['flags'], $right['flags'])));

        sort($translator_comments);
        sort($extracted_comments);
        sort($references);
        sort($flags);

        return [
            'translator_comments' => $translator_comments,
            'extracted_comments' => $extracted_comments,
            'references' => $references,
            'flags' => $flags,
            'msgctxt' => $left['msgctxt'],
            'msgid' => $left['msgid'],
            'msgid_plural' => $left['msgid_plural'],
            'msgstr' => $left['msgstr'] !== [] ? $left['msgstr'] : $right['msgstr'],
        ];
    }

    /**
     * @param array{translator_comments:array<int,string>,extracted_comments:array<int,string>,references:array<int,string>,flags:array<int,string>,msgctxt:string|null,msgid:string,msgid_plural:string|null,msgstr:array<int,string>} $header
     * @param array<int,array{translator_comments:array<int,string>,extracted_comments:array<int,string>,references:array<int,string>,flags:array<int,string>,msgctxt:string|null,msgid:string,msgid_plural:string|null,msgstr:array<int,string>}> $entries
     */
    private function renderPotEntries(array $header, array $entries): string
    {
        $output_lines = [];

        $write_entry = static function (array $entry) use (&$output_lines): void {
            foreach ($entry['translator_comments'] as $comment) {
                $output_lines[] = '# ' . $comment;
            }

            foreach ($entry['extracted_comments'] as $comment) {
                $output_lines[] = '#. ' . $comment;
            }

            if ($entry['references'] !== []) {
                $output_lines[] = '#: ' . implode(' ', $entry['references']);
            }

            if ($entry['flags'] !== []) {
                $output_lines[] = '#, ' . implode(', ', $entry['flags']);
            }

            if ($entry['msgctxt'] !== null) {
                $output_lines[] = 'msgctxt ' . self::quotePoString($entry['msgctxt']);
            }

            $output_lines[] = 'msgid ' . self::quotePoString($entry['msgid']);

            if ($entry['msgid_plural'] !== null) {
                $output_lines[] = 'msgid_plural ' . self::quotePoString($entry['msgid_plural']);
            }

            if ($entry['msgid_plural'] === null) {
                $output_lines[] = 'msgstr ' . self::quotePoString($entry['msgstr'][0] ?? '');
            } else {
                $msgstr_values = $entry['msgstr'];

                if ($msgstr_values === []) {
                    $msgstr_values = [0 => '', 1 => ''];
                }

                ksort($msgstr_values);

                foreach ($msgstr_values as $index => $value) {
                    $output_lines[] = 'msgstr[' . $index . '] ' . self::quotePoString($value);
                }
            }

            $output_lines[] = '';
        };

        $write_entry($header);

        foreach ($entries as $entry) {
            $write_entry($entry);
        }

        return implode("\n", $output_lines);
    }

    private static function quotePoString(string $value): string
    {
        return '"' . addcslashes($value, "\\\"\n\r\t") . '"';
    }


    /**
     * @return array<string,string>|null
     */
    private function compilePoFile(string $po_file, string $php_file): array|null
    {
        $stream       = fopen($po_file, 'rb');
        $translation  = Translation::fromPoStream($stream);
        fclose($stream);
        $translations = $translation->toArray();


        $php_code = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        $written  = file_put_contents($php_file, $php_code);

        if ($written === false) {
            return null;
        }

        /** @var array<string,string> $translations */
        return $translations;
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

    /**
     * @return array<int,array{source:string,translated:string}>
     */
    private function validatePoPlaceholders(string $po_file): array
    {
        $content = file_get_contents($po_file);

        if ($content === false) {
            return [[
                'source' => '[unable to read PO file]',
                'translated' => $po_file,
            ]];
        }

        $issues          = [];
        $lines           = explode("\n", $content);
        $current_msgid   = null;
        $current_plural  = null;
        $current_msgstrs = [];
        $active_field    = null;

        foreach ($lines as $line) {
            if ($line === '') {
                $issues = $this->appendPoPlaceholderIssues($issues, $current_msgid, $current_plural, $current_msgstrs);
                $current_msgid   = null;
                $current_plural  = null;
                $current_msgstrs = [];
                $active_field    = null;

                continue;
            }

            if (str_starts_with($line, '#')) {
                continue;
            }

            if (preg_match('/^msgid "(.*)"$/', $line, $matches) === 1) {
                $current_msgid = stripcslashes($matches[1]);
                $active_field  = 'msgid';

                continue;
            }

            if (preg_match('/^msgid_plural "(.*)"$/', $line, $matches) === 1) {
                $current_plural = stripcslashes($matches[1]);
                $active_field   = 'msgid_plural';

                continue;
            }

            if (preg_match('/^msgstr "(.*)"$/', $line, $matches) === 1) {
                $current_msgstrs[0] = stripcslashes($matches[1]);
                $active_field       = 'msgstr:0';

                continue;
            }

            if (preg_match('/^msgstr\[(\d+)] "(.*)"$/', $line, $matches) === 1) {
                $msgstr_index                  = (int) $matches[1];
                $current_msgstrs[$msgstr_index] = stripcslashes($matches[2]);
                $active_field                  = 'msgstr:' . $msgstr_index;

                continue;
            }

            if (preg_match('/^"(.*)"$/', $line, $matches) !== 1 || $active_field === null) {
                continue;
            }

            $continuation = stripcslashes($matches[1]);

            if ($active_field === 'msgid') {
                $current_msgid .= $continuation;

                continue;
            }

            if ($active_field === 'msgid_plural') {
                $current_plural .= $continuation;

                continue;
            }

            if (str_starts_with($active_field, 'msgstr:')) {
                $msgstr_index = (int) substr($active_field, 7);
                $existing     = $current_msgstrs[$msgstr_index] ?? '';
                $current_msgstrs[$msgstr_index] = $existing . $continuation;
            }
        }

        $issues = $this->appendPoPlaceholderIssues($issues, $current_msgid, $current_plural, $current_msgstrs);

        return $issues;
    }

    /**
     * @param array<int,array{source:string,translated:string}> $issues
     * @param array<int,string>                                  $current_msgstrs
     *
     * @return array<int,array{source:string,translated:string}>
     */
    private function appendPoPlaceholderIssues(array $issues, string|null $current_msgid, string|null $current_plural, array $current_msgstrs): array
    {
        if ($current_msgid === null || $current_msgid === '' || $current_msgstrs === []) {
            return $issues;
        }

        $expected_singular = $this->countPlaceholders($current_msgid);
        $expected_plural   = $current_plural === null ? $expected_singular : $this->countPlaceholders($current_plural);

        foreach ($current_msgstrs as $index => $translated_value) {
            if ($translated_value === '') {
                continue;
            }

            $source_value = $index === 0 || $current_plural === null ? $current_msgid : $current_plural;

            if ($this->isPlaceholderCheckException($source_value)) {
                continue;
            }

            $expected_placeholders = $index === 0 ? $expected_singular : $expected_plural;
            $actual_placeholders   = $this->countPlaceholders($translated_value);

            if ($expected_placeholders !== $actual_placeholders) {
                $issues[] = [
                    'source' => $source_value,
                    'translated' => $translated_value,
                ];
            }
        }

        return $issues;
    }

    private function formatDisplayString(string $value): string
    {
        return var_export($value, true);
    }

    private function isPlaceholderCheckException(string $source_value): bool
    {
        return $source_value === self::DATE_FORMAT || $source_value === self::TIME_FORMAT;
    }

    private function countPlaceholders(string $value): int
    {
        $value_without_escaped_percent = str_replace('%%', '', $value);

        preg_match_all('/%(?:\d+\$)?[+-]?(?:\d+)?(?:\.\d+)?[bcdeEufFgGosxX]/', $value_without_escaped_percent, $matches);

        return count($matches[0]);
    }

    /**
     * @return array{translated:int,total:int}
     */
    private function countPoTranslationStatus(string $po_file): array
    {
        $output_lines = [];
        $exit_code    = 1;

        exec('msgfmt --statistics -o /dev/null ' . escapeshellarg($po_file) . ' 2>&1', $output_lines, $exit_code);

        if ($exit_code !== 0) {
            return ['translated' => 0, 'total' => 0];
        }

        $statistics      = implode(' ', $output_lines);
        $translated      = 0;
        $fuzzy           = 0;
        $untranslated    = 0;

        if (preg_match('/(\d+) translated message/', $statistics, $matches) === 1) {
            $translated = (int) $matches[1];
        }

        if (preg_match('/(\d+) fuzzy translation/', $statistics, $matches) === 1) {
            $fuzzy = (int) $matches[1];
        }

        if (preg_match('/(\d+) untranslated message/', $statistics, $matches) === 1) {
            $untranslated = (int) $matches[1];
        }

        return [
            'translated' => $translated,
            'total' => $translated + $fuzzy + $untranslated,
        ];
    }

    /**
     * @param array<int,string>    $message_ids
     * @param array<string,string> $translations
     *
     * @return array{translated:int,total:int}
     */
    private function countJavaScriptTranslationStatus(array $message_ids, array $translations): array
    {
        $translated = 0;

        foreach ($message_ids as $message_id) {
            if (isset($translations[$message_id]) && $translations[$message_id] !== '') {
                $translated++;
            }
        }

        return [
            'translated' => $translated,
            'total' => count($message_ids),
        ];
    }

    private function formatStatus(int $translated, int $total): string
    {
        return $this->formatNumber($translated) . '/' . $this->formatNumber($total);
    }

    private function countMessages(string $pot_filename): int
    {
        $pot_content = file_get_contents($pot_filename);

        if ($pot_content === false) {
            return 0;
        }

        $message_count = 0;

        foreach (explode("\n", $pot_content) as $line) {
            if (str_starts_with($line, 'msgid ')) {
                $message_count++;
            }
        }

        return max(0, $message_count - 1);
    }

    private function formatNumber(int $value): string
    {
        return number_format($value, 0, '.', ',');
    }

    /**
     * @param array<int,string> $files
     */
    private function xgettextPhpCommand(string $output_file, array $files): string
    {
        return $this->xgettextCommand([
            '--package-name=webtrees',
            '--package-version=1.0',
            '--output=' . $output_file,
            '--no-wrap',
            '--add-location=file',
            '--language=PHP',
            '--add-comments=I18N',
            '--from-code=utf-8',
            '--keyword',
            '--keyword=translate:1',
            '--keyword=translateContext:1c,2',
            '--keyword=plural:1,2',
        ], $files);
    }

    /**
     * @param array<int,string> $files
     */
    private function xgettextJavaScriptCommand(string $output_file, array $files): string
    {
        return $this->xgettextCommand([
            '--package-name=webtrees',
            '--package-version=1.0',
            '--output=' . $output_file,
            '--no-wrap',
            '--add-location=file',
            '--language=JavaScript',
            '--from-code=utf-8',
            '--keyword=i18n.gettext:1',
            '--keyword=webtrees.i18n.gettext:1',
        ], $files);
    }

    /**
     * @param array<int,string> $arguments
     * @param array<int,string> $files
     */
    private function xgettextCommand(array $arguments, array $files): string
    {
        $escaped_arguments = array_map(static fn (string $argument): string => escapeshellarg($argument), $arguments);
        $escaped_files     = array_map(static fn (string $file): string => escapeshellarg($file), $files);

        return 'xgettext ' . implode(' ', $escaped_arguments) . ' ' . implode(' ', $escaped_files);
    }

    /**
     * @param string            $extension
     * @param string            $search_directory
     *
     * @return array<int,string>
     */
    private function collectFiles(string $root_directory, string $search_directory, string $extension): array
    {
        $directory = rtrim($root_directory, DIRECTORY_SEPARATOR);

        if ($search_directory !== '') {
            $directory .= DIRECTORY_SEPARATOR . ltrim($search_directory, DIRECTORY_SEPARATOR);
        }

        if (!is_dir($directory)) {
            return [];
        }

        $files    = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        /** @var SplFileInfo $file_info */
        foreach ($iterator as $file_info) {
            if (!$file_info->isFile()) {
                continue;
            }

            $pathname = $file_info->getPathname();

            if (!str_ends_with($pathname, $extension)) {
                continue;
            }

            $files[] = ltrim(str_replace($root_directory, '', $pathname), DIRECTORY_SEPARATOR);
        }

        sort($files);

        return $files;
    }



    /**
     * @param array<int,string> $xml_files
     */
    private function prepareXmlTemplateDirectory(array $xml_files): string
    {
        $temporary_directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'webtrees-xgettext-' . uniqid('', true);

        mkdir($temporary_directory, 0777, true);

        foreach ($xml_files as $xml_file) {
            $target_file = $temporary_directory . DIRECTORY_SEPARATOR . $xml_file;
            $target_path = dirname($target_file);

            if (!is_dir($target_path)) {
                mkdir($target_path, 0777, true);
            }

            copy(self::ROOT_DIRECTORY . $xml_file, $target_file);

            $content = file_get_contents($target_file);

            if ($content === false) {
                continue;
            }

            $transformed = preg_replace('/(I18N::[^)]*[)])/', '<?php echo $1; ?>', $content);

            if (!is_string($transformed)) {
                continue;
            }

            file_put_contents($target_file, $transformed);
        }

        return $temporary_directory;
    }

    private function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($directory);
    }

    private function runCommand(string $command, string $working_directory): bool
    {
        $output_lines = [];
        $exit_code    = 1;

        exec('cd ' . escapeshellarg($working_directory) . ' && ' . $command, $output_lines, $exit_code);

        return $exit_code === 0;
    }
}
