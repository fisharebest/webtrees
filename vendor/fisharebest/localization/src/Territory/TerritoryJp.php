<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory JP - Japan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryJp extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'JP';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
