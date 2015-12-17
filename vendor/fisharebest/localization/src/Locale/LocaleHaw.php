<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHaw;

/**
 * Class LocaleHaw - Hawaiian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHaw extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ʻŌlelo Hawaiʻi';
	}

	public function endonymSortable() {
		return 'OLELO HAWAII';
	}

	public function language() {
		return new LanguageHaw;
	}
}
