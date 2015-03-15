<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKw - Cornish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKw extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kernewek';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KERNEWEK';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKw;
	}
}
