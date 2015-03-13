<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEe - Ewe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEe extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'eʋegbe';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'EWEGBE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEe;
	}
}
