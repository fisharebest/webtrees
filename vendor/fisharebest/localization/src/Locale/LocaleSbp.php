<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSbp;

/**
 * Class LocaleSbp - Sangu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSbp extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ishisangu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ISHISANGU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSbp;
	}
}
