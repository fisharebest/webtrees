<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKam;

/**
 * Class LocaleKam - Kamba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKam extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kikamba';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KIKAMBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKam;
	}
}
