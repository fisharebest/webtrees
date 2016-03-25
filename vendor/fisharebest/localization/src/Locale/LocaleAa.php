<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAa;

/**
 * Class LocaleAa - Afar
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAa extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Qafar';
	}

	public function endonymSortable() {
		return 'QAFAR';
	}

	public function language() {
		return new LanguageAa;
	}
}
