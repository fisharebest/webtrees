<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageZgh;

/**
 * Class LocaleZgh - Standard Moroccan Tamazight
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZgh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ⵜⴰⵎⴰⵣⵉⵖⵜ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageZgh;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
