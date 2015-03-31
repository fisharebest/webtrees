<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LocaleHaLatnGh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHaLatnGh extends LocaleHaLatn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGh;
	}
}
