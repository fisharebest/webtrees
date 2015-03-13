<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnSh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSh extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySh;
	}
}
