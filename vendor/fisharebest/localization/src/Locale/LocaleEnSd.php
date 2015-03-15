<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnSd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSd extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySd;
	}
}
