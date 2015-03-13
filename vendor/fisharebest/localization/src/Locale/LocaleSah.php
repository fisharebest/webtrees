<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSah - Sakha
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSah extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'саха тыла';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'САХА ТЫЛА';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSah;
	}
}
