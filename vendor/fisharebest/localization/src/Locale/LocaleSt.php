<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSt;

/**
 * Class LocaleSt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSt extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Sesotho';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SESOTHO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSt;
	}
}
