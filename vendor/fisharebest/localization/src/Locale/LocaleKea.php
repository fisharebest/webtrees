<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKea;

/**
 * Class LocaleKea - Kabuverdianu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKea extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kabuverdianu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KABUVERDIANU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKea;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
