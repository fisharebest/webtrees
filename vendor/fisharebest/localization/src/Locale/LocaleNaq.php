<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNaq - Nama
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNaq extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Khoekhoegowab';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KHOEKHOEGOWAB';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNaq;
	}
}
