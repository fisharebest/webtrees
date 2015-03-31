<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMg;

/**
 * Class LocaleMg - Malagasy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Malagasy';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'MALAGASY';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMg;
	}
}
