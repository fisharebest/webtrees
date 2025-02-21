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

namespace Fisharebest\Webtrees\Cli\Commands;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

use function is_bool;
use function is_string;

abstract class AbstractCommand extends Command
{
    protected function boolOption(InputInterface $input, string $name): bool
    {
        $value = $input->getOption(name: $name);

        if ($value === null || is_bool($value)) {
            return (bool) $value;
        }

        throw new InvalidArgumentException(message: 'Argument must be bool : ' . $name);
    }

    protected function stringArgument(InputInterface $input, string $name): string
    {
        $value = $input->getArgument(name: $name);

        if ($value === null || is_string($value)) {
            return (string) $value;
        }

        throw new InvalidArgumentException(message: 'Argument must be string: ' . $name);
    }

    protected function stringOption(InputInterface $input, string $name): string
    {
        $value = $input->getOption(name: $name);

        if ($value === null || is_string($value)) {
            return (string) $value;
        }

        throw new InvalidArgumentException(message: 'Argument must be string : ' . $name);
    }
}
