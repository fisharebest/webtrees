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

namespace Fisharebest\Webtrees\Report;

use DomainException;

use function sprintf;

/**
 * Typed key/value collection for report variables.
 *
 * Reports expose a set of named string variables that originate from the
 * setup form (<Input>) and may be mutated at runtime by <SetVar>.
 */
final class VariableTable
{
    /**
     * @param array<string,string> $values Initial variable values
     */
    public function __construct(
        private array $values,
    ) {
    }

    public function has(string $name): bool
    {
        return isset($this->values[$name]);
    }

    /**
     * Return the value bound to $name or throw if no such variable exists.
     * Use {@see has()} when a missing key is a legitimate possibility.
     */
    public function get(string $name): string
    {
        if (!$this->has($name)) {
            throw new DomainException(sprintf('Undefined report variable: $%s', $name));
        }

        return $this->values[$name];
    }

    public function set(string $name, string $value): void
    {
        $this->values[$name] = $value;
    }
}
