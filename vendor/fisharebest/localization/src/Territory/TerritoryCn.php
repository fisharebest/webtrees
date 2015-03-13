<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory CN - China.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCn extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'CN';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
