<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory ZA - South Africa.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryZa extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'ZA';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
