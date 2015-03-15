<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MW - Malawi.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMw extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MW';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
