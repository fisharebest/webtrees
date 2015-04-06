<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLg;

/**
 * Class LocaleLg - Ganda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Luganda';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LUGANDA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLg;
	}
}
