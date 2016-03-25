<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCu;

/**
 * Class LocaleCu - Old Church Slavonic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'церковнослове́нскїй';
	}

	public function endonymSortable() {
		return 'ЦЕРКОВНОСЛОВЕ́НСКЇЙ';
	}

	public function language() {
		return new LanguageCu;
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
