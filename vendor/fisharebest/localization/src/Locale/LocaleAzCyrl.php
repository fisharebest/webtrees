<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAzCyrl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAzCyrl extends LocaleAz {
	/** {@inheritdoc} */
	public function script() {
		return new ScriptCyrl;
	}
}
