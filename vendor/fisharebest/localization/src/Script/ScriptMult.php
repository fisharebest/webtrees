<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMult - Representation of the  Multani script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMult extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Mult';
	}

	public function number() {
		return '323';
	}

	public function unicodeName() {
		return 'Multani';
	}
}
