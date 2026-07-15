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

use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Module\CkeditorModule;

#[CoversClass(CkeditorModule::class)]
class CkeditorModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(CkeditorModule::class));
    }

    public function testCkeditorLanguageMapping(): void
    {
        $module = new CkeditorModule();

        $method = (new \ReflectionClass($module))->getMethod('ckeditorLanguage');

        self::assertSame('en', $method->invoke($module, 'en-US'));
        self::assertSame('en-gb', $method->invoke($module, 'en-GB'));
        self::assertSame('pt-br', $method->invoke($module, 'pt-BR'));
        self::assertSame('sr-latn', $method->invoke($module, 'sr-Latn'));
        self::assertSame('zh-cn', $method->invoke($module, 'zh-Hans'));
        self::assertSame('zh', $method->invoke($module, 'zh-Hant'));
        self::assertSame('no', $method->invoke($module, 'nn'));
        self::assertSame('en', $method->invoke($module, 'jv'));
    }
}
