<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory IL - Israel.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryIl extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'IL';
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
