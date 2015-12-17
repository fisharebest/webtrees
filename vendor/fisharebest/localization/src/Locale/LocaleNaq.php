<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNaq;

/**
 * Class LocaleNaq - Nama
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNaq extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Khoekhoegowab';
	}

	public function endonymSortable() {
		return 'KHOEKHOEGOWAB';
	}

	public function language() {
		return new LanguageNaq;
	}
}
