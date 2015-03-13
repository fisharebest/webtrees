<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDav - Taita
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDav extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kitaita';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KITAITA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDav;
	}
}
