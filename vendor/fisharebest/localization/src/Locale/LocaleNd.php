<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNd - North Ndebele
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNd extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'isiNdebele';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ISINDEBELE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNd;
	}
}
