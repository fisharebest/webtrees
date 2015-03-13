<?php namespace Fisharebest\Localization;

/**
 * Class LocaleZhHantTw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHantTw extends LocaleZhHant {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTw;
	}
}
