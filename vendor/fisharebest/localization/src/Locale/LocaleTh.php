<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTh - Thai
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTh extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ไทย';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTh;
	}
}
