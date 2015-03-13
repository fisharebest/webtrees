<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory ID - Indonesia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryId extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'ID';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
