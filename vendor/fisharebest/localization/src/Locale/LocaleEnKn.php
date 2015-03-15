<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnKn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKn extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKn;
	}
}
