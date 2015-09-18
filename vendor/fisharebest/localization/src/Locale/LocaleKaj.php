<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKaj;

/**
 * Class LocaleKaj - Jju
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKaj extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Jju';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'JJU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKaj;
	}
}
