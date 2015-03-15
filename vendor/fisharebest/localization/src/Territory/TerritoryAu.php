<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory AU - Australia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAu extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'AU';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
