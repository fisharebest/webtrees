<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNnh;

/**
 * Class LocaleNnh - Ngiemboon
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNnh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Shwóŋò ngiembɔɔn';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SHWONO NGIEMBOON';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNnh;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
