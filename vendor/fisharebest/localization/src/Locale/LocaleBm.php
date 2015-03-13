<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBm - Bambara
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBm extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'bamanakan';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'BAMANAKAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBm;
	}
}
