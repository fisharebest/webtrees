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
	/** {@inheritdoc} */
	public function collation() {
		return 'sinhala_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'සිංහල';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSi;
	}
}
