<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory JM - Jamaica.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryJm extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'JM';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
