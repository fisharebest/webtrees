<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSo - Somali
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Soomaali';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SOOMAALI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSo;
	}
}
