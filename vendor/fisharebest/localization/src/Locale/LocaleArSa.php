<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArSa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSa extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySa;
	}
}
