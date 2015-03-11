<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTe - Telugu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTe extends Locale {
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
