<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMer;

/**
 * Class LocaleMer - Meru
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMer extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kĩmĩrũ';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KIMIRU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMer;
	}
}
