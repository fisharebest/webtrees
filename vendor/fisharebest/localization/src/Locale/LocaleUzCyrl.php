<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptCyrl;

/**
 * Class LocaleUzCyrl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUzCyrl extends LocaleUz {
	public function script() {
		return new ScriptCyrl;
	}
}
