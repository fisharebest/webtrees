<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMg - Malagasy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMg extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Malagasy';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'MALAGASY';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMg;
	}
}
