<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDyo;

/**
 * Class LocaleDyo - Jola-Fonyi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDyo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'joola';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'JOOLA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDyo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
