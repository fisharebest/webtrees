<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGuz;

/**
 * Class LocaleGuz - Gusii
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGuz extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Ekegusii';
	}

	public function endonymSortable() {
		return 'EKEGUSII';
	}

	public function language() {
		return new LanguageGuz;
	}
}
