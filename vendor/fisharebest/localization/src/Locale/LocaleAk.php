<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAk;

/**
 * Class LocaleAk - Akan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAk extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Akan';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'AKAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAk;
	}
}
