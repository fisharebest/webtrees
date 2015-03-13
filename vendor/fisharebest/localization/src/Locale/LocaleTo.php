<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTo - Tongan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'lea fakatonga';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'LEA FAKATONGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTo;
	}
}
