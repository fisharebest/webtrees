<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFil;

/**
 * Class LocaleFil - Filipino
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFil extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Filipino';
	}

	public function endonymSortable() {
		return 'FILIPINO';
	}

	public function language() {
		return new LanguageFil;
	}
}
