<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHira - Representation of the Hiragana script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHira extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Hira';
	}

	public function number() {
		return '410';
	}

	public function unicodeName() {
		return 'Hiragana';
	}
}
