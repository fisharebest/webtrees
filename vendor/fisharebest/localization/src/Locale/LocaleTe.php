<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTe;

/**
 * Class LocaleTe - Telugu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'తెలుగు';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTe;
	}
}
