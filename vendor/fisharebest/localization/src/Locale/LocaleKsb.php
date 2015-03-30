<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsb;

/**
 * Class LocaleKsb - Shambala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKsb extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kishambaa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KISHAMBAA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKsb;
	}
}
