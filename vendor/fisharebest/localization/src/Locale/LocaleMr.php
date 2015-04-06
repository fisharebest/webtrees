<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMr;

/**
 * Class LocaleMr - Marathi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'मराठी';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMr;
	}
}
