<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCy;

/**
 * Class LocaleCy - Welsh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCy extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Cymraeg';
	}

	public function endonymSortable() {
		return 'CYMRAEG';
	}

	public function language() {
		return new LanguageCy;
	}
}
