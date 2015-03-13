<?php namespace Fisharebest\Localization;

/**
 * Class LocaleGa - Irish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Gaeilge';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'GAEILGE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGa;
	}
}
