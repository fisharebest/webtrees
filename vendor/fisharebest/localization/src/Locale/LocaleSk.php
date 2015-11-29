<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSk;

/**
 * Class LocaleSk - Slovak
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSk extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'slovak_ci';
	}

	public function endonym() {
		return 'slovenčina';
	}

	public function endonymSortable() {
		return 'SLOVENCINA';
	}

	public function language() {
		return new LanguageSk;
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
