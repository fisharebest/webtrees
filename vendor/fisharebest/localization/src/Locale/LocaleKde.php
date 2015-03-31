<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKde;

/**
 * Class LocaleKde - Makonde
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKde extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Chimakonde';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'CHIMAKONDE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKde;
	}
}
