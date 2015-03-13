<?php namespace Fisharebest\Localization;

/**
 * Class LocaleCy - Welsh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCy extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Cymraeg';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'CYMRAEG';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCy;
	}
}
