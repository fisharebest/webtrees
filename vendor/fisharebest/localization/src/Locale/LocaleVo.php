<?php namespace Fisharebest\Localization;

/**
 * Class LocaleVo - Volapük
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Volapük';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'VOLAPUK';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageVo;
	}
}
