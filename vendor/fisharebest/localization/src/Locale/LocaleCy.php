<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCy;

/**
 * Class LocaleCy - Welsh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCy extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Cymraeg';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'CYMRAEG';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCy;
	}
}
