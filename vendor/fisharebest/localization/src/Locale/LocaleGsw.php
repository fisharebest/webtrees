<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGsw;

/**
 * Class LocaleGsw - Swiss German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGsw extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Schwiizertüütsch';
	}

	public function endonymSortable() {
		return 'SCHWIIZERTUUTSCH';
	}

	public function language() {
		return new LanguageGsw;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::APOSTROPHE,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
