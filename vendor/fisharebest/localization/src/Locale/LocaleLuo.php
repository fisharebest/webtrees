<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLuo;

/**
 * Class LocaleLuo - Luo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLuo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Dholuo';
	}

	public function endonymSortable() {
		return 'DHOLUO';
	}

	public function language() {
		return new LanguageLuo;
	}
}
