<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDv - Divehi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMi extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Māori';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMi;
	}
}
