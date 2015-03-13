<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArSy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSy extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySy;
	}
}
