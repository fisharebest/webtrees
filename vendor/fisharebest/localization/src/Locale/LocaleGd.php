<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGd;

/**
 * Class LocaleGd - Scottish Gaelic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGd extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Gàidhlig';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'GAIDHLIG';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGd;
	}
}
