<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MH - Marshall Islands.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMh extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MH';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
