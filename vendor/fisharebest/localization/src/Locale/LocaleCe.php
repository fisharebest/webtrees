<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCe;

/**
 * Class LocaleCe - Chechen
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCe extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'нохчийн';
	}

	public function endonymSortable() {
		return 'НОХЧИЙН';
	}

	public function language() {
		return new LanguageCe;
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
