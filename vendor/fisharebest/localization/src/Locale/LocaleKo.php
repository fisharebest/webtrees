<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKo;

/**
 * Class LocaleKo - Korean
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return '한국어';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKo;
	}
}
