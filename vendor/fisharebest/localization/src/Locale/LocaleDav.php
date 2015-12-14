<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDav;

/**
 * Class LocaleDav - Taita
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDav extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kitaita';
	}

	public function endonymSortable() {
		return 'KITAITA';
	}

	public function language() {
		return new LanguageDav;
	}
}
