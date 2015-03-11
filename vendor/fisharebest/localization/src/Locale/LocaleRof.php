<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRof - Rombo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRof extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kihorombo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIHOROMBO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRof;
	}
}
