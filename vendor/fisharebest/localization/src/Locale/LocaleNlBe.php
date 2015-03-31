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
	/** {@inheritdoc} */
	public function endonym() {
		return 'Vlaams';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'VLAAMS';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBe;
	}
}
