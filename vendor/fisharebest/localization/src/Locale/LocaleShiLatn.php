<?php namespace Fisharebest\Localization;

/**
 * Class LocaleShiLatn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleShiLatn extends LocaleShi {
	/** {@inheritdoc} */
	public function endonym() {
		return 'tamazight';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'TAMAZIGHT';
	}

	/** {@inheritdoc} */
	public function script() {
		return new ScriptLatn;
	}
}
