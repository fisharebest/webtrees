<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrCm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrCm extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCm;
	}
}
