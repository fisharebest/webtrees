<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnCm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCm;
	}
}
