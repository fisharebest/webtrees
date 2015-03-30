<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLuy;

/**
 * Class LocaleLuy - Luyia
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLuy extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Luluhia';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LULUHIA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLuy;
	}
}
