<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAs;

/**
 * Class LocaleAs - Assamese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAs extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'অসমীয়া';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAs;
	}
}
