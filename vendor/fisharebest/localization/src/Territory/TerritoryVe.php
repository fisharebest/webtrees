<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory VE - Venezuela.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryVe extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'VE';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
