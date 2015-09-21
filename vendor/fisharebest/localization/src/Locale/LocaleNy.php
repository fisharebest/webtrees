<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNy;

/**
 * Class LocaleNy - Chewa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNy extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Chichewa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'CHICHEWA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNy;
	}
}
