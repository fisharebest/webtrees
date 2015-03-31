<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsh;

/**
 * Class LocaleKsh - Colognian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKsh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kölsch';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KOLSCH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKsh;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
