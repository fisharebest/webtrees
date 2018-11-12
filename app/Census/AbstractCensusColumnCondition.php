<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;

/**
 * Marital status.
 */
abstract class AbstractCensusColumnCondition extends AbstractCensusColumn implements CensusColumnInterface
{
    /** @var string Text to display for married males */
    protected $husband = '';

    /** @var string Text to display for married females */
    protected $wife = '';

    /** @var string Text to display for unmarried males */
    protected $bachelor = '';

    /** @var string Text to display for unmarried females */
    protected $spinster = '';

    /** @var string Text to display for male children */
    protected $boy = '';

    /** @var string Text to display for female children */
    protected $girl = '';

    /** @var string Text to display for divorced males */
    protected $divorce = '';

    /** @var string Text to display for divorced females */
    protected $divorcee = '';

    /** @var string Text to display for widowed males */
    protected $widower = '';

    /** @var string Text to display for widowed females */
    protected $widow = '';

    /** @var int At what age is this individual recorded as an adult */
    protected $age_adult = 15;

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
        $sex    = $individual->getSex();

        if ($family === null || count($family->facts('MARR')) === 0) {
            if ($this->isChild($individual)) {
                return $this->conditionChild($sex);
            }

            return $this->conditionSingle($sex);
        }

        if (count($family->facts('DIV')) > 0) {
            return $this->conditionDivorced($sex);
        }

        $spouse = $family->getSpouse($individual);
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
        $age = Date::getAgeYears($individual->getEstimatedBirthDate(), $this->date());

        return $age < $this->age_adult;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionChild($sex)
    {
        if ($sex === 'F') {
            return $this->girl;
        }

        return $this->boy;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionSingle($sex)
    {
        if ($sex === 'F') {
            return $this->spinster;
        }

        return $this->bachelor;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionDivorced($sex)
    {
        if ($sex === 'F') {
            return $this->divorcee;
        }

        return $this->divorce;
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
    private function conditionWidowed($sex)
    {
        if ($sex === 'F') {
            return $this->widow;
        }

        return $this->widower;
    }

    /**
     * How is this condition written in a census column.
     *
     * @param string $sex
     *
     * @return string
     */
    private function conditionMarried($sex)
    {
        if ($sex === 'F') {
            return $this->wife;
        }

        return $this->husband;
    }
}
