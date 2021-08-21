<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;

use function array_filter;
use function array_keys;
use function array_map;
use function implode;
use function in_array;

/**
 * All family members keep their original surname
 */
class DefaultSurnameTradition implements SurnameTraditionInterface
{
    /** Extract a GIVN from a NAME */
    protected const REGEX_GIVN = '~^(?<GIVN>[^/ ]+)~';

    /** Extract a SPFX and SURN from a NAME */
    protected const REGEX_SPFX_SURN = '~(?<NAME>/(?<SPFX>[a-z’\']{0,4}(?: [a-z’\']{1,4})*) ?(?<SURN>[^/]*)/)~';

    /** Extract a simple SURN from a NAME */
    protected const REGEX_SURN = '~(?<NAME>/(?<SURN>[^/]+)/)~';

    /** Extract two Spanish/Portuguese SURNs from a NAME */
    protected const REGEX_SURNS = '~/(?<SURN1>[^ /]+)(?: | y |/ /|/ y /)(?<SURN2>[^ /]+)/~';

    /**
     * Does this surname tradition change surname at marriage?
     *
     * @return bool
     */
    public function hasMarriedNames(): bool
    {
        return false;
    }

    /**
     * Does this surname tradition use surnames?
     *
     * @return bool
     */
    public function hasSurnames(): bool
    {
        return true;
    }

    /**
     * What name is given to a new child
     *
     * @param Individual|null $father
     * @param Individual|null $mother
     * @param string          $sex
     *
     * @return array<int,string>
     */
    public function newChildNames(?Individual $father, ?Individual $mother, string $sex): array
    {
        return [
            $this->buildName('//', ['TYPE' => 'birth']),
        ];
    }

    /**
     * What name is given to a new parent
     *
     * @param Individual $child
     * @param string     $sex
     *
     * @return array<int,string>
     */
    public function newParentNames(Individual $child, string $sex): array
    {
        return [
            $this->buildName('//', ['TYPE' => 'birth']),
        ];
    }

    /**
     * What names are given to a new spouse
     *
     * @param Individual $spouse
     * @param string     $sex
     *
     * @return array<int,string>
     */
    public function newSpouseNames(Individual $spouse, string $sex): array
    {
        return [
            $this->buildName('//', ['TYPE' => 'birth']),
        ];
    }

    /**
     * Build a GEDCOM name record
     *
     * @param string               $name
     * @param array<string,string> $parts
     *
     * @return string
     */
    protected function buildName(string $name, array $parts): string
    {
        $parts = array_filter($parts);

        $parts = array_map(
            fn (string $tag, string $value): string => "\n2 " . $tag . ' ' . $value,
            array_keys($parts),
            $parts
        );

        if ($name === '') {
            return '1 NAME' . implode($parts);
        }

        return '1 NAME ' . $name . implode($parts);
    }

    /**
     * Extract an individual's name.
     *
     * @param Individual|null $individual
     *
     * @return string
     */
    protected function extractName(?Individual $individual): string
    {
        if ($individual instanceof Individual) {
            $fact = $individual
                ->facts(['NAME'])
                ->first(fn (Fact $fact): bool => in_array($fact->attribute('TYPE'), ['', 'birth', 'change'], true));

            if ($fact instanceof Fact) {
                return $fact->value();
            }
        }

        return '';
    }
}
