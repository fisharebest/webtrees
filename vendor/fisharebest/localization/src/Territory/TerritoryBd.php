<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory BD - Bangladesh.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBd extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'BD';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 5;
	}
}
