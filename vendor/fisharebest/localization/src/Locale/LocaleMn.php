<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMn - Mongolian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'монгол';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'МОНГОЛ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMn;
	}
}
