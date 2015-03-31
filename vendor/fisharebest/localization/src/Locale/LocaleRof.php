<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRof;

/**
 * Class LocaleRof - Rombo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRof extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kihorombo';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KIHOROMBO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRof;
	}
}
