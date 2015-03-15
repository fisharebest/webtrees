<?php namespace Fisharebest\Localization;

/**
 * Class LocaleVun - Vunjo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVun extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kyivunjo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KYIVUNJO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageVun;
	}
}
