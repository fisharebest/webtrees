<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYo;

/**
 * Class LocaleYo - Yoruba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Èdè Yorùbá';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'EDE YORUBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageYo;
	}
}
