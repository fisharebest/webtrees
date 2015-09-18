<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTk;

/**
 * Class LocaleTk - Turkmen
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTk extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Türkmençe';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'TURKMENCE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTk;
	}
}
