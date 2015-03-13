<?php namespace Fisharebest\Localization;

/**
 * Class LocaleZh - Chinese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZh extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return '中文';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageZh;
	}
}
