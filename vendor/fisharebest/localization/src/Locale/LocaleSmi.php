<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmi;

/**
 * Class LocaleSmi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSmi extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'saami';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SAAMI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSmi;
	}
}
