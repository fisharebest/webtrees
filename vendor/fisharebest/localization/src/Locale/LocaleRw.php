<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRw;

/**
 * Class LocaleRw - Kinyarwanda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRw extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kinyarwanda';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KINYARWANDA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRw;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
