<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKa - Georgian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ქართული';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKa;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 2;
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
