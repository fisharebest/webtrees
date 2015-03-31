<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMoon - Representation of the Moon (Moon code, Moon script, Moon type) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMoon extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Moon';
	}

	/** {@inheritdoc} */
	public function number() {
		return '218';
	}
}
