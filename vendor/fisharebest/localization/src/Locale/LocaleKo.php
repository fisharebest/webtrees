<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKo - Korean
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return '한국어';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKo;
	}
}
