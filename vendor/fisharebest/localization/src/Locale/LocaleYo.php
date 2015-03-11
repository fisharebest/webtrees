<?php namespace Fisharebest\Localization;

/**
 * Class LocaleYo - Yoruba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Èdè Yorùbá';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'EDE YORUBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageYo;
	}
}
