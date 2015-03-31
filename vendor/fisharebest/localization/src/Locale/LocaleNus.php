<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNus;

/**
 * Class LocaleNus - Nuer
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNus extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Thok Nath';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'THOK NATH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNus;
	}
}
