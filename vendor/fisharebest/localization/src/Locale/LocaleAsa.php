<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAsa - Asu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAsa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kipare';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIPARE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAsa;
	}
}
