<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnSs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSs extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySs;
	}
}
