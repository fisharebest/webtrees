<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleSrLatn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrLatn extends LocaleSr {
	/** {@inheritdoc} */
	public function endonym() {
		return 'srpski';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SRPSKI';
	}

	/** {@inheritdoc} */
	public function script() {
		return new ScriptLatn();
	}
}
