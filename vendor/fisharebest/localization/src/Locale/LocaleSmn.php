<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmn;

/**
 * Class LocaleSmn - Inari Sami
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSmn extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'anarâškielâ';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ANARASKIELA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSmn;
	}
}
