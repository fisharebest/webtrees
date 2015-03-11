<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory SG - Singapore.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySg extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'SG';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
