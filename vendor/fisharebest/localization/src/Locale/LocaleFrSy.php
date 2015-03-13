<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrSy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrSy extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySy;
	}
}
