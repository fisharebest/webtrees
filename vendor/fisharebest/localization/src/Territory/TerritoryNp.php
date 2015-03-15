<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory NP - Nepal.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryNp extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'NP';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
