<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageXog;

/**
 * Class LocaleXog - Soga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleXog extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Olusoga';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'OLUSOGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageXog;
	}
}
