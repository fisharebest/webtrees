<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory LR - Liberia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryLr extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'LR';
	}

	/** {@inheritdoc} */
	public function measurementSystem() {
		return 'US';
	}
}
