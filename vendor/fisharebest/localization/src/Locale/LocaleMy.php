<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMy - Burmese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMy extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ဗမာ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMy;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 3;
	}
}
