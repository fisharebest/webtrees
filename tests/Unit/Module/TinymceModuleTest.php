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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Module\TinymceModule;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TinymceModule::class)]
class TinymceModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(TinymceModule::class));
    }

    public function testIsDisabledByDefault(): void
    {
        self::assertFalse((new TinymceModule())->isEnabledByDefault());
    }

    public function testTinymceLanguageMapping(): void
    {
        $module = new TinymceModule();

        $method = (new \ReflectionClass($module))->getMethod('tinymceLanguage');

        self::assertSame('en', $method->invoke($module, 'en-US'));
        self::assertSame('fr-FR', $method->invoke($module, 'fr-CA'));
        self::assertSame('pt-BR', $method->invoke($module, 'pt-BR'));
        self::assertSame('nb-NO', $method->invoke($module, 'nn'));
        self::assertSame('zh-CN', $method->invoke($module, 'zh-Hans'));
        self::assertSame('zh-TW', $method->invoke($module, 'zh-Hant'));
        self::assertSame('ka-GE', $method->invoke($module, 'ka'));
        self::assertSame('en', $method->invoke($module, 'mk'));
    }
}
