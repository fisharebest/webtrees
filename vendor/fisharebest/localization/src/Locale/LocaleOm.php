<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageOm;

/**
 * Class LocaleOm - Oromo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOm extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Oromoo';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'OROMOO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageOm;
	}
}
