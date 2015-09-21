<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSms;

/**
 * Class LocaleSms
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSms extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'sääʹmǩiõll';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SAA MKIOLL';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSms;
	}
}
