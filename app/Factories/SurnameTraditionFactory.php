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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\SurnameTraditionFactoryInterface;
use Fisharebest\Webtrees\SurnameTradition\DefaultSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\IcelandicSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\MatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

/**
 * Create a surname tradition.
 */
class SurnameTraditionFactory implements SurnameTraditionFactoryInterface
{
    /** @var array<SurnameTraditionInterface> */
    private array $surname_traditions = [];

    /**
     * Register the surname traditions.
     */
    public function __construct()
    {
        $this->register(self::PATERNAL, new PaternalSurnameTradition());
        $this->register(self::PATRILINEAL, new PatrilinealSurnameTradition());
        $this->register(self::MATRILINEAL, new MatrilinealSurnameTradition());
        $this->register(self::PORTUGUESE, new PortugueseSurnameTradition());
        $this->register(self::SPANISH, new SpanishSurnameTradition());
        $this->register(self::POLISH, new PolishSurnameTradition());
        $this->register(self::LITHUANIAN, new LithuanianSurnameTradition());
        $this->register(self::ICELANDIC, new IcelandicSurnameTradition());
        $this->register(self::DEFAULT, new DefaultSurnameTradition());
    }

    /**
     * A list of supported surname traditions and their names.
     *
     * @return array<string,string>
     */
    public function list(): array
    {
        $fn = static fn (SurnameTraditionInterface $surname_tradition): string => $surname_tradition->name() . ' — ' . $surname_tradition->description();

        return array_map($fn, $this->surname_traditions);
    }

    /**
     * Create a named surname tradition.
     *
     * @param string $name
     *
     * @return SurnameTraditionInterface
     */
    public function make(string $name): SurnameTraditionInterface
    {
        return $this->surname_traditions[$name] ?? new DefaultSurnameTradition();
    }

    /**
     * @param string                    $name
     * @param SurnameTraditionInterface $surname_tradition
     *
     * @return void
     */
    public function register(string $name, SurnameTraditionInterface $surname_tradition): void
    {
        $this->surname_traditions[$name] = $surname_tradition;
    }
}
