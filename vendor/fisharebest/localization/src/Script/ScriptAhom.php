<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAhom - Representation of the Ahom script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptAhom extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Ahom';
	}

	public function number() {
		return '338';
	}

	public function unicodeName() {
		return 'Ahom';
	}
}
