<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAk - Akan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAk extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Akan';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'AKAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAk;
	}
}
