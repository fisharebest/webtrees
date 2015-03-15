<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory ZW - Zimbabwe.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryZw extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'ZW';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
