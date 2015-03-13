<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MT - Malta.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMt extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MT';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
