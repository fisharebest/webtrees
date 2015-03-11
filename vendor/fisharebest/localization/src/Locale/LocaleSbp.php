<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSbp - Sangu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSbp extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ishisangu';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ISHISANGU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSbp;
	}
}
