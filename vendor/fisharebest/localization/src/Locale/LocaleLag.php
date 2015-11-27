<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLag;

/**
 * Class LocaleLag - Langi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLag extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kɨlaangi';
	}

	public function endonymSortable() {
		return 'KILAANGI';
	}

	public function language() {
		return new LanguageLag;
	}
}
