<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBs;

/**
 * Class LocaleBs - Bosnian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBs extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'bosanski';
	}

	public function endonymSortable() {
		return 'BOSANSKI';
	}

	public function language() {
		return new LanguageBs;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
