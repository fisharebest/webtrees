<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGv;

/**
 * Class LocaleGv - Manx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGv extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Gaelg';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'GAELG';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGv;
	}
}
