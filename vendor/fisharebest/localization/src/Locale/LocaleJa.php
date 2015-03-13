<?php namespace Fisharebest\Localization;

/**
 * Class LocaleJa - Japanese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return '日本語';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageJa;
	}
}
