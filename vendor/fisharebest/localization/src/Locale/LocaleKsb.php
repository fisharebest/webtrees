<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsb;

/**
 * Class LocaleKsb - Shambala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKsb extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kishambaa';
	}

	public function endonymSortable() {
		return 'KISHAMBAA';
	}

	public function language() {
		return new LanguageKsb;
	}
}
