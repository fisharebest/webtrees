<?php namespace Fisharebest\Localization;

/**
 * Class LocaleIi - Sichuan Yi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIi extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ꆈꌠꉙ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIi;
	}
}
