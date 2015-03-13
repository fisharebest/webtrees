<?php namespace Fisharebest\Localization;

/**
 * Class LocaleUzArabAf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUzArabAf extends LocaleUzArab {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAf;
	}
}
