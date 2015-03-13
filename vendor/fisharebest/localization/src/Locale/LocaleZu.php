<?php namespace Fisharebest\Localization;

/**
 * Class LocaleZu - Zulu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZu extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'isiZulu';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ISIZULU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageZu;
	}
}
