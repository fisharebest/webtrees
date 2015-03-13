<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAa - Afar
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Qafar';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'QAFAR';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAa;
	}
}
