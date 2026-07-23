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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function addcslashes;
use function array_map;
use function array_values;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function in_array;
use function is_file;
use function is_string;
use function preg_match;
use function preg_match_all;
use function sort;
use function str_replace;
use function str_starts_with;
use function stripcslashes;
use function substr;

/**
 * Write a translation entry to a PO file.
 *
 * Designed for use by AI assistants to add translations to language files.
 * All translations are marked as fuzzy, so that a native speaker can review them.
 */
final class WriteTranslation extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('ai:write-translation')
            ->setDescription('Write a fuzzy translation to a PO file, for review by a native speaker')
            ->addArgument('po-file', InputArgument::REQUIRED, 'The path to the PO file (e.g. "resources/lang/de/messages.po")')
            ->addArgument('msgid', InputArgument::REQUIRED, 'The English source string (msgid)')
            ->addArgument('translations', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One translation for simple entries, or multiple plural forms (msgstr[0], msgstr[1], ...)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite an existing non-empty translation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $po_file      = $this->stringArgument($input, 'po-file');
        $msgid        = $this->stringArgument($input, 'msgid');
        $translations = array_values(array_map(
            static fn (mixed $value): string => is_string($value) ? $value : '',
            (array) $input->getArgument('translations'),
        ));
        $force = $this->boolOption($input, 'force');

        if (!is_file($po_file)) {
            $io->error('PO file does not exist: ' . $po_file);

            return self::FAILURE;
        }

        $content = file_get_contents($po_file);

        if ($content === false) {
            $io->error('Unable to read PO file: ' . $po_file);

            return self::FAILURE;
        }

        // Find the entry in the PO file
        $entry = $this->findEntry($content, $msgid);

        if ($entry === null) {
            $io->error('The msgid was not found in the PO file: ' . $msgid);

            return self::FAILURE;
        }

        $is_plural         = $entry['msgid_plural'] !== null;
        $translation_count = count($translations);

        // Validate that the number of translations matches the entry type
        if (!$is_plural && $translation_count !== 1) {
            $io->error('This is not a plural entry. Provide exactly one translation.');

            return self::FAILURE;
        }

        if ($is_plural) {
            $nplurals = $this->extractNplurals($content);

            if ($nplurals === null) {
                $io->error('Unable to determine the number of plural forms from the PO file header.');

                return self::FAILURE;
            }

            if ($translation_count !== $nplurals) {
                $io->error('This language requires ' . $nplurals . ' plural forms, but ' . $translation_count . ' were provided.');

                return self::FAILURE;
            }
        }

        // Check whether the translation already matches what we are trying to set
        $existing     = $entry['msgstr'];
        $is_identical = true;
        $has_existing = false;

        for ($index = 0; $index < $translation_count; $index++) {
            $existing_value = $existing[$index] ?? '';

            if ($existing_value !== '') {
                $has_existing = true;
            }

            if ($existing_value !== $translations[$index]) {
                $is_identical = false;
            }
        }

        if ($is_identical) {
            $io->info('The translation is already set to this value.');

            return self::SUCCESS;
        }

        // Refuse to overwrite an existing translation unless --force is used
        if (!$force && $has_existing) {
            $io->warning('A different translation already exists for this entry.');

            if ($is_plural) {
                foreach ($existing as $index => $value) {
                    $io->writeln('  Existing msgstr[' . $index . ']: ' . ($value === '' ? '(empty)' : $value));
                }
            } else {
                $io->writeln('  Existing: ' . ($existing[0] ?? ''));
            }

            $io->writeln('');
            $io->writeln('Use the --force option to overwrite.');

            return self::FAILURE;
        }

        // Validate placeholders in each translation form against the source string
        $placeholder_error = $this->validatePlaceholders($msgid, $translations[0]);

        if ($placeholder_error !== null) {
            $io->error('msgstr[0]: ' . $placeholder_error);

            return self::FAILURE;
        }

        if ($entry['msgid_plural'] !== null) {
            for ($index = 1; $index < $translation_count; $index++) {
                $placeholder_error = $this->validatePlaceholders($entry['msgid_plural'], $translations[$index]);

                if ($placeholder_error !== null) {
                    $io->error('msgstr[' . $index . ']: ' . $placeholder_error);

                    return self::FAILURE;
                }
            }
        }

        // Build the replacement entry
        $new_entry = $this->buildEntry($entry, $translations);
        $content   = str_replace($entry['raw'], $new_entry, $content);
        $written   = file_put_contents($po_file, $content);

        if ($written === false) {
            $io->error('Unable to write PO file: ' . $po_file);

            return self::FAILURE;
        }

        $io->success('Translation written to ' . $po_file);

        return self::SUCCESS;
    }

    /**
     * Find a PO entry by its msgid and return its raw text and parsed structure.
     *
     * @return array{raw:string,msgid_plural:string|null,flags:list<string>,has_fuzzy:bool,msgstr:array<int,string>}|null
     */
    private function findEntry(string $content, string $msgid): array|null
    {
        $escaped_msgid = addcslashes($msgid, "\\\"\n\r\t");
        $lines         = explode("\n", str_replace("\r\n", "\n", $content));
        $entry_lines   = [];
        $in_entry      = false;
        $found_msgid   = false;
        $msgid_plural  = null;
        $flags         = [];
        $msgstr        = [];
        $active_field  = null;

        foreach ($lines as $line) {
            if ($line === '') {
                if ($in_entry && $found_msgid) {
                    return [
                        'raw'          => implode("\n", $entry_lines),
                        'msgid_plural' => $msgid_plural,
                        'flags'        => $flags,
                        'has_fuzzy'    => in_array('fuzzy', $flags, true),
                        'msgstr'       => $msgstr,
                    ];
                }

                $entry_lines  = [];
                $in_entry     = false;
                $found_msgid  = false;
                $msgid_plural = null;
                $flags        = [];
                $msgstr       = [];
                $active_field = null;

                continue;
            }

            if (!$in_entry) {
                $in_entry = true;
            }

            $entry_lines[] = $line;

            if ($line === 'msgid "' . $escaped_msgid . '"') {
                $found_msgid = true;
            }

            if (preg_match('/^#, (.+)$/', $line, $matches) === 1) {
                $flags = array_map(trim(...), explode(',', $matches[1]));
            }

            if (preg_match('/^msgid_plural "(.+)"$/', $line, $matches) === 1) {
                $msgid_plural = stripcslashes($matches[1]);
            }

            if (preg_match('/^msgstr "(.*)"\s*$/', $line, $matches) === 1) {
                $msgstr[0]    = stripcslashes($matches[1]);
                $active_field = 'msgstr:0';
            } elseif (preg_match('/^msgstr\[(\d+)] "(.*)"\s*$/', $line, $matches) === 1) {
                $index          = (int) $matches[1];
                $msgstr[$index] = stripcslashes($matches[2]);
                $active_field   = 'msgstr:' . $index;
            } elseif (preg_match('/^"(.*)"\s*$/', $line, $matches) === 1 && $active_field !== null && str_starts_with($active_field, 'msgstr:')) {
                $index          = (int) substr($active_field, 7);
                $msgstr[$index] = ($msgstr[$index] ?? '') . stripcslashes($matches[1]);
            } elseif (str_starts_with($line, 'msgid ') || str_starts_with($line, 'msgid_plural ') || str_starts_with($line, 'msgctxt ') || str_starts_with($line, '#')) {
                $active_field = null;
            }
        }

        // Handle entry at end of file without trailing blank line
        if ($in_entry && $found_msgid) {
            return [
                'raw'          => implode("\n", $entry_lines),
                'msgid_plural' => $msgid_plural,
                'flags'        => $flags,
                'has_fuzzy'    => in_array('fuzzy', $flags, true),
                'msgstr'       => $msgstr,
            ];
        }

        return null;
    }

    /**
     * Extract the number of plural forms from the PO file header.
     */
    private function extractNplurals(string $content): int|null
    {
        if (preg_match('/Plural-Forms:\s*nplurals\s*=\s*(\d+)\s*;/', $content, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Validate that the translation has the same placeholders as the source string.
     */
    private function validatePlaceholders(string $source, string $translation): string|null
    {
        $source_placeholders      = $this->extractPlaceholders($source);
        $translation_placeholders = $this->extractPlaceholders($translation);

        if ($source_placeholders !== $translation_placeholders) {
            return 'Placeholder mismatch. Source has: ' . ($source_placeholders === [] ? '(none)' : implode(', ', $source_placeholders))
                . '. Translation has: ' . ($translation_placeholders === [] ? '(none)' : implode(', ', $translation_placeholders)) . '.';
        }

        return null;
    }

    /**
     * Extract printf-style placeholders from a string.
     *
     * @return list<string>
     */
    private function extractPlaceholders(string $value): array
    {
        $value = str_replace('%%', '', $value);

        preg_match_all('/%(?:\d+\$)?[+-]?(?:\d+)?(?:\.\d+)?[bcdeEufFgGosxX]/', $value, $matches);

        $placeholders = $matches[0];
        sort($placeholders);

        return $placeholders;
    }

    /**
     * Build a replacement PO entry with the fuzzy flag and the new translation.
     *
     * @param array{raw:string,msgid_plural:string|null,flags:list<string>,has_fuzzy:bool,msgstr:array<int,string>} $entry
     * @param list<string> $translations
     */
    private function buildEntry(array $entry, array $translations): string
    {
        $lines     = explode("\n", $entry['raw']);
        $new_lines = [];
        $flags     = $entry['flags'];

        // Add fuzzy flag if not already present
        if (!$entry['has_fuzzy']) {
            $flags[] = 'fuzzy';
        }

        $wrote_flags  = false;
        $skip_msgstr  = false;

        foreach ($lines as $line) {
            // Skip existing flag lines; we will write our own
            if (preg_match('/^#, /', $line) === 1) {
                continue;
            }

            // Insert flags just before the first msgctxt or msgid line
            if (!$wrote_flags && (str_starts_with($line, 'msgctxt ') || str_starts_with($line, 'msgid '))) {
                $new_lines[] = '#, ' . implode(', ', $flags);
                $wrote_flags = true;
            }

            // Replace msgstr lines with new translations
            if (preg_match('/^msgstr(?:\[\d+])? "/', $line) === 1) {
                if (!$skip_msgstr) {
                    $skip_msgstr = true;

                    if ($entry['msgid_plural'] === null) {
                        $new_lines[] = 'msgstr ' . self::quotePoString($translations[0]);
                    } else {
                        foreach ($translations as $index => $translation) {
                            $new_lines[] = 'msgstr[' . $index . '] ' . self::quotePoString($translation);
                        }
                    }
                }

                continue;
            }

            // Skip continuation lines of msgstr
            if ($skip_msgstr && preg_match('/^"/', $line) === 1) {
                continue;
            }

            $new_lines[] = $line;
        }

        return implode("\n", $new_lines);
    }

    private static function quotePoString(string $value): string
    {
        return '"' . addcslashes($value, "\\\"\n\r\t") . '"';
    }
}
