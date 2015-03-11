<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEn - English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'English';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ENGLISH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEn;
	}
}
