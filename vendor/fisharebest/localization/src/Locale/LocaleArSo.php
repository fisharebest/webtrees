<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArSo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSo extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySo;
	}
}
