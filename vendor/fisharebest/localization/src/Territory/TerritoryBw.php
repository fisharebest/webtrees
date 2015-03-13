<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory BW - Botswana.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBw extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'BW';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
