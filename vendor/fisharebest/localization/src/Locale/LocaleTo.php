<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTo;

/**
 * Class LocaleTo - Tongan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'lea fakatonga';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LEA FAKATONGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTo;
	}
}
