<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTeo - Teso
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTeo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kiteso';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KITESO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTeo;
	}
}
