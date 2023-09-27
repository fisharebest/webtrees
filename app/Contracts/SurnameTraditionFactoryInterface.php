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

namespace Fisharebest\Webtrees\Contracts;

use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

/**
 * Create a surname tradition.
 */
interface SurnameTraditionFactoryInterface
{
    public const PATERNAL    = 'paternal';
    public const PATRILINEAL = 'patrilineal';
    public const MATRILINEAL = 'matrilineal';
    public const PORTUGUESE  = 'portuguese';
    public const SPANISH     = 'spanish';
    public const POLISH      = 'polish';
    public const LITHUANIAN  = 'lithuanian';
    public const ICELANDIC   = 'icelandic';
    public const DEFAULT     = '';

    /**
     * A list of supported surname traditions and their names.
     *
     * @return array<string,string>
     */
    public function list(): array;

    /**
     * Create a named surname tradition.
     *
     * @param string $name
     *
     * @return SurnameTraditionInterface
     */
    public function make(string $name): SurnameTraditionInterface;

    /**
     * @param string                    $name
     * @param SurnameTraditionInterface $surname_tradition
     *
     * @return void
     */
    public function register(string $name, SurnameTraditionInterface $surname_tradition): void;
}
