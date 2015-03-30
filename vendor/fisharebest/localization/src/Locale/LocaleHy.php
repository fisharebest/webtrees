<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHy;

/**
 * Class LocaleHy - Armenian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHy extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'հայերեն';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ՀԱՅԵՐԵՆ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHy;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
