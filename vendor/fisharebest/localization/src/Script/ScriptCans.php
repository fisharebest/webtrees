<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCans - Representation of the Unified Canadian Aboriginal Syllabics script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCans extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Cans';
	}

	public function number() {
		return '440';
	}

	public function unicodeName() {
		return 'Canadian_Aboriginal';
	}
}
