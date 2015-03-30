<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEbu;

/**
 * Class LocaleEbu - Embu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEbu extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kĩembu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KIEMBU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEbu;
	}
}
