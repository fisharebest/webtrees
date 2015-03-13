<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSrLatn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrLatn extends LocaleSr {
	/** {@inheritdoc} */
	public function script() {
		return new ScriptLatn();
	}
}
