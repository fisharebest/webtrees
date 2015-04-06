<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLi;

/**
 * Class LocaleGswLi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGswLi extends LocaleGsw {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryLi;
	}
}
