<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMs;

/**
 * Class LocaleMs - Malay
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMs extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Bahasa Melayu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'BAHASA MELAYU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMs;
	}
}
