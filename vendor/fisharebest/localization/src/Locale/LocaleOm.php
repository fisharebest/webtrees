<?php namespace Fisharebest\Localization;

/**
 * Class LocaleOm - Oromo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOm extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Oromoo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'OROMOO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageOm;
	}
}
