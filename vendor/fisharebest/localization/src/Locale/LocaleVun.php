<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVun;

/**
 * Class LocaleVun - Vunjo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVun extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kyivunjo';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KYIVUNJO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageVun;
	}
}
