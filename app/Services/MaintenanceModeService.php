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

use Fisharebest\Webtrees\Webtrees;
use InvalidArgumentException;

use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_file;
use function is_link;
use function is_readable;
use function is_string;
use function realpath;
use function rmdir;
use function unlink;

/**
 * Manage the site's online/offline status.
 */
readonly class MaintenanceModeService
{
    private const string OFFLINE_FILE = 'offline.txt';

    public function __construct(private string $data_dir = Webtrees::DATA_DIR)
    {
        if (!is_dir($data_dir)) {
            throw new InvalidArgumentException($data_dir . ' does not exist');
        }
    }

    public function file(): string
    {
        // Remove any '/../' from the path.
        return realpath($this->data_dir) . DIRECTORY_SEPARATOR . self::OFFLINE_FILE;
    }

    public function isOffline(): bool
    {
        $file = $this->file();

        return is_file($file) || is_link($file) || is_dir($file);
    }

    public function message(): string
    {
        $file = $this->file();

        if ($this->isOffline() && is_file($file) && is_readable($file)) {
            $message = file_get_contents($file);

            if (is_string($message)) {
                return $message;
            }
        }

        return '';
    }

    public function offline(string $message = ''): void
    {
        $this->online();

        file_put_contents($this->file(), $message);
    }

    public function online(): void
    {
        $file = $this->file();

        if (is_dir($file)) {
            rmdir($file);
        } elseif (is_link($file) || is_file($file)) {
            unlink($file);
        }
    }
}
