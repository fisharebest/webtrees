<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

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
	public function endonymSortable() {
		return 'TAMAZIGHT';
	}

	/** {@inheritdoc} */
	public function script() {
		return new ScriptLatn;
	}
}
