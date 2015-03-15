<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnMu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMu extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMu;
	}
}
