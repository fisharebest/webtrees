<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function extension_loaded;
use function ini_parse_quantity;
use function sys_get_temp_dir;

#[CoversClass(PhpService::class)]
class PhpServiceTest extends TestCase
{
    public function testExtensionLoaded(): void
    {
        $php_service = new PhpService();

        self::assertSame(extension_loaded(extension: 'gd'), $php_service->extensionLoaded(extension: 'gd'));
        self::assertSame(extension_loaded(extension: 'foo'), $php_service->extensionLoaded(extension: 'foo'));
    }

    public function testSysGetTempDir(): void
    {
        $php_service = new PhpService();

        self::assertSame(sys_get_temp_dir(), $php_service->sysGetTempDir());
    }

    public function testIniGet(): void
    {
        $php_service = new PhpService();

        self::assertSame((bool) ini_get(option: 'display_errors'), $php_service->displayErrors());
        self::assertSame((int) ini_get(option: 'max_execution_time'), $php_service->maxExecutionTime());

        self::assertSame(
            ini_parse_quantity(shorthand: (string) ini_get(option: 'memory_limit')),
            $php_service->memoryLimit()
        );

        self::assertSame(
            ini_parse_quantity(shorthand: (string) ini_get(option: 'post_max_size')),
            $php_service->postMaxSize()
        );

        self::assertSame(
            ini_parse_quantity(shorthand: (string) ini_get(option: 'upload_max_filesize')),
            $php_service->uploadMaxFilesize()
        );
    }
}
