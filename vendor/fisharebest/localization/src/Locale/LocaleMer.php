<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMer;

/**
 * Class LocaleMer - Meru
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMer extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kĩmĩrũ';
	}

	public function endonymSortable() {
		return 'KIMIRU';
	}

	public function language() {
		return new LanguageMer;
	}
}
