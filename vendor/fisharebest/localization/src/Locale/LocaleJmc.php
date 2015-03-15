<?php namespace Fisharebest\Localization;

/**
 * Class LocaleJmc - Machame
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJmc extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kimachame';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIMACHAME';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageJmc;
	}
}
