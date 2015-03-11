<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrCf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrCf extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCf;
	}
}
