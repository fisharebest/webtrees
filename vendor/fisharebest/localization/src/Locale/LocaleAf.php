<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAf;

/**
 * Class LocaleAf - Afrikaans
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAf extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Afrikaans';
	}

	public function endonymSortable() {
		return 'AFRIKAANS';
	}

	public function language() {
		return new LanguageAf;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
