<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBas;

/**
 * Class LocaleBas - Basaa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBas extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Ɓàsàa';
	}

	public function endonymSortable() {
		return 'BASAA';
	}

	public function language() {
		return new LanguageBas;
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
