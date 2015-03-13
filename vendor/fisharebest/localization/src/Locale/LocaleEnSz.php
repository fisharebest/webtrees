<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnSz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSz extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySz;
	}
}
