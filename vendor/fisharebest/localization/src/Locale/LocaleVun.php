<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVun;

/**
 * Class LocaleVun - Vunjo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVun extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kyivunjo';
	}

	public function endonymSortable() {
		return 'KYIVUNJO';
	}

	public function language() {
		return new LanguageVun;
	}
}
