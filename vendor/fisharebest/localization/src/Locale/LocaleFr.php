<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFr;

/**
 * Class LocaleFr - French
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFr extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'français';
	}

	public function endonymSortable() {
		return 'FRANCAIS';
	}

	public function language() {
		return new LanguageFr;
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
