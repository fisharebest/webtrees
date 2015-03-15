<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory SV - El Salvador.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySv extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'SV';
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
