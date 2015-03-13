<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory VI - U.S. Virgin Islands.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryVi extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'VI';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
