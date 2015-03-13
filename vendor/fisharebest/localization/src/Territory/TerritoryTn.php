<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory TN - Tunisia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryTn extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'TN';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 5;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 6;
	}
}
