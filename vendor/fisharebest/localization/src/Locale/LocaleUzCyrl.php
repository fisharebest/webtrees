<?php namespace Fisharebest\Localization;

/**
 * Class LocaleUzCyrl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUzCyrl extends LocaleUz {
	/** {@inheritdoc} */
	public function script() {
		return new ScriptCyrl;
	}
}
