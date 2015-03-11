<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArPs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArPs extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPs;
	}
}
