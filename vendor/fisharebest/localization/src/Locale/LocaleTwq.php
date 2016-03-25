<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTwq;

/**
 * Class LocaleTwq - Tasawaq
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTwq extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Tasawaq senni';
	}

	public function endonymSortable() {
		return 'TASAWAQ SENNI';
	}

	public function language() {
		return new LanguageTwq;
	}

	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
