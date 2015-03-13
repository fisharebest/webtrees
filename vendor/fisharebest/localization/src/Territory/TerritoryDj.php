<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory DJ - Djibouti.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryDj extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'DJ';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 6;
	}
}
