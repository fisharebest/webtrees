<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMer - Meru
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMer extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kĩmĩrũ';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KIMIRU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMer;
	}
}
