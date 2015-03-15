<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory AS - American Samoa.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAs extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'AS';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
