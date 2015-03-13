<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRwk - Rwa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRwk extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kiruwa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIRUWA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRwk;
	}
}
