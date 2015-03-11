<?php namespace Fisharebest\Localization;

/**
 * Class LocaleHaw - Hawaiian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHaw extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ʻŌlelo Hawaiʻi';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'OLELO HAWAII';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHaw;
	}
}
