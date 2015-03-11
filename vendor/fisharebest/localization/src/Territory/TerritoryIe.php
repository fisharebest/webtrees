<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory IE - Ireland.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryIe extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'IE';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
