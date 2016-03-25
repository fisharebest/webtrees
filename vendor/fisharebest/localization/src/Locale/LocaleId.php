<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageId;

/**
 * Class LocaleId - Indonesian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleId extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Indonesia';
	}

	public function endonymSortable() {
		return 'INDONESIA';
	}

	public function language() {
		return new LanguageId;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
