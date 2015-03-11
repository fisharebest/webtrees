<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnKe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKe extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKe;
	}
}
