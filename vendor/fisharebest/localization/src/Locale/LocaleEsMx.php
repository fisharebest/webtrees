<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMx;

/**
 * Class LocaleEsMx - Mexican Spanish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsMx extends LocaleEs {
	public function endonym() {
		return 'español de México';
	}

	public function endonymSortable() {
		return 'ESPANOL DE MEXICO';
	}

	protected function percentFormat() {
		return '%s' . self::PERCENT;
	}

	public function territory() {
		return new TerritoryMx;
	}
}
