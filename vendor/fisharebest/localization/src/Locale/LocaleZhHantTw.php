<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTw;

/**
 * Class LocaleZhHantTw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHantTw extends LocaleZhHant {
	public function territory() {
		return new TerritoryTw;
	}
}
