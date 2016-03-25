<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBez;

/**
 * Class LocaleBez - Bena
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBez extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Hibena';
	}

	public function endonymSortable() {
		return 'HIBENA';
	}

	public function language() {
		return new LanguageBez;
	}
}
