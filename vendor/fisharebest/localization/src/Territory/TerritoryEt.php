<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory ET - Ethiopia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryEt extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'ET';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
