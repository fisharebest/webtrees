<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKsb - Shambala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKsb extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kishambaa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KISHAMBAA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKsb;
	}
}
