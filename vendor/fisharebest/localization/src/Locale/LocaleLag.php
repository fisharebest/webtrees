<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLag - Langi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLag extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kɨlaangi';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KILAANGI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLag;
	}
}
