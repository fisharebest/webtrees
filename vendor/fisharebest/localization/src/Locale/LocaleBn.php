<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBn;

/**
 * Class LocaleBn - Bengali
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBn extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'বাংলা';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBn;
	}
}
