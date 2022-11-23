<?php

namespace Fisharebest\Localization\Territory;

/**
 * Interface TerritoryInterface - Representation of a geographic area.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
interface TerritoryInterface
{
    /**
     * The ISO639 or M.49 code for this territory.
     *
     * @return string
     */
    public function code();

    /**
     * What is the first day of the week?
     * 0 = Sunday
     * 1 = Monday
     * etc.
     *
     * @return int
     */
    public function firstDay();

    /**
     * Does this territory prefer 'metric', 'UK' or 'US' measurements.
     *
     * @return string
     */
    public function measurementSystem();

    /**
     * Does this territory prefer 'A4' or 'US-Letter' paper.
     *
     * @return string
     */
    public function paperSize();

    /**
     * What is the first day of the weekend?
     * 0 = Sunday
     * 1 = Monday
     * etc.
     *
     * @return int
     */
    public function weekendStart();

    /**
     * What is the last day of the weekend?
     * 0 = Sunday
     * 1 = Monday
     * etc.
     *
     * @return int
     */
    public function weekendEnd();
}
