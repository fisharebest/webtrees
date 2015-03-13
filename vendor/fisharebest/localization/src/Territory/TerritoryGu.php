<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory GU - Guam.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryGu extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'GU';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
