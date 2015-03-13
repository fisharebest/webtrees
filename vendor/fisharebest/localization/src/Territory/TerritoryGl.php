<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory GL - Greenland.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryGl extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'GL';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
