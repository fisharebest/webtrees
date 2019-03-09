<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Individual;

/**
 * Marital status.
 */
abstract class AbstractCensusColumnCondition extends AbstractCensusColumn implements CensusColumnInterface
{
    /* Text to display for married males */
    protected $husband = '';

    /* Text to display for married females */
    protected $wife    = '';

    /* Text to display for unmarried males */
    protected $bachelor = '';

    /* Text to display for unmarried females */
    protected $spinster = '';

    /* Text to display for male children */
    protected $boy  = '';

    /* Text to display for female children */
    protected $girl = '';

    /* Text to display for divorced males */
    protected $divorce  = '';

    /* Text to display for divorced females */
    protected $divorcee = '';

    /* Text to display for widowed males */
    protected $widower = '';

    /* Text to display for widowed females */
    protected $widow   = '';

    /* At what age is this individual recorded as an adult */
    protected $age_adult = 15;

    /**
     * Generate the likely value of this census column, based on available information.
     *
     * @param Individual      $individual
     * @param Individual|null $head
     *
     * @return string
     */
    public function generate(Individual $individual, Individual $head = null)
    {
        $family = $this->spouseFamily($individual);
        $sex    = $individual->getSex();

        if ($family === null || count($family->getFacts('_NMR')) > 0) {
            if ($this->isChild($individual)) {
                return $this->conditionChild($sex);
            } else {
                return $this->conditionSingle($sex);
            }
        } elseif (count($family->getFacts('DIV')) > 0) {
            return $this->conditionDivorced($sex);
        } else {
            $spouse = $family->getSpouse($individual);
            if ($spouse instanceof Individual && $this->isDead($spouse)) {
                return $this->conditionWidowed($sex);
            } else {
                return $this->conditionMarried($sex);
            }
        }
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
        } else {
            return $this->boy;
        }
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
        } else {
            return $this->divorce;
        }
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
        } else {
            return $this->husband;
        }
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
        } else {
            return $this->bachelor;
        }
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
        } else {
            return $this->widower;
        }
    }

    /**
     * Is the individual a child.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    private function isChild(Individual $individual)
    {
        $age = (int) Date::getAge($individual->getEstimatedBirthDate(), $this->date(), 0);

        return $age < $this->age_adult;
    }

    /**
     * Is the individual dead.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    private function isDead(Individual $individual)
    {
        return $individual->getDeathDate()->isOK() && Date::compare($individual->getDeathDate(), $this->date()) < 0;
    }
}
