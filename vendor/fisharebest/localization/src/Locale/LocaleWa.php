<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageWa;

/**
 * Class LocaleWa - Walloon
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleWa extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Walon';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'WALON';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageWa;
	}
}
