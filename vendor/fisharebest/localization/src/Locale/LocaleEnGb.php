<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGb - British English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGb extends LocaleEn {
	/** {@inheritdoc} */
	public function endonym() {
		return 'British English';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ENGLISH, BRITISH';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGb;
	}
}
