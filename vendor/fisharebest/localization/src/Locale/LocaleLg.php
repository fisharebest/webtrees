<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLg - Ganda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLg extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Luganda';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'LUGANDA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLg;
	}
}
