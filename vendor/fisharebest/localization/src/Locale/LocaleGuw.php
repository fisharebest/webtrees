<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGuw;

/**
 * Class LocaleFrBj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGuw extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Gun';
	}

	public function endonymSortable() {
		return 'GUN';
	}

	public function language() {
		return new LanguageGuw;
	}
}
