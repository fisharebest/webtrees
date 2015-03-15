<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNe - Nepali
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNe extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'नेपाली';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNe;
	}
}
