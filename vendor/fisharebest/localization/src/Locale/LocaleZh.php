<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageZh;

/**
 * Class LocaleZh - Chinese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return '中文';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageZh;
	}
}
