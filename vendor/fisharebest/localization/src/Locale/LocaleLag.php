<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLag;

/**
 * Class LocaleLag - Langi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLag extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kɨlaangi';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KILAANGI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLag;
	}
}
