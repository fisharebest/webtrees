<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageQu;

/**
 * Class LocaleQu - Quechua
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleQu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Runasimi';
	}

	public function endonymSortable() {
		return 'RUNASIMI';
	}

	public function language() {
		return new LanguageQu;
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
