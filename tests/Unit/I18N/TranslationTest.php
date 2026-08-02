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

namespace Fisharebest\Webtrees\Tests\Unit\I18N;

use Fisharebest\Webtrees\I18N\Translation;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;

use function explode;
use function fclose;
use function fopen;
use function fwrite;
use function rewind;

#[CoversClass(Translation::class)]
class TranslationTest extends TestCase
{
    private const string DATA_DIR = __DIR__ . '/../../data/';

    public function testFromPoStreamSimpleTranslation(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertSame('Bonjour', $translation->toArray()['Hello']);
    }

    public function testFromPoStreamStripsComments(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertArrayNotHasKey('# Simple translation', $translation->toArray());
    }

    public function testFromPoStreamEscapeCharacters(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertSame("Ligne un\nLigne deux", $translation->toArray()["Line one\nLine two"]);
    }

    public function testFromPoStreamQuotedStrings(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertSame('Elle a dit "bonjour"', $translation->toArray()['She said "hello"']);
    }

    public function testFromPoStreamContext(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        $key = 'greeting' . Translation::CONTEXT_SEPARATOR . 'Hello';
        self::assertSame('Salut', $translation->toArray()[$key]);
    }

    public function testFromPoStreamPlurals(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        $key     = '%s record' . Translation::PLURAL_SEPARATOR . '%s records';
        $plurals = explode(Translation::PLURAL_SEPARATOR, $translation->toArray()[$key]);

        self::assertSame('%s enregistrement', $plurals[0]);
        self::assertSame('%s enregistrements', $plurals[1]);
    }

    public function testFromPoStreamMultiLineStrings(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertSame('Ceci est un long message', $translation->toArray()['This is a long message']);
    }

    public function testFromPoStreamSkipsEmptyTranslations(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.po', 'rb');
        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertArrayNotHasKey('Untranslated', $translation->toArray());
    }

    public function testFromPoStreamWithInMemoryStream(): void
    {
        $po_content = "msgid \"Yes\"\nmsgstr \"Oui\"\n";
        $stream     = fopen('php://memory', 'r+b');
        fwrite($stream, $po_content);
        rewind($stream);

        $translation = Translation::fromPoStream($stream);
        fclose($stream);

        self::assertSame('Oui', $translation->toArray()['Yes']);
    }

    public function testFromCsvStream(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.csv', 'rb');
        $translation = Translation::fromCsvStream($stream);
        fclose($stream);

        self::assertSame('Bonjour', $translation->toArray()['Hello']);
        self::assertSame('Au revoir', $translation->toArray()['Goodbye']);
        self::assertSame('Merci', $translation->toArray()['Thank you']);
    }

    public function testFromCsvStreamWithInMemoryStream(): void
    {
        $csv_content = "Hello;Hola\nGoodbye;Adios\n";
        $stream      = fopen('php://memory', 'r+b');
        fwrite($stream, $csv_content);
        rewind($stream);

        $translation = Translation::fromCsvStream($stream);
        fclose($stream);

        self::assertSame('Hola', $translation->toArray()['Hello']);
        self::assertSame('Adios', $translation->toArray()['Goodbye']);
    }

    public function testFromPhpFile(): void
    {
        $translation = Translation::fromPhpFile(self::DATA_DIR . 'test-translations.php');

        self::assertSame('Bonjour', $translation->toArray()['Hello']);
        self::assertSame('Au revoir', $translation->toArray()['Goodbye']);
        self::assertSame('Oui', $translation->toArray()['Yes']);
        self::assertSame('Non', $translation->toArray()['No']);
    }

    public function testFromPhpFileThrowsExceptionForInvalidFile(): void
    {
        $this->expectException(RuntimeException::class);

        Translation::fromPhpFile(self::DATA_DIR . 'test-translations-invalid.php');
    }

    public function testFromMoStream(): void
    {
        $stream = fopen(self::DATA_DIR . 'test-translations.mo', 'rb');
        $translation = Translation::fromMoStream($stream);
        fclose($stream);

        self::assertSame('Bonjour', $translation->toArray()['Hello']);
        self::assertSame('Au revoir', $translation->toArray()['Goodbye']);
    }

    public function testToArrayReturnsAllTranslations(): void
    {
        $translation = Translation::fromPhpFile(self::DATA_DIR . 'test-translations.php');

        self::assertCount(4, $translation->toArray());
    }

    public function testWithMessagesAddsNewTranslations(): void
    {
        $translation = Translation::fromPhpFile(self::DATA_DIR . 'test-translations.php');
        $merged      = $translation->withMessages(['Please' => "S'il vous plait"]);

        self::assertSame("S'il vous plait", $merged->toArray()['Please']);
        self::assertSame('Bonjour', $merged->toArray()['Hello']);
    }

    public function testWithMessagesOverwritesExistingTranslations(): void
    {
        $translation = Translation::fromPhpFile(self::DATA_DIR . 'test-translations.php');
        $merged      = $translation->withMessages(['Hello' => 'Salut']);

        self::assertSame('Salut', $merged->toArray()['Hello']);
    }

    public function testWithMessagesDoesNotMutateOriginal(): void
    {
        $translation = Translation::fromPhpFile(self::DATA_DIR . 'test-translations.php');
        $translation->withMessages(['Hello' => 'Salut']);

        self::assertSame('Bonjour', $translation->toArray()['Hello']);
    }
}
