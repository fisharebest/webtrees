<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSo;

/**
 * Class LocaleSo - Somali
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Soomaali';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SOOMAALI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSo;
	}
}
