<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnCk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCk extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCk;
	}
}
