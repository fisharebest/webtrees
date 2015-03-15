<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNus - Nuer
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNus extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Thok Nath';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'THOK NATH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNus;
	}
}
