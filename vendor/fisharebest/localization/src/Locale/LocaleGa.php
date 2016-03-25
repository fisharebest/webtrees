<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGa;

/**
 * Class LocaleGa - Irish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGa extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Gaeilge';
	}

	public function endonymSortable() {
		return 'GAEILGE';
	}

	public function language() {
		return new LanguageGa;
	}
}
