<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNe;

/**
 * Class LocaleNe - Nepali
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'नेपाली';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNe;
	}
}
