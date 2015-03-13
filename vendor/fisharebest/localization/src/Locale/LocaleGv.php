<?php namespace Fisharebest\Localization;

/**
 * Class LocaleGv - Manx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGv extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Gaelg';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'GAELG';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGv;
	}
}
