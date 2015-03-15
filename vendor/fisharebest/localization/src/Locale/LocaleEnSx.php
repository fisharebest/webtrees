<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnSx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSx extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySx;
	}
}
