<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBe;

/**
 * Class LocaleNlBe - Flemish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlBe extends LocaleNl {
	public function endonym() {
		return 'Vlaams';
	}

	public function endonymSortable() {
		return 'VLAAMS';
	}

	public function territory() {
		return new TerritoryBe;
	}
}
