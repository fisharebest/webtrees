<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKu;

/**
 * Class LocaleKu - Kurdish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kurdî';
	}

	public function endonymSortable() {
		return 'KURDI';
	}

	public function language() {
		return new LanguageKu;
	}


	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	protected function percentFormat() {
		return self::PERCENT . '%s';
	}
}
