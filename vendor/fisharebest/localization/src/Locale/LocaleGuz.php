<?php namespace Fisharebest\Localization;

/**
 * Class LocaleGuz - Gusii
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGuz extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ekegusii';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'EKEGUSII';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGuz;
	}
}
