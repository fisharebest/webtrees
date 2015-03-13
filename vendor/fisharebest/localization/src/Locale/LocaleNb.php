<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNb - Norwegian Bokmål
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNb extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'norsk bokmål';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'NORSK BOKMAL';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNb;
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
