<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKln;

/**
 * Class LocaleKln - Kalenjin
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKln extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kalenjin';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KALENJIN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKln;
	}
}
