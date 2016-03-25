<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMero - Representation of the Meroitic Hieroglyphs script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMero extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Mero';
	}

	public function number() {
		return '100';
	}

	public function unicodeName() {
		return 'Meroitic_Hieroglyphs';
	}
}
