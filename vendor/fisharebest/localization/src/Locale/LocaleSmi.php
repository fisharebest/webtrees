<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmi;

/**
 * Class LocaleSmi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSmi extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'saami';
	}

	public function endonymSortable() {
		return 'SAAMI';
	}

	public function language() {
		return new LanguageSmi;
	}
}
