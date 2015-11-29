<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMo;

/**
 * Class LocaleIt - Italian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'limba moldovenească';
	}

	public function endonymSortable() {
		return 'LIMBA MOLDOVENEASCĂ';
	}

	public function language() {
		return new LanguageMo;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
