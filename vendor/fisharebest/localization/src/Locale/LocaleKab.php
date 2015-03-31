<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKab;

/**
 * Class LocaleKab - Kabyle
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKab extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Taqbaylit';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'TAQBAYLIT';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKab;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
