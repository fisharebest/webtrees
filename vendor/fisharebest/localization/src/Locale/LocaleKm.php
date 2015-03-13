<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKm - Khmer
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKm extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ខ្មែរ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKm;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
