<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnKi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKi extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKi;
	}
}
