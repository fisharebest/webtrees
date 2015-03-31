<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHa;

/**
 * Class LocaleHa - Hausa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHa extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Hausa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'HAUSA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHa;
	}
}
