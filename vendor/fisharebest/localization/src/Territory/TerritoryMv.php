<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MV - Maldives.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMv extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MV';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 5;
	}
}
