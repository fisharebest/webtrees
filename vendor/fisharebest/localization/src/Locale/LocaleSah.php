<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSah;

/**
 * Class LocaleSah - Sakha
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSah extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'саха тыла';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'САХА ТЫЛА';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSah;
	}
}
