<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIg;

/**
 * Class LocaleIg - Igbo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIg extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Igbo';
	}

	public function endonymSortable() {
		return 'IGBO';
	}

	public function language() {
		return new LanguageIg;
	}
}
