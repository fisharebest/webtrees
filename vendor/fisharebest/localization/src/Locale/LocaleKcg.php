<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKcg;

/**
 * Class LocaleKcg - Tyap
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKcg extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Tyap';
	}

	public function endonymSortable() {
		return 'TYAP';
	}

	public function language() {
		return new LanguageKcg;
	}
}
