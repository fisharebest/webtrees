<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory KH - Cambodia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryKh extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'KH';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
