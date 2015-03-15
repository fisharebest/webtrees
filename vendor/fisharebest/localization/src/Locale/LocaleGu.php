<?php namespace Fisharebest\Localization;

/**
 * Class LocaleGu - Gujarati
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGu extends Locale {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'ગુજરાતી';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGu;
	}
}
