<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSn;

/**
 * Class LocaleSn - Shona
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSn extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'chiShona';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'CHISHONA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSn;
	}
}
