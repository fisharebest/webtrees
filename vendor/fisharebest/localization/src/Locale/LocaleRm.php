<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRm;

/**
 * Class LocaleRm - Romansh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRm extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'rumantsch';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'RUMANTSCH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRm;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::APOSTROPHE,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
