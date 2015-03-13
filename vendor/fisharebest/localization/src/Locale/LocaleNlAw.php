<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNlAw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlAw extends LocaleNl {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAw;
	}
}
