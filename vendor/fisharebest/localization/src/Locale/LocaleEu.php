<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEu;

/**
 * Class LocaleEu - Basque
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'euskara';
	}

	public function endonymSortable() {
		return 'EUSKARA';
	}

	public function language() {
		return new LanguageEu;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	protected function percentFormat() {
		return self::PERCENT . self::NBSP . '%s';
	}
}
