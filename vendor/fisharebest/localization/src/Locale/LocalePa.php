<?php namespace Fisharebest\Localization;

/**
 * Class LocalePa - Punjabi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePa extends Locale {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'ਪੰਜਾਬੀ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguagePa;
	}
}
