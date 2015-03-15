<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKi - Kikuyu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKi extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Gikuyu';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'GIKUYU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKi;
	}
}
