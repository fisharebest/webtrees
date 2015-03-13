<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory IR - Islamic Republic of Iran.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryIr extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'IR';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 6;
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 5;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 5;
	}
}
