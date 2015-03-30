<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMas;

/**
 * Class LocaleMas - Masai
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMas extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Maa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'MAA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMas;
	}
}
