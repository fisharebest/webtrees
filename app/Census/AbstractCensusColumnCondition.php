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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;

/**
 * Marital status.
 */
abstract class AbstractCensusColumnCondition extends AbstractCensusColumn implements CensusColumnInterface
{
    // Text to display for married males
    protected const string HUSBAND = '';

    // Text to display for married females
    protected const string WIFE = '';

    // Text to display for married unmarried males
    protected const string BACHELOR = '';

    // Text to display for married unmarried females
    protected const string SPINSTER = '';

    // Text to display for male children
    protected const string BOY = '';

    // Text to display for female children
    protected const string GIRL = '';

    // Text to display for divorced males
    protected const string DIVORCE = '';

    // Text to display for divorced females
    protected const string DIVORCEE = '';

    // Text to display for widowed males
    protected const string WIDOWER = '';

    // Text to display for widowed females
    protected const string WIDOW = '';

    // At what age is this individual recorded as an adult
    protected const int AGE_ADULT = 15;

    /**
     * Generate the likely value of this census column, based on available information.
     *
     * @param Individual $individual
     * @param Individual $head
     *
     * @return string
     */
    public function generate(Individual $individual, Individual $head): string
    {
        $family = $this->spouseFamily($individual);
        $sex    = $individual->sex();

        if ($family === null || $family->facts(['MARR'])->isEmpty()) {
            if ($this->isChild($individual)) {
                return $this->conditionChild($sex);
            }

            return $this->conditionSingle($sex);
        }

        if ($family->facts(['DIV'])->isNotEmpty()) {
            return $this->conditionDivorced($sex);
        }

        $spouse = $family->spouse($individual);
        if ($spouse instanceof Individual && $this->isDead($spouse)) {
            return $this->conditionWidowed($sex);
        }

        return $this->conditionMarried($sex);
    }

    /**
     * Is the individual a child.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    private function isChild(Individual $individual): bool
    {
        $age = new Age($individual->getEstimatedBirthDate(), $this->date());

        return $age->ageYears() < static::AGE_ADULT;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionChild(string $sex): string
    {
        if ($sex === 'F') {
            return static::GIRL;
        }

        return static::BOY;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionSingle(string $sex): string
    {
        if ($sex === 'F') {
            return static::SPINSTER;
        }

        return static::BACHELOR;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionDivorced(string $sex): string
    {
        if ($sex === 'F') {
            return static::DIVORCEE;
        }

        return static::DIVORCE;
    }

    /**
     * Is the individual dead.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    private function isDead(Individual $individual): bool
    {
        return $individual->getDeathDate()->isOK() && Date::compare($individual->getDeathDate(), $this->date()) < 0;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionWidowed(string $sex): string
    {
        if ($sex === 'F') {
            return static::WIDOW;
        }

        return static::WIDOWER;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionMarried(string $sex): string
    {
        if ($sex === 'F') {
            return static::WIFE;
        }

        return static::HUSBAND;
    }
}
