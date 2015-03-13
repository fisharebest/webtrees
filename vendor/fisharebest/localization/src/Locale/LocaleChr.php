<?php namespace Fisharebest\Localization;

/**
 * Class LocaleChr - Cherokee
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleChr extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ᏣᎳᎩ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageChr;
	}
}
