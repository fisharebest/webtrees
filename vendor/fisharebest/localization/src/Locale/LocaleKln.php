<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKln - Kalenjin
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKln extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kalenjin';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KALENJIN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKln;
	}
}
