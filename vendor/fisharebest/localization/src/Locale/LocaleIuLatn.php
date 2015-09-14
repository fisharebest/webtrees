<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleIuLatn - Inuktitut
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIuLatn extends LocaleIu {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Inuktitut';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'INUKTITUT';
	}

	/** {@inheritdoc} */
	public function script() {
		return new ScriptLatn;
	}
}
