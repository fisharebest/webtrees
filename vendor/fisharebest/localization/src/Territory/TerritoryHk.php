<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory HK - Hong Kong.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryHk extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'HK';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
