<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMs - Malay
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMs extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Bahasa Melayu';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'BAHASA MELAYU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMs;
	}
}
