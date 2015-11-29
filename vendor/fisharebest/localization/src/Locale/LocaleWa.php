<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageWa;

/**
 * Class LocaleWa - Walloon
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleWa extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Walon';
	}

	public function endonymSortable() {
		return 'WALON';
	}

	public function language() {
		return new LanguageWa;
	}
}
