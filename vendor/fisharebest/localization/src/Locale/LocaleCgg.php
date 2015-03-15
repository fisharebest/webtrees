<?php namespace Fisharebest\Localization;

/**
 * Class LocaleCgg - Chiga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCgg extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Rukiga';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'RUKIGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCgg;
	}
}
