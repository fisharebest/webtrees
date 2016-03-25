<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBe;

/**
 * Class LocaleBe - Belarusian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBe extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'беларуская';
	}

	public function endonymSortable() {
		return 'БЕЛАРУСКАЯ';
	}

	public function language() {
		return new LanguageBe;
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
