<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKm;

/**
 * Class LocaleKm - Khmer
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKm extends AbstractLocale implements LocaleInterface {
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
