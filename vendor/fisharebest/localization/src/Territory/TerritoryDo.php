<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory DO - Dominican Republic.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryDo extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'DO';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
