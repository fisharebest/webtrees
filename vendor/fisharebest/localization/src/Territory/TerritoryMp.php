<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory MP - Northern Mariana Islands.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMp extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'MP';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
