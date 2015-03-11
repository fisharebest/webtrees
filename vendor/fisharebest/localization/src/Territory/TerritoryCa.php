<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory CA - Canada.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCa extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'CA';
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
