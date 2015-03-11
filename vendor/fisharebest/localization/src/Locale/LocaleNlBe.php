<?php namespace Fisharebest\Localization;

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
	protected function endonymSortable() {
		return 'VLAAMS';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBe;
	}
}
