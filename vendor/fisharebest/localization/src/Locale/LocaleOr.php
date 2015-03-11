<?php namespace Fisharebest\Localization;

/**
 * Class LocaleOr - Oriya
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOr extends Locale {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'ଓଡ଼ିଆ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageOr;
	}
}
