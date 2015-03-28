<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNyn;

/**
 * Class LocaleNyn - Nyankole
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNyn extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Runyankore';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'RUNYANKORE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNyn;
	}
}
