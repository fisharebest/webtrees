<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory PH - Philippines.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPh extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'PH';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
