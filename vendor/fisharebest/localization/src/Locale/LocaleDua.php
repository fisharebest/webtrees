<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDua;

/**
 * Class LocaleDua - Duala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDua extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'duálá';
	}

	public function endonymSortable() {
		return 'DUALA';
	}

	public function language() {
		return new LanguageDua;
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
