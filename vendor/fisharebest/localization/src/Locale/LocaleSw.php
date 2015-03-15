<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSw - Swahili
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSw extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kiswahili';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KISWAHILI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSw;
	}
}
