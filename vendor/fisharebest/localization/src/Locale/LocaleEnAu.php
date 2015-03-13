<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnAu - Australian English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnAu extends LocaleEn {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Australian English';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ENGLISH, AUSTRALIAN';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAu;
	}
}
