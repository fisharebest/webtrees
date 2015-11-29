<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSr;

/**
 * Class LocaleSr - Serbian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSr extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'српски';
	}

	public function endonymSortable() {
		return 'СРПСКИ';
	}

	public function language() {
		return new LanguageSr;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
