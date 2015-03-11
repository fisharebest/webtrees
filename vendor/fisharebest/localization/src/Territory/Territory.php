<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of a geographic area.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class Territory {
	/**
	 * The ISO639 or M.49 code for this territory.
	 *
	 * @return string
	 */
	abstract public function code();

	/**
	 * What is the first day of the week?
	 * 0 = Sunday
	 * 1 = Monday
	 * etc.
	 *
	 * @return integer
	 */
	public function firstDay() {
		return 1;
	}

	/**
	 * Does this territory use 'metric' or 'US' measurements.
	 *
	 * @return string
	 */
	public function measurementSystem() {
		return 'metric';
	}

	/**
	 * Does this territory use 'A4' or 'US-Letter' paper.
	 *
	 * @return string
	 */
	public function paperSize() {
		return 'A4';
	}

	/**
	 * What is the first day of the weekend?
	 * 0 = Sunday
	 * 1 = Monday
	 * etc.
	 *
	 * @return integer
	 */
	public function weekendStart() {
		return 6;
	}

	/**
	 * What is the last day of the weekend?
	 * 0 = Sunday
	 * 1 = Monday
	 * etc.
	 *
	 * @return integer
	 */
	public function weekendEnd() {
		return 0;
	}
}
