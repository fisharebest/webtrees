<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTr;

/**
 * Class LocaleTr - Turkish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTr extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'turkish_ci';
	}

	public function endonym() {
		return 'Türkçe';
	}

	public function endonymSortable() {
		return 'TURKCE';
	}

	public function language() {
		return new LanguageTr;
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
