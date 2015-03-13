<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRoMd - Moldavian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRoMd extends LocaleRo {
	/** {@inheritdoc} */
	public function endonym() {
		return 'moldovenească';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'MOLDOVENEASCA';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMd;
	}
}
