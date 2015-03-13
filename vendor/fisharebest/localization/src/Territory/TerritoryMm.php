<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MM - Myanmar.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMm extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MM';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function measurementSystem() {
		return 'US';
	}
}
