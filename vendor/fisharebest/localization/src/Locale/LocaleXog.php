<?php namespace Fisharebest\Localization;

/**
 * Class LocaleXog - Soga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleXog extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Olusoga';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'OLUSOGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageXog;
	}
}
