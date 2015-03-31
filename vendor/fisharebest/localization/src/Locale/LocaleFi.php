<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFi;

/**
 * Class LocaleFi - Finnish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFi extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'suomi';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SUOMI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageFi;
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
