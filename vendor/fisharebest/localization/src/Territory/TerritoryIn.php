<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory IN - India.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryIn extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'IN';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 0;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 1;
	}
}
