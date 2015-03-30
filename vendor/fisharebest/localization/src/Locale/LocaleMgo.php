<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMgo;

/**
 * Class LocaleMgo - Metaʼ
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMgo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'metaʼ';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'META';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMgo;
	}
}
