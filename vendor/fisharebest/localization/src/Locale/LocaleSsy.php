<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSsy;

/**
 * Class LocaleSsy - Saho
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSsy extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Saho';
	}

	public function endonymSortable() {
		return 'SAHO';
	}

	public function language() {
		return new LanguageSsy;
	}
}
