<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory US - United States.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryUs extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'US';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function measurementSystem() {
		return 'US';
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
