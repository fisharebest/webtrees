<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSn - Shona
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'chiShona';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'CHISHONA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSn;
	}
}
