<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAst;

/**
 * Class LocaleAst - Asturian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAst extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'asturianu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ASTURIANU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAst;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
