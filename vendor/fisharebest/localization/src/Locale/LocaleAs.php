<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAs - Assamese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAs extends Locale {
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
