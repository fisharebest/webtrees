<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVo;

/**
 * Class LocaleVo - Volapük
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Volapük';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'VOLAPUK';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageVo;
	}
}
