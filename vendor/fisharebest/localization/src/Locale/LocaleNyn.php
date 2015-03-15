<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNyn - Nyankole
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNyn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Runyankore';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'RUNYANKORE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNyn;
	}
}
