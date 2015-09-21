<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptCyrl;

/**
 * Class LocaleIt - Italian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMoCyrl extends LocaleMo {
	/** {@inheritdoc} */
	public function endonym() {
		return 'лимба молдовеняскэ';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ЛИМБА МОЛДОВЕНЯСКЭ';
	}

	/** {@inheritdoc} */
	public function script() {
		return new ScriptCyrl;
	}
}
