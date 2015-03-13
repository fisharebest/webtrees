<?php namespace Fisharebest\Localization;

/**
 * Class LocaleUg - Uyghur
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUg extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ئۇيغۇرچە';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageUg;
	}
}
