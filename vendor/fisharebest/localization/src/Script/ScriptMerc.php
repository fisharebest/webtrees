<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMerc - Representation of the Meroitic Cursive script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMerc extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Merc';
	}

	public function number() {
		return '101';
	}

	public function unicodeName() {
		return 'Meroitic_Cursive';
	}
}
