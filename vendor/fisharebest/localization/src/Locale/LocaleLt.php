<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLt;

/**
 * Class LocaleLt - Lithuanian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLt extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'lithuanian_ci';
	}

	public function endonym() {
		return 'lietuvių';
	}

	public function endonymSortable() {
		return 'LIETUVIU';
	}

	public function language() {
		return new LanguageLt;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
