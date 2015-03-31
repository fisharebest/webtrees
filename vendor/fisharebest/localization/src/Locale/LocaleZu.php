<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageZu;

/**
 * Class LocaleZu - Zulu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZu extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'isiZulu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ISIZULU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageZu;
	}
}
