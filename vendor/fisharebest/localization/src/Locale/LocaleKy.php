<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKy;

/**
 * Class LocaleKy - Kyrgyz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKy extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'кыргызча';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'КЫРГЫЗЧА';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKy;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
