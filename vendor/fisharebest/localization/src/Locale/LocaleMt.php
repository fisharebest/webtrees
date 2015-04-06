<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMt;

/**
 * Class LocaleMt - Maltese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMt extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Malti';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'MALTI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMt;
	}
}
