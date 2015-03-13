<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnPw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPw extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPw;
	}
}
