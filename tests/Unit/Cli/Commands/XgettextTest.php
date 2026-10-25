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

use Fisharebest\Webtrees\Cli\Commands\Xgettext;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

use function file_put_contents;
use function str_contains;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

#[CoversClass(Xgettext::class)]
class XgettextTest extends TestCase
{
    public function testExtractMessageIdsFromPotIgnoresHeaderAndKeepsUniqueIds(): void
    {
        $command = new Xgettext();

        $pot_contents = <<<'POT'
msgid ""
msgstr ""

#: resources/js/webtrees/confirm.js:35
msgid "previous"
msgstr ""

#: resources/js/webtrees/gallery.js:468
msgid "next"
msgstr ""

#: resources/js/webtrees/gallery.js:469
msgid "next"
msgstr ""
POT;

        self::assertSame(['previous', 'next'], $this->invokePrivateMethod($command, 'extractJavaScriptMessageIdsFromPot', $pot_contents));
    }

    public function testBuildCatalogUsesTranslationsAndFallsBackToEnglishIds(): void
    {
        $command = new Xgettext();

        $catalog = $this->invokePrivateMethod(
            $command,
            'buildJavaScriptCatalog',
            ['Cancel', 'next', 'Image'],
            ['Cancel' => 'Annuler', 'next' => 'suivant'],
        );

        self::assertSame([
            'Cancel' => 'Annuler',
            'next' => 'suivant',
            'Image' => 'Image',
        ], $catalog);
    }

    public function testBuildCatalogScriptCreatesWindowBootstrapSnippet(): void
    {
        $command = new Xgettext();

        $script = $this->invokePrivateMethod($command, 'buildJavaScriptCatalogScript', [
            'Cancel' => 'Cancel',
            'OK' => 'OK',
        ]);
        $catalog_prefix = 'window.webtrees.i18nCatalog = Object.assign(window.webtrees.i18nCatalog || {}, ';

        self::assertStringContainsString('window.webtrees = window.webtrees || {};', $script);
        self::assertStringContainsString($catalog_prefix, $script);
        self::assertStringContainsString('{"Cancel":"Cancel","OK":"OK"}', $script);
    }

    public function testValidatePoPlaceholdersDetectsMismatches(): void
    {
        $command         = new Xgettext();
        $temporary_file  = sys_get_temp_dir() . '/webtrees-po-' . uniqid('', true) . '.po';
        $po_content      = <<<'PO'
msgid ""
msgstr ""

msgid "One %s"
msgstr "Uno"

msgid "Value %d"
msgstr "Valor %d"

msgid "Plural %s"
msgid_plural "Plurales %s"
msgstr[0] "Plural %s"
msgstr[1] "Plurales"
PO;

        file_put_contents($temporary_file, $po_content . "\n");

        try {
            $issues = $this->invokePrivateMethod($command, 'validatePoPlaceholders', $temporary_file);
        } finally {
            unlink($temporary_file);
        }

        self::assertSame([
            ['source' => 'One %s', 'translated' => 'Uno'],
            ['source' => 'Plurales %s', 'translated' => 'Plurales'],
        ], $issues);
    }

    public function testValidatePoPlaceholdersIgnoresUntranslatedEntries(): void
    {
        $command        = new Xgettext();
        $temporary_file = sys_get_temp_dir() . '/webtrees-po-' . uniqid('', true) . '.po';
        $po_content     = <<<'PO'
msgid ""
msgstr ""

msgid "One %s"
msgstr ""

msgid "Plural %s"
msgid_plural "Plurales %s"
msgstr[0] ""
msgstr[1] ""
PO;

        file_put_contents($temporary_file, $po_content . "\n");

        try {
            $issues = $this->invokePrivateMethod($command, 'validatePoPlaceholders', $temporary_file);
        } finally {
            unlink($temporary_file);
        }

        self::assertSame([], $issues);
    }

    public function testValidatePoPlaceholdersIgnoresDateFormatExceptions(): void
    {
        $command        = new Xgettext();
        $temporary_file = sys_get_temp_dir() . '/webtrees-po-' . uniqid('', true) . '.po';
        $po_content     = <<<'PO'
msgid ""
msgstr ""

msgid "%H:%i:%s"
msgstr "%H:%i"

msgid "%j %F %Y"
msgstr "%j %Y"
PO;

        file_put_contents($temporary_file, $po_content . "\n");

        try {
            $issues = $this->invokePrivateMethod($command, 'validatePoPlaceholders', $temporary_file);
        } finally {
            unlink($temporary_file);
        }

        self::assertSame([], $issues);
    }

    public function testMergePotContentsMergesDuplicateEntriesWithoutConflictMarkers(): void
    {
        $command         = new Xgettext();
        $temporary_file1 = sys_get_temp_dir() . '/webtrees-pot-' . uniqid('', true) . '-1.pot';
        $temporary_file2 = sys_get_temp_dir() . '/webtrees-pot-' . uniqid('', true) . '-2.pot';

        $pot_file_1 = <<<'POT'
msgid ""
msgstr ""
"Content-Type: text/plain; charset=UTF-8\n"

#. I18N: from php
#: app/Foo.php:10
msgid "Cancel"
msgstr ""
POT;

        $pot_file_2 = <<<'POT'
msgid ""
msgstr ""
"Content-Type: text/plain; charset=UTF-8\n"

#. I18N: from xml
#: resources/xml/reports/bar.xml:5
msgid "Cancel"
msgstr ""
POT;

        file_put_contents($temporary_file1, $pot_file_1 . "\n");
        file_put_contents($temporary_file2, $pot_file_2 . "\n");

        try {
            $merged_content = $this->invokePrivateMethod($command, 'mergePotContents', [$temporary_file1, $temporary_file2]);
        } finally {
            unlink($temporary_file1);
            unlink($temporary_file2);
        }

        self::assertIsString($merged_content);
        self::assertFalse(str_contains($merged_content, '#-#-#-#-#'));
        self::assertTrue(str_contains($merged_content, '#. I18N: from php'));
        self::assertTrue(str_contains($merged_content, '#. I18N: from xml'));
        self::assertTrue(str_contains($merged_content, '#: app/Foo.php:10 resources/xml/reports/bar.xml:5'));
    }

    private function invokePrivateMethod(Xgettext $command, string $method_name, mixed ...$arguments): mixed
    {
        return (new ReflectionMethod($command, $method_name))->invokeArgs($command, $arguments);
    }
}
