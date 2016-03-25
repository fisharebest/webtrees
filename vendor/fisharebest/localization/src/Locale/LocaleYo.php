<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYo;

/**
 * Class LocaleYo - Yoruba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Èdè Yorùbá';
	}

	public function endonymSortable() {
		return 'EDE YORUBA';
	}

	public function language() {
		return new LanguageYo;
	}
}
