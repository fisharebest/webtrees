<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSsy - Saho
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSsy extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Saho';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SAHO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSsy;
	}
}
