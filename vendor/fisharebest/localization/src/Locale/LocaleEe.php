<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEe;

/**
 * Class LocaleEe - Ewe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'eʋegbe';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'EWEGBE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEe;
	}
}
