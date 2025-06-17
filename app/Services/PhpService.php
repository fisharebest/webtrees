<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use RuntimeException;

use function extension_loaded;
use function function_exists;
use function ini_get;
use function ini_parse_quantity;
use function sys_get_temp_dir;

/**
 * Access to the PHP environment - to facilitate mocking/testing.
 */
class PhpService
{
    public function extensionLoaded(string $extension): bool
    {
        return extension_loaded(extension: $extension);
    }

    public function functionExists(string $function): bool
    {
        return function_exists(function: $function);
    }

    public function sysGetTempDir(): string
    {
        return sys_get_temp_dir();
    }

    public function displayErrors(): bool
    {
        return (bool) $this->iniGet(option: 'display_errors');
    }

    public function maxExecutionTime(): int
    {
        return (int) $this->iniGet(option: 'max_execution_time');
    }

    public function memoryLimit(): int
    {
        return ini_parse_quantity(shorthand: $this->iniGet(option: 'memory_limit'));
    }

    public function postMaxSize(): int
    {
        return ini_parse_quantity(shorthand: $this->iniGet(option: 'post_max_size'));
    }

    public function uploadMaxFilesize(): int
    {
        return ini_parse_quantity(shorthand: $this->iniGet(option: 'upload_max_filesize'));
    }

    public function iniGet(string $option): string
    {
        $value = ini_get(option: $option);

        if ($value === false) {
            throw new RuntimeException(message: 'Cannot read PHP configuration: ' . $option);
        }

        return $value;
    }
}
