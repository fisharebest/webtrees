<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSi;

/**
 * Class LocaleSi - Sinhala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSi extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'sinhala_ci';
	}

	public function endonym() {
		return 'සිංහල';
	}

	public function language() {
		return new LanguageSi;
	}
}
