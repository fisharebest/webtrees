<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory CR - Costa Rica.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCr extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'CR';
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
