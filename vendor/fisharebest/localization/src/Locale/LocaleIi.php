<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIi;

/**
 * Class LocaleIi - Sichuan Yi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIi extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ꆈꌠꉙ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIi;
	}
}
