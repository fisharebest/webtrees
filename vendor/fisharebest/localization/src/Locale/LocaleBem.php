<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBem - Bemba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBem extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ichibemba';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ICHIBEMBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBem;
	}
}
