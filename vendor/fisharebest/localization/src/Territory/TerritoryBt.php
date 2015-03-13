<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory BT - Bhutan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBt extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'BT';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
