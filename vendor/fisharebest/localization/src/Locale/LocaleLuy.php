<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLuy - Luyia
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLuy extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Luluhia';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'LULUHIA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLuy;
	}
}
