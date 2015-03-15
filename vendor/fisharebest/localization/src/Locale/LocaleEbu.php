<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEbu - Embu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEbu extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kĩembu';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIEMBU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEbu;
	}
}
