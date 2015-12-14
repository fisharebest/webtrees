<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLkt;

/**
 * Class LocaleLkt - Lakota
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLkt extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Lakȟólʼiyapi';
	}

	public function endonymSortable() {
		return 'LAKHOLIYAPI';
	}

	public function language() {
		return new LanguageLkt;
	}
}
