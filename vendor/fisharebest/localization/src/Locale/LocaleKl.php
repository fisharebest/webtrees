<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKl;

/**
 * Class LocaleKl - Kalaallisut
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKl extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kalaallisut';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KALAALLISUT';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKl;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::DOT,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::HYPHEN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
