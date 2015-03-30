<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUa;

/**
 * Class LocaleRuUa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuUa extends LocaleRu {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryUa;
	}
}
