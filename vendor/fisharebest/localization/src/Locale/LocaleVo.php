<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVo;

/**
 * Class LocaleVo - Volapük
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Volapük';
	}

	public function endonymSortable() {
		return 'VOLAPUK';
	}

	public function language() {
		return new LanguageVo;
	}
}
