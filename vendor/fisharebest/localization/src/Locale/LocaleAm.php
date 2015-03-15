<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAm - Amharic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAm extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'አማርኛ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAm;
	}
}
