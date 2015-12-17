<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLu;

/**
 * Class LocaleLu - Luba-Katanga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Tshiluba';
	}

	public function endonymSortable() {
		return 'TSHILUBA';
	}

	public function language() {
		return new LanguageLu;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
