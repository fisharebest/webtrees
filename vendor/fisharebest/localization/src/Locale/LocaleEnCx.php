<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnCx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCx extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCx;
	}
}
