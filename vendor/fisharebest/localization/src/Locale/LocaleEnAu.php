<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAu;

/**
 * Class LocaleEnAu - Australian English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnAu extends LocaleEn {
	public function endonym() {
		return 'Australian English';
	}

	public function endonymSortable() {
		return 'ENGLISH, AUSTRALIAN';
	}

	public function territory() {
		return new TerritoryAu;
	}
}
