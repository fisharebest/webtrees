<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MZ - Mozambique.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMz extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MZ';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
