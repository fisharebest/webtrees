<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageXh;

/**
 * Class LocaleXh - Xhosa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleXh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Xhosa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'XHOSA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageXh;
	}
}
