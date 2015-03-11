<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory SO - Somalia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySo extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'SO';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 6;
	}
}
