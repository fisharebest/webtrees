<?php namespace Fisharebest\Localization\Territory;

/**
 * Interface TerritoryInterface - Representation of a geographic area.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
interface TerritoryInterface {
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
	 * @return integer
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
	 * @return integer
	 */
	public function weekendStart();

	/**
	 * What is the last day of the weekend?
	 * 0 = Sunday
	 * 1 = Monday
	 * etc.
	 *
	 * @return integer
	 */
	public function weekendEnd();
}
