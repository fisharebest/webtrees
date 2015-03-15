<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKde - Makonde
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKde extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Chimakonde';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'CHIMAKONDE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKde;
	}
}
