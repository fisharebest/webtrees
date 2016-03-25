<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptThai - Representation of the Thai script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptThai extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Thai';
	}

	public function numerals() {
		return array('๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙');
	}

	public function number() {
		return '352';
	}

	public function unicodeName() {
		return 'Thai';
	}
}
