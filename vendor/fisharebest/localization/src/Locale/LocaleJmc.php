<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJmc;

/**
 * Class LocaleJmc - Machame
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJmc extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kimachame';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KIMACHAME';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageJmc;
	}
}
