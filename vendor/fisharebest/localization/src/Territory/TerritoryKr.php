<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory KR - Republic of Korea.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryKr extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'KR';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
