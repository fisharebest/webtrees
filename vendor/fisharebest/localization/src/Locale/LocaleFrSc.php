<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrSc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrSc extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySc;
	}
}
