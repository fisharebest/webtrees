<?php namespace Fisharebest\Localization;

/**
 * Class LocaleVai - Vai
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVai extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ꕙꔤ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageVai;
	}
}
