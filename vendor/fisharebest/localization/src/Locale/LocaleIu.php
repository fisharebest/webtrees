<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIu;

/**
 * Class LocaleIu - Inuktitut
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ᐃᓄᒃᑎᑐᑦ';
	}

	public function endonymSortable() {
		return 'ᐃᓄᒃᑎᑐᑦ';
	}

	public function language() {
		return new LanguageIu;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
