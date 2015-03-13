<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRuKg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuKg extends LocaleRu {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKg;
	}
}
