<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKam - Kamba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKam extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kikamba';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIKAMBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKam;
	}
}
