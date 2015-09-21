<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSma;

/**
 * Class LocaleSma
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSma extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Åarjelsaemien gïele';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'AARJELSAMIEN GIELE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSma;
	}
}
