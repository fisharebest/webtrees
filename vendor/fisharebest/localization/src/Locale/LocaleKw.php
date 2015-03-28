<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKw;

/**
 * Class LocaleKw - Cornish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKw extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kernewek';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KERNEWEK';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKw;
	}
}
