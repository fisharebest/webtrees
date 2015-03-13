<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLuo - Luo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLuo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Dholuo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'DHOLUO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLuo;
	}
}
