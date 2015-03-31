<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptCyrl;

/**
 * Class LocaleBsCyrl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBsCyrl extends LocaleBs {
	/** {@inheritdoc} */
	public function script() {
		return new ScriptCyrl;
	}
}
