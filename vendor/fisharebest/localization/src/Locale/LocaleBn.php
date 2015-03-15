<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBn - Bengali
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBn extends Locale {
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
