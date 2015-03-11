<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory TT - Trinidad and Tobago.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryTt extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'TT';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
