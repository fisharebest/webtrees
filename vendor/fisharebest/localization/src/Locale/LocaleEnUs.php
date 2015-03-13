<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnUs - American English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnUs extends LocaleEn {
	/** {@inheritdoc} */
	public function endonym() {
		return 'American English';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ENGLISH, AMERICAN';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryUs;
	}
}
