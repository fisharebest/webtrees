<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCgg;

/**
 * Class LocaleCgg - Chiga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCgg extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Rukiga';
	}

	public function endonymSortable() {
		return 'RUKIGA';
	}

	public function language() {
		return new LanguageCgg;
	}
}
