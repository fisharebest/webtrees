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

use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\I18N\Translation;
use Fisharebest\Webtrees\I18N\Translator;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Translator::class)]
class TranslatorTest extends TestCase
{
    public function testTranslateReturnsTranslatedMessage(): void
    {
        $translator = new Translator(
            ['Hello' => 'Bonjour'],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('Bonjour', $translator->translate('Hello'));
    }

    public function testTranslateReturnsFallbackWhenMissing(): void
    {
        $translator = new Translator(
            ['Hello' => 'Bonjour'],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('Goodbye', $translator->translate('Goodbye'));
    }

    public function testTranslateContextReturnsContextualTranslation(): void
    {
        $key = 'greeting' . Translation::CONTEXT_SEPARATOR . 'Hello';

        $translator = new Translator(
            [$key => 'Salut'],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('Salut', $translator->translateContext('greeting', 'Hello'));
    }

    public function testTranslateContextReturnsFallbackWhenMissing(): void
    {
        $translator = new Translator(
            [],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('Hello', $translator->translateContext('greeting', 'Hello'));
    }

    public function testTranslatePluralSingularEnglishRule(): void
    {
        // English: plural=n!=1
        $key = '%s record' . Translation::PLURAL_SEPARATOR . '%s records';

        $translator = new Translator(
            [$key => '%s enregistrement' . Translation::PLURAL_SEPARATOR . '%s enregistrements'],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('%s enregistrement', $translator->translatePlural('%s record', '%s records', 1));
    }

    public function testTranslatePluralPluralEnglishRule(): void
    {
        $key = '%s record' . Translation::PLURAL_SEPARATOR . '%s records';

        $translator = new Translator(
            [$key => '%s enregistrement' . Translation::PLURAL_SEPARATOR . '%s enregistrements'],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('%s enregistrements', $translator->translatePlural('%s record', '%s records', 5));
    }

    public function testTranslatePluralZeroEnglishRule(): void
    {
        // In English rule, 0 is plural (n != 1)
        $key = '%s record' . Translation::PLURAL_SEPARATOR . '%s records';

        $translator = new Translator(
            [$key => '%s enregistrement' . Translation::PLURAL_SEPARATOR . '%s enregistrements'],
            PluralRule::TwoFormsSingularForOne,
        );

        self::assertSame('%s enregistrements', $translator->translatePlural('%s record', '%s records', 0));
    }

    public function testTranslatePluralFrenchRule(): void
    {
        // French: plural=n>1 (0 and 1 are singular)
        $key = '%s record' . Translation::PLURAL_SEPARATOR . '%s records';

        $translator = new Translator(
            [$key => '%s enregistrement' . Translation::PLURAL_SEPARATOR . '%s enregistrements'],
            PluralRule::TwoFormsPluralForMoreThanOne,
        );

        // 0 is singular in French
        self::assertSame('%s enregistrement', $translator->translatePlural('%s record', '%s records', 0));
        // 1 is singular in French
        self::assertSame('%s enregistrement', $translator->translatePlural('%s record', '%s records', 1));
        // 2 is plural in French
        self::assertSame('%s enregistrements', $translator->translatePlural('%s record', '%s records', 2));
    }

    public function testTranslatePluralOneFormRule(): void
    {
        // Languages like Chinese: one form for all numbers
        $key = '%s record' . Translation::PLURAL_SEPARATOR . '%s records';

        $translator = new Translator(
            [$key => '%s 条记录'],
            PluralRule::OneForm,
        );

        self::assertSame('%s 条记录', $translator->translatePlural('%s record', '%s records', 0));
        self::assertSame('%s 条记录', $translator->translatePlural('%s record', '%s records', 1));
        self::assertSame('%s 条记录', $translator->translatePlural('%s record', '%s records', 5));
    }

    public function testTranslatePluralFallbackWhenMissing(): void
    {
        $translator = new Translator(
            [],
            PluralRule::TwoFormsSingularForOne,
        );

        // Falls back to original English singular/plural
        self::assertSame('%s record', $translator->translatePlural('%s record', '%s records', 1));
        self::assertSame('%s records', $translator->translatePlural('%s record', '%s records', 5));
    }
}
