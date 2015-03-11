<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMas - Masai
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMas extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Maa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'MAA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMas;
	}
}
