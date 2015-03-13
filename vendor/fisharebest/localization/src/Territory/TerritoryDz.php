<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory DZ - Algeria.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryDz extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'DZ';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 6;
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 5;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 6;
	}
}
