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

namespace Fisharebest\Webtrees\Tests\Unit\Enums;

use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\TextDirection;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Script::class)]
class ScriptTest extends TestCase
{
    public function testFromTextLatin(): void
    {
        self::assertSame(Script::Latn, Script::fromText('Hello world'));
    }

    public function testFromTextArabic(): void
    {
        self::assertSame(Script::Arab, Script::fromText('مرحبا'));
    }

    public function testFromTextCyrillic(): void
    {
        self::assertSame(Script::Cyrl, Script::fromText('Привет'));
    }

    public function testFromTextGreek(): void
    {
        self::assertSame(Script::Grek, Script::fromText('Γεια'));
    }

    public function testFromTextHebrew(): void
    {
        self::assertSame(Script::Hebr, Script::fromText('שלום'));
    }

    public function testFromTextDevanagari(): void
    {
        self::assertSame(Script::Deva, Script::fromText('नमस्ते'));
    }

    public function testFromTextThai(): void
    {
        self::assertSame(Script::Thai, Script::fromText('สวัสดี'));
    }

    public function testFromTextJapanese(): void
    {
        self::assertSame(Script::Jpan, Script::fromText('こんにちは'));
    }

    public function testFromTextKorean(): void
    {
        self::assertSame(Script::Kore, Script::fromText('안녕하세요'));
    }

    public function testFromTextFallsBackToLatin(): void
    {
        self::assertSame(Script::Latn, Script::fromText('123'));
    }

    public function testTextDirectionRtlScripts(): void
    {
        self::assertSame(TextDirection::RTL, Script::Arab->textDirection());
        self::assertSame(TextDirection::RTL, Script::Hebr->textDirection());
        self::assertSame(TextDirection::RTL, Script::Thaa->textDirection());
    }

    public function testTextDirectionLtrScripts(): void
    {
        self::assertSame(TextDirection::LTR, Script::Latn->textDirection());
        self::assertSame(TextDirection::LTR, Script::Cyrl->textDirection());
        self::assertSame(TextDirection::LTR, Script::Grek->textDirection());
        self::assertSame(TextDirection::LTR, Script::Hans->textDirection());
        self::assertSame(TextDirection::LTR, Script::Jpan->textDirection());
        self::assertSame(TextDirection::LTR, Script::Kore->textDirection());
        self::assertSame(TextDirection::LTR, Script::Deva->textDirection());
        self::assertSame(TextDirection::LTR, Script::Thai->textDirection());
    }
}
