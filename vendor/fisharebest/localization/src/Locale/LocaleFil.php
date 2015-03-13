<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFil - Filipino
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFil extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Filipino';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'FILIPINO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageFil;
	}
}
