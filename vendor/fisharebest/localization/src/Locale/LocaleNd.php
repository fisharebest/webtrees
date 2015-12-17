<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNd;

/**
 * Class LocaleNd - North Ndebele
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNd extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'isiNdebele';
	}

	public function endonymSortable() {
		return 'ISINDEBELE';
	}

	public function language() {
		return new LanguageNd;
	}
}
