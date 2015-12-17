<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRu;

/**
 * Class LocaleRu - Russian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'русский';
	}

	public function endonymSortable() {
		return 'РУССКИЙ';
	}

	public function language() {
		return new LanguageRu;
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
