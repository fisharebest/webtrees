<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEe;

/**
 * Class LocaleEe - Ewe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEe extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Eʋegbe';
	}

	public function endonymSortable() {
		return 'EWEGBE';
	}

	public function language() {
		return new LanguageEe;
	}
}
