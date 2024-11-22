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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Elements\NameType;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;
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
    protected const string REGEX_GIVN = '~^(?<GIVN>[^/ ]+)~';

    /** Extract a SPFX and SURN from a NAME */
    protected const string REGEX_SPFX_SURN = '~(?<NAME>/(?<SPFX>[a-z’\']{0,4}(?: [a-z’\']{1,4})*) ?(?<SURN>[^/]*)/)~';

    /** Extract a simple SURN from a NAME */
    protected const string REGEX_SURN = '~(?<NAME>/(?<SURN>[^/]+)/)~';

    /** Extract two Spanish/Portuguese SURNs from a NAME */
    protected const string REGEX_SURNS = '~/(?<SURN1>[^ /]+)(?: | y |/ /|/ y /)(?<SURN2>[^ /]+)/~';

    /**
     * The name of this surname tradition
     *
     * @return string
     */
    public function name(): string
    {
        return I18N::translateContext('Surname tradition', 'none');
    }

    /**
     * A short description of this surname tradition
     *
     * @return string
     */
    public function description(): string
    {
        return '';
    }

    /**
     * A default/empty name
     *
     * @return string
     */
    public function defaultName(): string
    {
        return '//';
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
    public function newChildNames(Individual|null $father, Individual|null $mother, string $sex): array
    {
        return [
            $this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH]),
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
            $this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH]),
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
            $this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH]),
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
            static fn (string $tag, string $value): string => "\n2 " . $tag . ' ' . $value,
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
    protected function extractName(Individual|null $individual): string
    {
        if ($individual instanceof Individual) {
            $fact = $individual
                ->facts(['NAME'])
                ->first(fn (Fact $fact): bool => in_array($fact->attribute('TYPE'), ['', NameType::VALUE_BIRTH, NameType::VALUE_CHANGE], true));

            if ($fact instanceof Fact) {
                return $fact->value();
            }
        }

        return '';
    }
}
