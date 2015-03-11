<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory BR - Brazil.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBr extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'BR';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
