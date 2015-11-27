<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePap;

/**
 * Class LocalePap - Papiamentu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePap extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Papiamentu';
	}

	public function endonymSortable() {
		return 'PAPIAMENTU';
	}

	public function language() {
		return new LanguagePap;
	}
}
