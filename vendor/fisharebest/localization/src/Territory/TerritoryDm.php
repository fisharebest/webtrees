<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory DM - Dominica.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryDm extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'DM';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
