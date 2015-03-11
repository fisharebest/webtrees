<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMt - Maltese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMt extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Malti';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'MALTI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMt;
	}
}
