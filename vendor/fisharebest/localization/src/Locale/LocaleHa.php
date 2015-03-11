<?php namespace Fisharebest\Localization;

/**
 * Class LocaleHa - Hausa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Hausa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'HAUSA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHa;
	}
}
