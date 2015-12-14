<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEs;
use Fisharebest\Localization\Territory\TerritoryEs;

/**
 * Class LocaleEs - Spanish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEs extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'spanish_ci';
	}

	public function endonym() {
		return 'español';
	}

	public function endonymSortable() {
		return 'ESPANOL';
	}

	public function language() {
		return new LanguageEs;
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}

	public function territory() {
		return new TerritoryEs;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
