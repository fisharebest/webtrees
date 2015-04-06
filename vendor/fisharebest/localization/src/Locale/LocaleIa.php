<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIa;

/**
 * Class LocaleIa - Interlingua
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIa extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'interlingua';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'INTERLINGUA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIa;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
