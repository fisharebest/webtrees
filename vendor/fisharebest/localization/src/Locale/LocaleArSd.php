<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArSd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSd extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySd;
	}
}
