<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MO - Macao.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMo extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MO';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
