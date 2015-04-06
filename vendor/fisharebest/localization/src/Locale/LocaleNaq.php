<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNaq;

/**
 * Class LocaleNaq - Nama
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNaq extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Khoekhoegowab';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KHOEKHOEGOWAB';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNaq;
	}
}
