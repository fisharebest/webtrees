<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKi;

/**
 * Class LocaleKi - Kikuyu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKi extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Gikuyu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'GIKUYU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKi;
	}
}
