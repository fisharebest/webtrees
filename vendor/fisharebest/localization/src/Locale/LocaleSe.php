<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSe;

/**
 * Class LocaleSe - Northern Sami
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'davvisámegiella';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'DAVVISAMEGIELLA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSe;
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
